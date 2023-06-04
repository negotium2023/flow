<?php

namespace App\Http\Controllers;

use App\Activity;
use App\BusinessUnits;
use App\Committee;
use App\Process;
use App\Project;
use App\RelatedParty;
use App\RelatedPartyProcess;
use App\TriggerType;
use Illuminate\Http\Request;
use App\Client;
use App\Step;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Document;
use App\Template;
use App\EmailTemplate;
use App\Log;
use App\ActivityLog;
use App\RelatedPartyTextData;
use App\RelatedPartyTextareaData;
use App\RelatedPartyBooleanData;
use App\RelatedPartyDateData;
use App\RelatedPartyNotificationData;
use App\RelatedPartyDocumentEmailData;
use App\RelatedPartyDocumentData;
use App\RelatedPartyTemplateEmailData;
use App\RelatedPartyMultipleAttachmentData;
use App\RelatedPartyDropdownItem;
use App\Notification;
use App\UserNotification;
use App\ActionableDocumentData;
use App\Config;
use App\Events\NotificationEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TemplateMail;
use App\RelatedPartyDropdownData;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\RelatedPartyComment;
use App\RelatedPartiesTree;
use App\Presentation;
use Anam\PhantomMagick\Converter;

class RelatedPartyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->progress_colours = [
            'not_started' => 'background-color: rgba(64,159,255, 0.15)',
            'started' => 'background-color: rgba(255,255,70, 0.15)',
            'progress_completed' => 'background-color: rgba(60,255,70, 0.15)',
        ];
    }

    public function related_index($client_id){

        $config = Config::first();

        $client = Client::withTrashed()->find($client_id);

        $client->load('referrer', 'office.area.region.division', 'users', 'comments.user');

        $steps = Step::where('process_id', $client->process_id)->orderBy('order', 'asc')->get();
        $c_step_order = Step::where('id', $client->step_id)->withTrashed()->first();

        $step_data = [];
        foreach ($steps as $a_step):
            if ($a_step->deleted_at == null) {
                $progress_color = $client->process->getStageHex(0);
                $step_stage = 0;

                if ($c_step_order->order == $a_step->order) {
                    $progress_color = $client->process->getStageHex(1);
                    $step_stage = 1;
                }

                if ($c_step_order->order > $a_step->order) {
                    $progress_color = $client->process->getStageHex(2);
                    $step_stage = 2;
                }

                $tmp_step = [
                    'id' => $a_step->id,
                    'name' => $a_step->name,
                    'progress_color' => $progress_color,
                    'process_id' => $a_step->process_id,
                    'order' => $a_step->order,
                    'stage' => $step_stage
                ];

                array_push($step_data, $tmp_step);
            }
        endforeach;

        $process_steps_with_names = null;
        $process_steps_by_order = null;
        if(isset($config->process_id)){
            $process_steps_with_names = Step::where('process_id', '=', $config->process_id)->orderBy('order')->pluck('name', 'id');
            $process_steps_by_order = Step::where('process_id', '=', $config->process_id)->orderBy('order')->pluck('id');
        }

        $related_parties = [];
        $this->getRelatedParties($client_id, $related_parties, 0);
        //dd($related_parties);

        //$orgo = $this->buildTree($related_parties,'parent_id','id');
        //dd($orgo);

        $process = Process::where('process_type_id', '=', 2)->orderBy('id', 'desc')->first();

        $max_step = Step::where('process_id', '=', isset($process->id)?$process->id:0)->orderBy('id', 'desc')->first();

        $project = Project::where('name','!=','')->whereNotNull('name')->get();
        $committee = Committee::where('name','!=','')->whereNotNull('name')->pluck('name','id')->prepend('Select','0');
//dd($related_parties);
        $parameters = [
            'client' => $client,
            'client_id' => $client->id,
            'r' => (isset($config)?Step::where('process_id',$config->related_party_process)->orderBy('order','asc')->take(1)->first()->id:0),
            'view_process_dropdown' => $client->clientProcessIfActivitiesExist(),
            'steps' => $step_data,
            'firststep' => 1,
            'process_steps_with_names' => ($process_steps_with_names != null ? $process_steps_with_names : ''),
            'process_steps_by_order' => ($process_steps_by_order != null ? $process_steps_by_order : ''),
            'related_parties' => $related_parties,
            'process_id' => isset($process->id)?$process->id:0,
            'max_step' => isset($max_step->id)?$max_step->id:0,
            'business_unit' => BusinessUnits::orderBy('id','asc')->pluck('name','id')->prepend('Please Select',''),
            'related_party_dropdown' => RelatedParty::where('client_id',$client_id)->orderBy('id','asc')->pluck('description','id')->prepend('Please Select',''),
            'projects'=>$project,
            'committees'=>$committee,
            'trigger_types' => TriggerType::orderBy('id','asc')->pluck('name','id')->prepend('Please Select',''),
            'committee'=>($client->committee_id > 0 ? $client->committee_id : 0),
            'project'=>($client->project_id > 0 ? $client->project->name : ''),
            'trigger_type'=>($client->trigger_type_id > 0 ? $client->trigger_type_id : ''),
            'casenr'=>(isset($client->case_number) ? $client->case_number : ''),
            'instruction_date'=>(isset($client->instruction_date) ? $client->instruction_date : '')

        ];
//dd($parameters);
        return view('relatedparties.related_party')->with($parameters);
    }

    public function organogram($client_id){
        $related_parties = [];
        $this->getRelatedParties2($client_id, $related_parties, 0);
        //dd($related_parties);

        $orgo = $this->buildTree($related_parties,'parent_id','id');

        $parameters = [
            'orgo' => $orgo,
            'client' => Client::where('id',$client_id)->first()->toArray()
        ];

        return view('partials.organogram')->with($parameters);
    }

    public function add($client_id, $related_party_id, $level_id){

        $client = Client::withTrashed()->find($client_id);

        $client->load('referrer', 'office.area.region.division', 'users', 'comments.user');

        $steps = Step::where('process_id', $client->process_id)->orderBy('order', 'asc')->get();
        $c_step_order = Step::where('id', $client->step_id)->withTrashed()->first();

        $step_data = [];
        foreach ($steps as $a_step):
            if ($a_step->deleted_at == null) {
                $progress_color = $client->process->getStageHex(0);

                if ($c_step_order->order == $a_step->order)
                    $progress_color = $client->process->getStageHex(1);

                if ($c_step_order->order > $a_step->order)
                    $progress_color = $client->process->getStageHex(2);

                $tmp_step = [
                    'id' => $a_step->id,
                    'name' => $a_step->name,
                    'progress_color' => $progress_color,
                    'process_id' => $a_step->process_id,
                    'order' => $a_step->order
                ];

                array_push($step_data, $tmp_step);
            }
        endforeach;

        $clients_drop_down = Client::select(DB::raw("CONCAT(first_name,' ',COALESCE(`last_name`,'')) AS full_name"), 'id')->orderBy('first_name')->orderBy('last_name')->pluck('full_name', 'id')->prepend('Select Client', '');
        $config = RelatedPartyProcess::first();

        $process = Process::where('process_type_id', '=', 2)->orderBy('id', 'desc')->first();

        //Next Level
        $next_step = Step::where('id', '>', $level_id)->where('process_id', '=', isset($process->id)?$process->id:0)->first();
        $this_level_id = isset($next_step->id)?$next_step->id:0;

        $max_step = Step::where('process_id', '=', isset($process->id)?$process->id:0)->orderBy('id', 'desc')->first();
        //$step_levels = Step::where('process_id', '=', $process->id)->pluck('id');

        if($level_id == 0){
            $first_step = Step::where('process_id', '=', isset($process->id)?$process->id:0)->orderBy('id')->first();
            $next_step = Step::where('id', '>', isset($first_step->id)?$first_step->id:0)->where('process_id', '=', isset($process->id)?$process->id:0)->first();
            $this_level_id = isset($next_step->id)?$next_step->id:0;
        }

        //dd($level_id);

        $step = Step::with('activities')->where('id', '=', $this_level_id)->first();

        $activities_array = [];
        $step_activities = isset($step)?$step->activities:[];
        foreach ($step_activities as $activity) {

            $activity_array = [
                'id' => $activity->id,
                'name' => $activity->name,
                'type' => $activity->getTypeName(),
                'client' => $activity->client_activity,
                'kpi' => $activity->kpi,
                'stage' => $activity->stage,
                'type_display' => $activity->actionable_type,
                'comment' => $activity->comment,
                'weight' => $activity->weight,
                'dropdown_item' => '',
                'dropdown_items' => [],
                'dropdown_values' => [],
                'is_dropdown_items_shown' => false,
                'max_step' => isset($max_step->id)?$max_step->id:0,
                'user' => $activity->user_id ?? 0
            ];

            if ($activity->getTypeName() == 'dropdown') {
                $activity_array['dropdown_items'] = $activity->actionable->items->pluck('name')->toArray();
            }

            array_push($activities_array, $activity_array);
        }

        //dd($activities_array);



        $process_activities_drop_down = Step::Where('process_id', '=', isset($process->id)?$process->id:0)->orderBy('order')->pluck('name', 'id')->prepend('Select Step', '');

        $parameters = [
            'client' => $client,
            'related_party_id' => $related_party_id,
            'level_id' => $level_id,
            'view_process_dropdown' => $client->clientProcessIfActivitiesExist(),
            'steps' => $step_data,
            'clients_drop_down' => $clients_drop_down,
            'process_activities_drop_down' => $process_activities_drop_down,
            'add_relative_party' => 'active',
            'step_level_id' => $this_level_id,//Fix
            'step' => $step,

            'step_dropdown' => Step::where('process_id',$client->process_id)->pluck('name','id'),
            'process' => Process::whereHas('steps')->orderBy('name','asc')->pluck('name','id')->prepend('Please Select','0'),
            'activities' => ($activities_array != null ? $activities_array : []),
            'users' => User::select(DB::raw("CONCAT(first_name,' ',COALESCE(`last_name`,'')) AS full_name"), 'id')->orderBy('first_name')->orderBy('last_name')->pluck('full_name', 'id'),
            'documents' => Document::orderBy('name')->where('client_id', $client->id)->orWhere('client_id', null)->pluck('name', 'id'),
            'document_options' => Document::orderBy('name')->pluck('name', 'id'),
            'templates' => Template::orderBy('name')->pluck('name', 'id'),
            'template_email_options' => EmailTemplate::orderBy('name')->pluck('name', 'id')
        ];

        return view('relatedparties.add')->with($parameters);

    }

    public function edit($process_id,$related_party_id){

        $related_party = RelatedParty::find($related_party_id);
        $related_party_min_step = Step::where('process_id',$process_id)->orderBy('order','asc')->first();
//dd($related_party_min_step);
        $client = Client::withTrashed()->find($related_party->client_id);

        $client->load('referrer', 'office.area.region.division', 'users', 'comments.user');

        $steps = Step::where('process_id', $client->process_id)->orderBy('order','asc')->get();

        $step_data = [];

        $c_step_order = Step::where('id', $client->step_id)->where('process_id', $client->process_id)->withTrashed()->first();

        foreach ($steps as $a_step) {

            $progress_color = $client->process->getStageHex(0);
            $step_stage = 0;

            if ($c_step_order["order"] == $a_step->order) {
                $progress_color = $client->process->getStageHex(1);
                $step_stage = 1;
            }

            if ($c_step_order["order"] > $a_step->order) {
                $progress_color = $client->process->getStageHex(2);
                $step_stage = 2;
            }

            /*if ($c_step_order["order"] == $a_step->order && $client->completed_at != null && $a_step->process_id == $client->process_id) {
                $progress_color = $client->process->getStageHex(2);
            }*/

            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'progress_color' => $progress_color,
                'process_id' => $a_step->process_id,
                'order' => $a_step->order,
                'stage' => $step_stage
            ];

            array_push($step_data, $tmp_step);
        }


        $process = Process::where('process_type_id', '=', 2)->orderBy('id', 'desc')->first();

        $max_step = Step::where('process_id', '=', $process->id)->orderBy('id', 'desc')->first();
        //$step_levels = Step::where('process_id', '=', $process->id)->pluck('id');

        $step = Step::with('activities.actionable.data')->where('process_id',$client->process_id)->first();
        $related_party_steps = Step::with('activities.actionable.data')->where('process_id',$process_id)->get();

        $related_party_step_data = [];

        foreach ($related_party_steps as $a_step) {
            $progress_color = $client->process->getStageHex(0);
            $step_stage2 = 0;

            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'progress_color' => $progress_color,
                'process_id' => $a_step->process_id,
                'order' => $a_step->order,
                'step2' => $step_stage2
            ];

            array_push($related_party_step_data, $tmp_step);
        }

        $process_activities_drop_down = Step::Where('process_id', '=', isset($process->id)?$process->id:0)->orderBy('order')->pluck('name', 'id')->prepend('Select Step', '');

        $project = Project::where('name','!=','')->whereNotNull('name')->get();
        $committees = Committee::where('name','!=','')->whereNotNull('name')->pluck('name','id')->prepend('Select','0');

        $parameters = [
            'client' => $client,
            'related_party_id' => $related_party->level_id,
            'view_process_dropdown' => $client->clientProcessIfActivitiesExist(),
            'steps' => $step_data,
            'process_activities_drop_down' => $process_activities_drop_down,
            'add_relative_party' => 'active',
            'step' => $step,
            'related_party_steps' => $related_party_step_data,
            'process_progress' => $client->getRelatedPartyProcessStepProgress(Step::find($related_party_min_step->id)),
            'related_party' => $related_party,
            'step_dropdown' => Step::where('process_id',$client->process_id)->pluck('name','id'),
            'process' => Process::whereHas('steps')->orderBy('name','asc')->pluck('name','id')->prepend('Please Select','0'),
            'users' => User::select(DB::raw("CONCAT(first_name,' ',COALESCE(`last_name`,'')) AS full_name"), 'id')->orderBy('first_name')->orderBy('last_name')->pluck('full_name', 'id'),
            'documents' => Document::orderBy('name')->where('client_id', $client->id)->orWhere('client_id', null)->pluck('name', 'id'),
            'document_options' => Document::orderBy('name')->pluck('name', 'id'),
            'templates' => Template::orderBy('name')->pluck('name', 'id'),
            'template_email_options' => EmailTemplate::orderBy('name')->pluck('name', 'id'),
            'max_group' => 1,
            'projects'=>$project,
            'committees'=>$committees,
            'trigger_types' => TriggerType::orderBy('id','asc')->pluck('name','id')->prepend('Please Select',''),
            'committee'=>($client->committee_id > 0 ? $client->committee_id : 0),
            'project'=>($client->project_id > 0 ? $client->project->name : ''),
            'trigger_type'=>($client->trigger_type_id > 0 ? $client->trigger_type_id : ''),
            'casenr'=>(isset($client->case_number) ? $client->case_number : ''),
            'instruction_date'=>(isset($client->instruction_date) ? $client->instruction_date : '')
        ];

        return view('relatedparties.edit')->with($parameters);

    }

    public function editRelatedParty($parent_id,$related_party){
        $rp = RelatedParty::where('id',$related_party)->first();

        $rpt = RelatedPartiesTree::where('related_party_id',$related_party)->get();

        foreach ($rpt as $rptt){
            $rpta[] = $rptt->related_party_parent_id;
        }

        $data = [
            'parent_ids' => $rpta,
            'description' => $rp->description,
            'firstname' => $rp->first_name,
            'lastname' => $rp->last_name,
            'initials' => $rp->initials,
            'idnumber' => $rp->id_number,
            'companyname' => $rp->company,
            'companyreg' => $rp->company_registration_number,
            'cifcode' => $rp->cif_code,
            'contact' => $rp->contact,
            'email' => $rp->email,
            'businessunit' => $rp->business_unit_id,
            'casenr' => $rp->case_number,
            'committee'=>($rp->committee_id > 0 ? $rp->committee_id : 0),
            'project'=>($rp->project_id > 0 ? $rp->project->name : ''),
            'trigger_type_id'=>($rp->trigger_type_id > 0 ? $rp->trigger_type_id : ''),
            'instruction_date' => $rp->instruction_date,
            'out_of_scope' => $rp->out_of_scope
        ];

        return response()->json($data);
    }

    public function updateRelatedParty(Request $request, $parent_id,$related_party){

        $config = Config::first();

        $rp = RelatedParty::find($related_party);

        $rp->description = $request->input('description');
        $rp->client_id = $request->has('client_id')?$request->input('client_id'):0;
        $rp->first_name = $request->input('firstname');
        $rp->last_name = $request->input('lastname');
        $rp->initials = $request->input('initials');
        $rp->id_number = $request->input('idnumber');
        $rp->email = $request->input('email');
        $rp->contact = $request->input('contact');
        $rp->save();

        RelatedParty::where('id',$related_party)->update([
            'hash_first_name' => DB::raw("AES_ENCRYPT('".addslashes($request->input('firstname'))."','Qwfe345dgfdg')"),
            'hash_last_name' => DB::raw("AES_ENCRYPT('".addslashes($request->input('lastname'))."','Qwfe345dgfdg')"),
            'hash_company' => DB::raw("AES_ENCRYPT('".addslashes($request->input('companyname'))."','Qwfe345dgfdg')"),
            'hash_id_number' => DB::raw("AES_ENCRYPT('".addslashes($request->input('idnumber'))."','Qwfe345dgfdg')"),
            'hash_cif_code' => DB::raw("AES_ENCRYPT('".addslashes($request->input('cif'))."','Qwfe345dgfdg')"),
            'hash_email' => DB::raw("AES_ENCRYPT('".addslashes($request->input('email'))."','Qwfe345dgfdg')"),
            'hash_contact' => DB::raw("AES_ENCRYPT('".addslashes($request->input('contact'))."','Qwfe345dgfdg')"),
            'hash_company_registration_number' => DB::raw("AES_ENCRYPT('".addslashes($request->input('companyreg'))."','Qwfe345dgfdg')")
        ]);


        return response()->json('success');
    }

    public function deleteRelatedParty($parent_id,$related_party){

        RelatedPartiesTree::where('related_party_parent_id',$parent_id)->where('related_party_id',$related_party)->delete();

        return response()->json('success');
    }

    public function checkRelatedPartyActivities($related_party){
        $completed = false;


        $related_partyobj = RelatedParty::where('id',$related_party)->first();

        $clientobj = Client::where('id',$related_partyobj->client_id)->first();

        $steps = Step::where('process_id',$related_partyobj->process_id)->get();

        $i = 0;
        foreach ($steps as $step){
            $stepd = Step::where('id',$step->id)->first();
            if($clientobj->isRelatedPartiesStepActivitiesCompleted($stepd,$related_party)){
                $i++;
            }
        }

        if(count($steps) == $i) {
            return response()->json(['message' => 'Success']);
        } else {
            return response()->json(['message' => 'Error']);
        }
    }
    public function completeRelatedParty($related_party){

        $rp = RelatedParty::find($related_party);
        $rp->completed_date = now();
        $rp->completed_by = Auth::id();
        $rp->completed = 1;
        $rp->save();

        return response()->json(['message'=>'Success']);
    }

    public function linkRelatedParty(Request $request,$related_party_parent_id,$related_party){

        RelatedPartiesTree::where('related_party_id', $related_party)->delete();
        if($request->has('related_party_parent_id') && is_array($request->input('related_party_parent_id'))) {
            $arr = explode(',', $request->input('old_related_party_id'));
            $diff = array_diff($arr, $request->input('related_party_parent_id'));
        } else {
            $diff = array("0");
        }
        //dd($diff);
        if(!empty($diff)){
            RelatedPartiesTree::where('related_party_id',$related_party)->whereIn('related_party_parent_id',$diff)->delete();
        }
        if($request->has('related_party_parent_id')) {
            foreach($request->input('related_party_parent_id') as $parent_id) {
                $rpt = new RelatedPartiesTree();
                $rpt->related_party_id = $request->input('related_party_id');
                $rpt->related_party_parent_id = $parent_id;
                $rpt->save();
            }
        }


        return response()->json("success");
    }

    public function manageRelatedParty(Request $request){
        $rpt = RelatedPartiesTree::where('related_party_id',$request->input('related_party_id'))->get();

        foreach ($rpt as $rptt){
            $rpta[] = $rptt->related_party_parent_id;
        }

        $data = [
            'parent_ids' => $rpta
        ];

        return response()->json($data);
    }

    public function destroy($clientid,$related_party){

        $related_partyd = RelatedParty::destroy($related_party);
        $related_party_tree = RelatedPartiesTree::where('related_party_id',$related_party)->delete();

        return redirect()->route( 'relatedparty.related_index',$clientid );

    }

    public function show($client_id,$related_party_id,$process_id,$step_id){

        $config = Config::first();

        $related_party = RelatedParty::find($related_party_id);

        $related_party_min_step = Step::where('process_id',$process_id)->orderBy('order','asc')->first();
        //dd($related_party_min_step);
        $client = Client::withTrashed()->find($client_id);

        $client->load('referrer', 'office.area.region.division', 'users', 'comments.user');

        if($related_party->viewed == 0 && $client->consultant_id == Auth::id()){
            $clientv = RelatedParty::find($related_party_id);
            $clientv->viewed = 1;
            $clientv->save();
        }

        $steps = Step::where('process_id', $client->process_id)->orderBy('order','asc')->get();

        $step_data = [];

        $c_step_order = Step::where('id', $client->step_id)->where('process_id', $client->process_id)->withTrashed()->first();

        foreach ($steps as $a_step) {

            $progress_color = $client->process->getStageHex(0);
            $step_stage = 0;

            if ($c_step_order["order"] == $a_step->order) {
                $progress_color = $client->process->getStageHex(1);
                $step_stage = 1;
            }

            if ($c_step_order["order"] > $a_step->order) {
                $progress_color = $client->process->getStageHex(2);
                $step_stage = 2;
            }

            /*if ($c_step_order["order"] == $a_step->order && $client->completed_at != null && $a_step->process_id == $client->process_id) {
                $progress_color = $client->process->getStageHex(2);
            }*/

            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'progress_color' => $progress_color,
                'process_id' => $a_step->process_id,
                'order' => $a_step->order,
                'stage' => $step_stage
            ];

            array_push($step_data, $tmp_step);
        }


        $process = Process::where('process_type_id', '=', 2)->orderBy('id', 'desc')->first();

        $max_step = Step::where('process_id', '=', $process->id)->orderBy('id', 'desc')->first();
        //$step_levels = Step::where('process_id', '=', $process->id)->pluck('id');

        $step = Step::with('activities.related_party.data')->where('process_id',$client->process_id)->first();
        $related_party_steps = Step::with(['activities.related_party.data'=>function($q) use ($related_party_id){
            $q->where('related_party_id',$related_party_id);
        }])->where('process_id',$process_id)->orderBy('order')->get();

        $related_party_step_data = [];

        $r_step_order = Step::where('id', $related_party->step_id)->where('process_id', $related_party->process_id)->withTrashed()->first();

        foreach ($related_party_steps as $a_step) {
            $progress_color = $client->process->getStageHex(0);
            $step_stage2 = 0;

            if ($r_step_order["order"] == $a_step->order) {
                $progress_color = $client->process->getStageHex(1);
                $step_stage2 = 1;
            }

            if ($r_step_order["order"] > $a_step->order) {
                $progress_color = $client->process->getStageHex(2);
                $step_stage2 = 2;
            }

            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'progress_color' => $progress_color,
                'process_id' => $a_step->process_id,
                'order' => $a_step->order,
                'stage2' => $step_stage2
            ];

            array_push($related_party_step_data, $tmp_step);
        }

        $process_activities_drop_down = Step::Where('process_id', '=', isset($process->id)?$process->id:0)->orderBy('order')->pluck('name', 'id')->prepend('Select Step', '');

        $process = Process::where('process_type_id', '=', 2)->orderBy('id', 'desc')->first();

        $max_step = Step::where('process_id', '=', isset($process->id)?$process->id:0)->orderBy('id', 'desc')->first();

        $related_parties = [];
        $this->getRelatedParties($client_id, $related_parties, 0);

        $project = Project::where('name','!=','')->whereNotNull('name')->get();
        $committee = Committee::where('name','!=','')->whereNotNull('name')->pluck('name','id')->prepend('Select','0');

        $parameters = [

            'client' => $client,
            'client_id' => $client->id,
            'related_party_id' => $related_party_id,
            'view_process_dropdown' => $client->clientProcessIfActivitiesExist(),
            'steps' => $step_data,
            'process_activities_drop_down' => $process_activities_drop_down,
            'add_relative_party' => 'active',
            'step' => $step,
            'related_party_steps' => $related_party_step_data,
            'process_progress' => $client->getRelatedPartyProcessStepProgress(($step_id == Step::find($related_party_min_step->id) ? Step::find($related_party_min_step->id) : Step::find($step_id)),$related_party_id),
            'related_party' => $related_party,
            'step_dropdown' => Step::where('process_id',$client->process_id)->pluck('name','id'),
            'process' => Process::whereHas('steps')->orderBy('name','asc')->pluck('name','id')->prepend('Please Select','0'),
            'users' => User::select(DB::raw("CONCAT(first_name,' ',COALESCE(`last_name`,'')) AS full_name"), 'id')->orderBy('first_name')->orderBy('last_name')->pluck('full_name', 'id'),
            'documents' => Document::orderBy('name')->where('client_id', $client->id)->orWhere('client_id', null)->pluck('name', 'id'),
            'document_options' => Document::orderBy('name')->pluck('name', 'id'),
            'templates' => Template::orderBy('name')->pluck('name', 'id'),
            'template_email_options' => EmailTemplate::orderBy('name')->pluck('name', 'id'),
            'related_parties' => $related_parties,
            'process_id' => isset($process->id)?$process->id:0,
            'max_step' => isset($max_step->id)?$max_step->id:0,
            'r' => (isset($config)?Step::where('process_id',$config->related_party_process)->orderBy('order','asc')->take(1)->first()->id:0),
            'business_unit' => BusinessUnits::orderBy('id','asc')->pluck('name','id')->prepend('Please Select',''),
            'projects'=>$project,
            'committees'=>$committee,
            'trigger_types' => TriggerType::orderBy('id','asc')->pluck('name','id')->prepend('Please Select',''),
            'committee'=>($client->committee_id > 0 ? $client->committee_id : 0),
            'project'=>($client->project_id > 0 ? $client->project->name : ''),
            'trigger_type'=>($client->trigger_type_id > 0 ? $client->trigger_type_id : ''),
            'casenr'=>(isset($client->case_number) ? $client->case_number : ''),
            'instruction_date'=>(isset($client->instruction_date) ? $client->instruction_date : '')
        ];

        return view('relatedparties.details')->with($parameters);

    }

    /*
    * @param client id
    * @param related party id
    * @returns related party relationship structure
    * Used to build and populate the related party view tree structure
    */
    public function getRelatedParties($client_id, &$related_parties, $related_party_parent_id){

        $related_party_tree = RelatedPartiesTree::with(['tree'=>function($q) use ($client_id){
            $q->where('client_id',$client_id);
        }])->get();

        foreach($related_party_tree as $rpt){
            foreach($rpt->tree as $tree) {
                $related_parties[$rpt->related_party_parent_id][] = ['id' => $rpt->id, 'related_party_id'=>$rpt->related_party_id,'parent_id' => $rpt->related_party_parent_id, 'description' => $tree->description,'name'=>($tree->company != null ? $tree->company : $tree->first_name.' '.$tree->last_name),'process_id'=>$tree->process_id];
            }
        }
    }

    /*
    * @param client id
    * @param related party id
    * @returns related party relationship structure
    * Used to build the related party tree structure for the orgonogram/treeview
    */
    public function getRelatedParties2($client_id, &$related_parties, $related_party_parent_id){

        $related_party_tree = RelatedPartiesTree::with(['tree'=>function($q) use ($client_id){
            $q->where('client_id',$client_id);
        }])->get();

        $exposure = array('176','189','194','195','196','197','280','281','282','283','284','285');

        foreach($related_party_tree as $rpt){
                foreach($rpt->tree as $tree) {
                /*$related_partys = RelatedParty::with('process.steps.activities.actionable.data')->where('id',$tree->id)->get();*/

                $activities = Activity::whereIn('id',$exposure)->get();

                /*foreach ($related_partys as $related_party) {*/
                    $cnt = 0;
                    foreach ($activities as $activity) {
                        if (isset($activity->actionable['data'][0])) {


                            //Date Request Received
                            if (in_array($activity->id,$exposure)) {
                                foreach ($activity->actionable['data'] as $data) {
                                    if ($data["related_party_id"] == $tree->id && $data["data"] != null) {
                                        $cnt++;
                                    }
                                }
                            }
                        }
                    }
                /*}*/

                $related_parties[] = ['exposure'=>(isset($cnt) ? $cnt : 0),'id' => $rpt->id, 'related_party_id' => $rpt->related_party_id, 'parent_id' => $rpt->related_party_parent_id, 'description' => $tree->description, 'process_id' => $tree->process_id, 'name'=>($tree->company != null ? $tree->company : $tree->first_name.' '.$tree->last_name) ,'company_registration_number'=>$tree->company_registration_number,'cif_code'=>$tree->cif_code,'id_number'=>$tree->id_number];
            }
        }
        //dd($this->buildTree($related_parties, 'parent_id', 'id'));
    }

    /*
    * @param client id
    * @param related party id
    * @returns related party relationship structure
    * Used to build and populate the related party view tree structure
    */
    function buildTree($flat, $pidKey, $idKey = null)
    {
        $grouped = array();
        foreach ($flat as $sub){
            $grouped[$sub[$pidKey]][] = $sub;
        }

        $fnBuilder = function($siblings) use (&$fnBuilder, $grouped, $idKey) {
            foreach ($siblings as $k => $sibling) {
                $id = $sibling[$idKey];
                if(isset($grouped[$id])) {
                    $sibling['children'] = $fnBuilder($grouped[$id]);
                }
                $siblings[$k] = $sibling;
            }

            return $siblings;
        };

        $tree = (isset($grouped[0]) ? $fnBuilder($grouped[0]) : []);

        return $tree;
    }

    public function progress(Client $client, RelatedParty $related_party)
    {
        $config = Config::first();

        $client->with('process.office.area.region.division');

        $related_party_min_step = Step::where('process_id',$related_party->process_id)->orderBy('order','asc')->first();

        $process = Process::where('process_type_id', '=', 2)->orderBy('id', 'desc')->first();

        $max_step = Step::where('process_id', '=', isset($process->id)?$process->id:0)->orderBy('id', 'desc')->first();

        $related_parties = [];
        $this->getRelatedParties($client->id, $related_parties, 0);

        $parameters = [
            'client' => $client,
            'process_progress' => $client->getProcessProgress(Step::find($related_party_min_step->id),$related_party->id),
            'steps' => Step::all(),
            'documents' => Document::orderBy('name')->where('client_id', $client->id)->orWhere('client_id', null)->pluck('name', 'id'),
            'templates' => Template::orderBy('name')->pluck('name', 'id'),
            'related_parties' => $related_parties,
            'process_id' => isset($process->id)?$process->id:0,
            'max_step' => isset($max_step->id)?$max_step->id:0,
            'r' => (isset($config)?Step::where('process_id',$config->related_party_process)->orderBy('order','asc')->take(1)->first()->id:0),
            'business_unit' => BusinessUnits::orderBy('id','asc')->pluck('name','id')->prepend('Please Select','')
        ];

        return view('relatedparties.progress')->with($parameters);
    }

    public function stepProgress($client_id,$process_id,$step_id,$related_party_id){

        $config = Config::first();

        $related_party = RelatedParty::find($related_party_id);
        $related_party_min_step = Step::where('process_id',$process_id)->orderBy('order','asc')->first();
        //dd($related_party_min_step);
        $client = Client::withTrashed()->find($related_party->client_id);

        $client->load('referrer', 'office.area.region.division', 'users', 'comments.user');

        $steps = Step::where('process_id', $client->process_id)->orderBy('order','asc')->get();

        $step_data = [];

        $c_step_order = Step::where('id', $client->step_id)->where('process_id', $client->process_id)->withTrashed()->first();

        foreach ($steps as $a_step) {

            $progress_color = $client->process->getStageHex(0);
            $step_stage = 0;

            if ($c_step_order["order"] == $a_step->order) {
                $progress_color = $client->process->getStageHex(1);
                $step_stage = 1;
            }

            if ($c_step_order["order"] > $a_step->order) {
                $progress_color = $client->process->getStageHex(2);
                $step_stage = 2;
            }

            /*if ($c_step_order["order"] == $a_step->order && $client->completed_at != null && $a_step->process_id == $client->process_id) {
                $progress_color = $client->process->getStageHex(2);
            }*/

            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'progress_color' => $progress_color,
                'process_id' => $a_step->process_id,
                'order' => $a_step->order,
                'stage' => $step_stage
            ];

            array_push($step_data, $tmp_step);
        }


        $process = Process::where('process_type_id', '=', 2)->orderBy('id', 'desc')->first();

        $max_step = Step::where('process_id', '=', $process->id)->orderBy('id', 'desc')->first();
        //$step_levels = Step::where('process_id', '=', $process->id)->pluck('id');

        $step = Step::with('activities.actionable.data')->where('process_id',$client->process_id)->first();
        $rstep = Step::with('activities.related_party.data')->where('process_id',$related_party->process_id)->orderBy('order','asc')->first();
        $related_party_steps = Step::with(['activities.related_party.data'=>function($q) use ($related_party_id){
            $q->where('related_party_id',$related_party_id);
        }])->where('process_id',$process_id)->orderBy('order')->get();

        $related_party_step_data = [];

        $r_step_order = Step::where('id', $related_party->step_id)->where('process_id', $related_party->process_id)->withTrashed()->first();

        foreach ($related_party_steps as $a_step) {
            $progress_color = $client->process->getStageHex(0);
            $step_stage2 = 0;

            if ($r_step_order["order"] == $a_step->order) {
                $progress_color = $client->process->getStageHex(1);
                $step_stage2 = 1;
            }

            if ($r_step_order["order"] > $a_step->order) {
                $progress_color = $client->process->getStageHex(2);
                $step_stage2 = 2;
            }

            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'progress_color' => $progress_color,
                'process_id' => $a_step->process_id,
                'order' => $a_step->order,
                'group' => $a_step->group,
                'stage2' => $step_stage2
            ];

            array_push($related_party_step_data, $tmp_step);
        }

        $process_activities_drop_down = Step::Where('process_id', '=', isset($process->id)?$process->id:0)->orderBy('order')->pluck('name', 'id')->prepend('Select Step', '');

        $process = Process::where('process_type_id', '=', 2)->orderBy('id', 'desc')->first();

        $max_step = Step::where('process_id', '=', isset($process->id)?$process->id:0)->orderBy('id', 'desc')->first();

        //get next step id where order == current order+1
        $n_step = Step::select('id')->orderBy('order','asc')->where('process_id', $process->id)->where('order','>',$rstep->order)->whereNull('deleted_at')->first();
        //dd($step->id);
        $next_step = $step->id;
        //dd($step->id);
        if($next_step == $max_step->id)
            $next_step = $max_step->id;
        else
            $next_step = (isset($n_step->id) ? $n_step->id : $step->id);
        //$next_step = $step->id + 1;

        $related_party->groupCompletedActivities(Step::find($step_id),$client_id,$related_party_id);

        $related_parties = [];
        $this->getRelatedParties($client_id, $related_parties, 0);

        $project = Project::where('name','!=','')->whereNotNull('name')->get();
        $committee = Committee::where('name','!=','')->whereNotNull('name')->pluck('name','id')->prepend('Select','0');

        $qa_complete = '';

        if($client->qa_end_date != null){
            //$qa_complete = 'disabled';
        }

        if($client->is_qa == 1 && Auth::user()->is('consultant')){
            $qa_complete = 'disabled';
            if(Auth::user()->is('qa')){
                $qa_complete = '';
            }
        }

        $parameters = [
            'client' => $client,
            'client_id' => $client->id,
            'related_party_id' => $related_party_id,
            'view_process_dropdown' => $client->clientProcessIfActivitiesExist(),
            'steps' => $step_data,
            'rsteps' => $related_party_step_data,
            'process_activities_drop_down' => $process_activities_drop_down,
            'add_relative_party' => 'active',
            'step' => $step,
            'rstep' => $rstep,
            'active' => $rstep,
            'related_party_steps' => $related_party_step_data,
            'process_progress' => $client->getRelatedPartyProcessStepProgress(($step_id == Step::find($related_party_min_step->id) ? Step::find($related_party_min_step->id) : Step::find($step_id)),$related_party_id),
            'related_party' => $related_party,
            'step_dropdown' => Step::where('process_id',$client->process_id)->pluck('name','id'),
            'process' => Process::whereHas('steps')->orderBy('name','asc')->pluck('name','id')->prepend('Please Select','0'),
            'users' => User::select(DB::raw("CONCAT(first_name,' ',COALESCE(`last_name`,'')) AS full_name"), 'id')->orderBy('first_name')->orderBy('last_name')->pluck('full_name', 'id'),
            'documents' => Document::orderBy('name')->where('client_id', $client->id)->orWhere('client_id', null)->pluck('name', 'id'),
            'document_options' => Document::orderBy('name')->where('client_id', $client->id)->where('related_party_id', $related_party_id)->orWhere('client_id', null)->pluck('name', 'id'),
            'templates' => Template::orderBy('name')->pluck('name', 'id'),
            'template_email_options' => EmailTemplate::orderBy('name')->pluck('name', 'id'),
            'related_parties' => $related_parties,
            'process_id' => isset($process->id)?$process->id:0,
            'max_step' => isset($max_step->id)?$max_step->id:0,
            'r' => (isset($config)?Step::where('process_id',$config->related_party_process)->orderBy('order','asc')->take(1)->first()->id:0),
            'max_group' => ($related_party->groupCompletedActivities(Step::find($step_id),$client_id,$related_party_id) > 0 ? $related_party->groupCompletedActivities(Step::find($step_id),$client_id,$related_party_id) :1),
            'next_step' => $next_step,
            'business_unit' => BusinessUnits::orderBy('id','asc')->pluck('name','id')->prepend('Please Select',''),
            'qa_complete'=>$qa_complete,
            'projects'=>$project,
            'committees'=>$committee,'trigger_types' => TriggerType::orderBy('id','asc')->pluck('name','id')->prepend('Please Select',''),
            'committee'=>($client->committee_id > 0 ? $client->committee_id : 0),
            'project'=>($client->project_id > 0 ? $client->project->name : ''),
            'trigger_type'=>($client->trigger_type_id > 0 ? $client->trigger_type_id : ''),
            'casenr'=>(isset($client->case_number) ? $client->case_number : ''),
            'instruction_date'=>(isset($client->instruction_date) ? $client->instruction_date : '')
        ];

        return view('relatedparties.stepprogress')->with($parameters);

    }

    public function store(Request $request){

        $config = Config::first();

        $related_party = new RelatedParty();
        $related_party->description = $request->input('description');
        $related_party->client_id = $request->has('client_id')?$request->input('client_id'):0;
        $related_party->related_party_parent_id = $request->has('related_party_id')?$request->input('related_party_id'):0;
        $related_party->first_name = $request->input('firstname');
        $related_party->last_name = $request->input('lastname');
        $related_party->initials = $request->input('initials');
        $related_party->id_number = $request->input('idnumber');
        $related_party->email = $request->input('email');
        $related_party->contact = $request->input('contact');
        $related_party->introducer_id = Auth::id();
        $related_party->office_id = auth()->user()->office()->id ?? 1;
        $related_party->process_id = $config->related_party_process;
        $related_party->step_id = Step::where('process_id',$config->related_party_process)->orderBy('order','asc')->first()->id;
        $related_party->save();

        RelatedParty::where('id',$related_party->id)->update([
            'hash_first_name' => DB::raw("AES_ENCRYPT('".addslashes($request->input('firstname'))."','Qwfe345dgfdg')"),
            'hash_last_name' => DB::raw("AES_ENCRYPT('".addslashes($request->input('lastname'))."','Qwfe345dgfdg')"),
            'hash_id_number' => DB::raw("AES_ENCRYPT('".addslashes($request->input('idnumber'))."','Qwfe345dgfdg')"),
            'hash_email' => DB::raw("AES_ENCRYPT('".addslashes($request->input('email'))."','Qwfe345dgfdg')"),
            'hash_contact' => DB::raw("AES_ENCRYPT('".addslashes($request->input('contact'))."','Qwfe345dgfdg')")
        ]);

        if($request->has('related_party_parent_id')) {
            foreach($request->input('related_party_parent_id') as $parent_id) {
                $rpt = new RelatedPartiesTree();
                $rpt->related_party_id = $related_party->id;
                $rpt->related_party_parent_id = $parent_id;
                $rpt->save();
            }
        }


        return response()->json("success");
    }

    public function update($related_party_id, Request $request){
//dd($request);
        $client = Client::find($request->has('client_id')?$request->input('client_id'):0);

        $related_party = RelatedParty::find($related_party_id);

        if($request->has('step_id') && $request->input('step_id') != ''){
            $log = new Log;
            $log->client_id = $client->id;
            $log->related_party_id = $related_party_id;
            $log->user_id = auth()->id();
            $log->save();

            $id = $request->has('client_id')?$request->input('client_id'):$client->id;
            $step = Step::find($request->input('step_id'));
            $step->load(['activities.actionable.data' => function ($query) use ($id,$related_party_id) {
                $query->where('client_id', $id)->where('related_party_id',$related_party_id);
            }]);
//dd($step);
            $all_activities_completed = false;
            foreach ($step->activities as $activity) {
                if(is_null($request->input($activity->id))){
                    if($request->input('old_'.$activity->id) != $request->input($activity->id)){
                        if(is_array($request->input($activity->id))){

                            $old = explode(',',$request->input('old_'.$activity->id));
                            $diff = array_diff($old,$request->input($activity->id));
                            //dd($diff);

                            foreach($request->input($activity->id) as $key => $value) {
                                $activity_log = new ActivityLog;
                                $activity_log->log_id = $log->id;
                                $activity_log->activity_id = $activity->id;
                                $activity_log->activity_name = $activity->name;
                                $activity_log->old_value = $request->input('old_' . $activity->id);
                                $activity_log->new_value = $value;
                                $activity_log->save();
                            }
                        } else {
                            $old = $request->input('old_'.$activity->id);

                            $activity_log = new ActivityLog;
                            $activity_log->log_id = $log->id;
                            $activity_log->activity_id = $activity->id;
                            $activity_log->activity_name = $activity->name;
                            $activity_log->old_value = $request->input('old_'.$activity->id);
                            $activity_log->new_value = $request->input($activity->id);
                            $activity_log->save();
                        }

                        switch ($activity->actionable_type) {
                            case 'App\RelatedPartyText':
                                RelatedPartyTextData::where('related_party_text_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            case 'App\RelatedPartyTextarea':
                                RelatedPartyTextareaData::where('related_party_textarea_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            case 'App\RelatedPartyDropdown':
                                RelatedPartyDropdownData::where('related_party_dropdown_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            case 'App\RelatedPartyBoolean':
                                RelatedPartyBooleanData::where('related_party_boolean_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            case 'App\RelatedPartyDate':
                                RelatedPartyDateData::where('related_party_date_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            default:
                                //todo capture defaults
                                break;
                        }
                    }
                }

                if ($request->has($activity->id) && !is_null($request->input($activity->id))) {
                    //If value did not change, do not save it again or add it to log
                    if($request->input('old_'.$activity->id) == $request->input($activity->id)){
                        continue;
                    }
                    if(is_array($request->input($activity->id))){

                        $old = explode(',',$request->input('old_'.$activity->id));
                        $diff = array_diff($old,$request->input($activity->id));
                        //dd($diff);

                        foreach($request->input($activity->id) as $key => $value) {
                            $activity_log = new ActivityLog;
                            $activity_log->log_id = $log->id;
                            $activity_log->activity_id = $activity->id;
                            $activity_log->activity_name = $activity->name;
                            $activity_log->old_value = $request->input('old_' . $activity->id);
                            $activity_log->new_value = $value;
                            $activity_log->save();
                        }
                    } else {
                        $old = $request->input('old_'.$activity->id);

                        $activity_log = new ActivityLog;
                        $activity_log->log_id = $log->id;
                        $activity_log->activity_id = $activity->id;
                        $activity_log->activity_name = $activity->name;
                        $activity_log->old_value = $request->input('old_'.$activity->id);
                        $activity_log->new_value = $request->input($activity->id);
                        $activity_log->save();
                    }

                    //activity type hook
                    switch ($activity->actionable_type) {
                        case 'App\RelatedPartyBoolean':
                            RelatedPartyBooleanData::where('client_id',$client->id)->where('related_party_boolean_id',$activity->actionable_id)->where('data',$old)->delete();

                            RelatedPartyBooleanData::insert([
                                'data' => $request->input($activity->id),
                                'related_party_boolean_id' => $activity->actionable_id,
                                'related_party_id' => $related_party_id,
                                'client_id' => $client->id,
                                'user_id' => auth()->id(),
                                'duration' => 120,
                                'created_at' => now()
                            ]);
                            break;
                        case 'App\RelatedPartyText':
                            RelatedPartyTextData::where('client_id',$client->id)->where('related_party_id',$related_party_id)->where('related_party_text_id',$activity->actionable_id)->where('data',$old)->delete();

                            RelatedPartyTextData::insert([
                                'data' => $request->input($activity->id),
                                'related_party_text_id' => $activity->actionable_id,
                                'related_party_id' => $related_party_id,
                                'client_id' => $client->id,
                                'user_id' => auth()->id(),
                                'duration' => 120,
                                'created_at' => now()
                            ]);
                            break;
                        case 'App\RelatedPartyTextarea':
                            RelatedPartyTextareaData::where('client_id',$client->id)->where('related_party_id',$related_party_id)->where('related_party_textarea_id',$activity->actionable_id)->where('data',$old)->delete();

                            $replace1 = str_replace('&nbsp;',' ',$request->input($activity->id));
                            $replace2 = str_replace('&ndash;','-',$replace1);
                            $replace3 = str_replace('&bull;', '- ',$replace2);
                            $replace4 = str_replace('&ldquo;','"',$replace3);
                            $replace5 = str_replace('&rdquo;','"',$replace4);
                            $replace6 = str_replace("&rsquo;","'",$replace5);
                            $replace7 = str_replace("&lsquo;","'",$replace6);

                            $data = $replace7;

                            RelatedPartyTextareaData::insert([
                                'data' => $data,
                                'related_party_textarea_id' => $activity->actionable_id,
                                'related_party_id' => $related_party_id,
                                'client_id' => $client->id,
                                'user_id' => auth()->id(),
                                'duration' => 120,
                                'created_at' => now()
                            ]);
                            break;
                        case 'App\RelatedPartyDate':

                            RelatedPartyDateData::insert([
                                'data' => $request->input($activity->id),
                                'related_party_date_id' => $activity->actionable_id,
                                'related_party_id' => $related_party_id,
                                'client_id' => $client->id,
                                'user_id' => auth()->id(),
                                'duration' => 120,
                                'created_at' => now()
                            ]);
                            break;
                        case 'App\RelatedPartyDropdown':
                            foreach($request->input($activity->id) as $key => $value){
                                if(in_array($value,$old,true)) {

                                } else {
                                    RelatedPartyDropdownData::insert([
                                        'related_party_dropdown_id' => $activity->actionable_id,
                                        'related_party_dropdown_item_id' => $value,
                                        'related_party_id' => $related_party_id,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);
                                }

                                if(!empty($diff)){
                                    RelatedPartyDropdownData::where('client_id',$client->id)->where('related_party_dropdown_id',$activity->actionable_id)->whereIn('related_party_dropdown_item_id',$diff)->delete();
                                }



                            }
                            break;
                        default:
                            //todo capture defaults
                            break;
                    }

                }
            }
            $load_next_step = false;
            //Move process step to the next step if all activities completed
            $max_step = Step::orderBy('order','desc')->where('process_id', $related_party->process_id)->first();

            $n_step = Step::select('id')->orderBy('order','asc')->where('process_id', $related_party->process_id)->where('order','>',$step->order)->whereNull('deleted_at')->first();

            if($client->isRelatedPartiesStepActivitiesCompleted($step,$related_party->id) && $related_party->step_id != $max_step["id"] && $step->id != $max_step["id"]){
                $related_party2 = RelatedParty::find($related_party->id);
                $related_party2->step_id = $n_step->id;
                $related_party2->save();
                $load_next_step = true;
            }

            if($client->isRelatedPartiesStepActivitiesCompleted($step,$related_party->id) && $step->id == $max_step["id"]){
                $related_party2 = RelatedParty::find($related_party->id);
                $related_party2->step_id = $max_step['id'];
                $related_party2->save();
                $load_next_step = false;
            }

            if($related_party->step_id == $request->input('step_id')){
                $related_party2 = RelatedParty::find($related_party->id);
                $related_party2->step_id = $step->id;
                $related_party2->save();
                $load_next_step = false;
            }

            //Handle files
            foreach($request->files as $key => $file):
                $file_activity = Activity::find($key);
                switch($file_activity->actionable_type){
                    case 'App\RelatedPartyDocument':
                        $afile = $request->file($key);
                        $name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".".$afile->getClientOriginalExtension();
                        $stored = $afile->storeAs('documents', $name);

                        $document = new Document;
                        $document->name = $file_activity->name;
                        $document->file = $name;
                        $document->user_id = auth()->id();
                        $document->client_id = $client->id;
                        $document->related_party_id = $related_party_id;
                        $document->save();

                        RelatedPartyDocumentData::insert([
                            'related_party_document_id' => $file_activity->actionable_id,
                            'document_id' => $document->id,
                            'client_id' => $client->id,
                            'related_party_id' => $related_party_id,
                            'user_id' => auth()->id(),
                            'duration' => 120
                        ]);

                        break;
                    default:
                        //todo capture detaults
                        break;
                }

            endforeach;

        }

        $parameters = [
            'client_id' => $request->input('client_id'),
            'process_id' => $request->input('process_id'),
            'step_id' => $request->input('step_id'),
            'related_party_id' => $related_party_id
        ];

        if($load_next_step == true) {
            return redirect()->route('relatedparty.stepprogress', ['client' => $client,'process'=>$related_party->process_id, 'step' => $n_step,'related_party_id'=>$related_party_id])->with('flash_success', 'Related party updated successfully.');
        }

        return redirect()->back()->with('flash_success', 'Related party updated successfully.');
    }

    public function activities($client_id, $related_party_id){
        $client = Client::withTrashed()->find($client_id);

        $client->load('referrer', 'office.area.region.division', 'users', 'comments.user');

        $steps = Step::where('process_id', $client->process_id)->orderBy('order', 'asc')->get();
        $c_step_order = Step::where('id', $client->step_id)->withTrashed()->first();

        $step_data = [];
        foreach ($steps as $a_step):
            if ($a_step->deleted_at == null) {
                $progress_color = $client->process->getStageHex(0);
                $step_stage2 =0;

                if ($c_step_order->order == $a_step->order)
                    $progress_color = $client->process->getStageHex(1);

                if ($c_step_order->order > $a_step->order)
                    $progress_color = $client->process->getStageHex(2);

                $tmp_step = [
                    'id' => $a_step->id,
                    'name' => $a_step->name,
                    'progress_color' => $progress_color,
                    'process_id' => $a_step->process_id,
                    'order' => $a_step->order,
                    'step2' => $step_stage2
                ];

                array_push($step_data, $tmp_step);
            }
        endforeach;

        $parameters = [
            'client' => $client,
            'view_process_dropdown' => $client->clientProcessIfActivitiesExist(),
            'steps' => $step_data,
        ];
        return view('relatedparties.activities')->with($parameters);
    }

    public function getClient($client_id){

        $client = Client::find($client_id);
        return response()->json($client);

    }

    public function addActivities($client_id, $process_id, $step_id/*Request $request,$client_id, Process $process, Step $step*/)
    {

        $step = Step::with('activities')->where('id', '=', $step_id)->first();

        $client = Client::withTrashed()->find($client_id);

        $client->load('referrer', 'office.area.region.division', 'users', 'comments.user');

        $steps = Step::where('process_id', $client->process_id)->orderBy('order', 'asc')->get();
        $c_step_order = Step::where('id', $client->step_id)->withTrashed()->first();

        $step_data = [];
        foreach ($steps as $a_step):
            if ($a_step->deleted_at == null) {
                $progress_color = $client->process->getStageHex(0);

                if ($c_step_order->order == $a_step->order)
                    $progress_color = $client->process->getStageHex(1);

                if ($c_step_order->order > $a_step->order)
                    $progress_color = $client->process->getStageHex(2);

                $tmp_step = [
                    'id' => $a_step->id,
                    'name' => $a_step->name,
                    'progress_color' => $progress_color,
                    'process_id' => $a_step->process_id,
                    'order' => $a_step->order
                ];

                array_push($step_data, $tmp_step);
            }
        endforeach;

        $activities_array = [];
        foreach ($step->activities as $activity) {

            $activity_array = [
                'id' => $activity->id,
                'name' => $activity->name,
                'type' => $activity->getTypeName(),
                'client' => $activity->client_activity,
                'kpi' => $activity->kpi,
                'stage' => $activity->stage,
                'type_display' => $activity->actionable_type,
                'comment' => $activity->comment,
                'weight' => $activity->weight,
                'dropdown_item' => '',
                'dropdown_items' => [],
                'is_dropdown_items_shown' => false,
                'user' => $activity->user_id ?? 0
            ];

            if ($activity->getTypeName() == 'dropdown') {
                $activity_array['dropdown_items'] = $activity->actionable->items->pluck('name')->toArray();
            }

            array_push($activities_array, $activity_array);
        }

        $parameters = [
            'client' => $client,
            'step' => $step,
            'activities' => $activities_array,
            'view_process_dropdown' => $client->clientProcessIfActivitiesExist(),
            'steps' => $step_data,
            'add_relative_party' => 'active',
        ];
        return view('relatedparties.addactivities')->with($parameters);

    }

    public function sendNotification(Client $client, RelatedParty $related_party, Activity $activity, Request $request)
    {
        //Notification update
        $log = new Log;
        $log->client_id = $client->id;
        $log->user_id = auth()->id();
        $log->save();

        $client->load('users');

        $activity_log = new ActivityLog;
        $activity_log->log_id = $log->id;
        $activity_log->activity_id = $activity->id;
        $activity_log->activity_name = $activity->name;
        $activity_log->old_value = $request->input('old_'.$activity->id);
        $activity_log->new_value = implode(',',$request->input('notification_user'));
        $activity_log->save();


        $notification = new Notification;
        $notification->name = 'Related party '.$related_party->description.' for '.$client->company . ' has been updated: ' . $activity->name;
        $notification->link = route('activitieslog', $log->id);
        //$notification->link = route('clients.progress', $client).'/1';
        $notification->save();

        $actionable_notification_data = new RelatedPartyNotificationData;
        $actionable_notification_data->related_party_notification_id = $activity->actionable_id;
        $actionable_notification_data->notification_id = $notification->id;
        $actionable_notification_data->client_id = $client->id;
        $actionable_notification_data->related_party_id = $related_party->id;
        $actionable_notification_data->user_id = auth()->id();
        $actionable_notification_data->duration = 120;
        $actionable_notification_data->save();

        $user_notifications = [];

        if (!is_null($client->introducer_id)) {
            array_push($user_notifications, [
                'user_id' => $client->introducer_id,
                'notification_id' => $notification->id
            ]);
        }

        if (!is_null($client->user_id)) {
            array_push($user_notifications, [
                'user_id' => $activity->user_id,
                'notification_id' => $notification->id
            ]);
        }

        if ( $request->has('notification_user') && (!empty($request->input('notification_user')))){
            $notification_users = $request->input('notification_user');
            foreach($notification_users as $notification_user):
                array_push($user_notifications, [
                    'user_id' => (int)$notification_user,
                    'notification_id' => $notification->id
                ]);
            endforeach;
        }

        NotificationEvent::dispatch($client->introducer_id, $notification);

        foreach ($client->users as $user) {
            array_push($user_notifications, [
                'user_id' => $user->id,
                'notification_id' => $notification->id
            ]);

            NotificationEvent::dispatch($user->id, $notification);
        }

        UserNotification::insert($user_notifications);

        return response()->json(['success' => 'Template sent successfully.']);
    }

    public function viewTemplate(Client $client, RelatedParty $related_party, Template $template)
    {

        $processed_template = $this->processTemplate($client, $related_party, $template->id, $template->file, $template->name);

        return response()->download(storage_path('app/templates/' . $processed_template));

    }

    public function viewDocument(Client $client, RelatedParty $related_party, Document $document)
    {

        $processed_document = $this->processDocument($client, $related_party, $document->id, $document->file, $document->name);

        return response()->download(storage_path('app/documents/' . $processed_document));

    }

    public function sendTemplate(Client $client, RelatedParty $related_party, Activity $activity, Request $request)
    {
        $template = Template::find($request->input('template_file'));

        $processed_templates = array();
        $processed_templates[0]['file'] = $this->processTemplate($client, $related_party, $template->id, $template->file, $template->name);
        $processed_templates[0]['type'] = 'template';

        $actionable_template_email = $activity->actionable;

        RelatedPartyTemplateEmailData::where('email',$client->email)->where('related_party_template_email_id',$actionable_template_email->id)->where('client_id',$client->id)->where('related_party_id',$related_party->id)->delete();

        RelatedPartyTemplateEmailData::insert([
            'template_id' => $template->id,
            'email' => $client->email,
            'related_party_template_email_id' => $actionable_template_email->id,
            'client_id' => $client->id,
            'related_party_id' => $related_party->id,
            'user_id' => auth()->id(),
            'duration' => 120,
            //'file' => $processed_template, To Do Add file name
        ]);

        if($request->session()->has('email_template') && $request->session()->get('email_template') != null) {
            $email_subject = $request->input('subject');
            $email_content = $request->session()->get('email_template');
        } else {
            $email_subject = $request->input('subject');

            if($request->has('email_content') && $request->input('email_content') != ""){
                $email_content = $request->input('email_content');
            } else {
                $email_content = EmailTemplate::where('id', $request->input('template_email'))->first()->email_content;
            }
        }

        $emails = explode(",", $request->input('email'));

        foreach ($emails as $email):
            Mail::to(trim($email))->send(new TemplateMail($related_party, $processed_templates, $email_subject, $email_content));

        endforeach;

        if($request->session()->has('email_template')){
            $request->session()->forget('email_template');
        }

        return response()->json(['success' => 'Template sent successfully.']);

    }

    public function sendDocument(Client $client, RelatedParty $related_party, Activity $activity, Request $request)
    {
        $documents= Document::find($request->input('document_file'));

        $processed_documents= array();
        $processed_documents[0]['file'] = $this->processDocument($client, $related_party, $documents->id, $documents->file, $documents->name);
        $processed_documents[0]['type'] = 'document';
//dd($this->processDocument($client, $documents->file));
        $actionable_documents_email = $activity->actionable;

        RelatedPartyDocumentEmailData::where('email',$client->email)->where('related_party_document_email_id',$actionable_documents_email->id)->where('client_id',$client->id)->where('related_party_id',$related_party->id)->delete();

        RelatedPartyDocumentEmailData::insert([
            'document_id' => $documents->id,
            'email' => $client->email,
            'related_party_document_email_id' => $actionable_documents_email->id,
            'client_id' => $client->id,
            'related_party_id' => $related_party->id,
            'user_id' => auth()->id(),
            'duration' => 120,
            //'file' => $processed_template, To Do Add file name
        ]);

        if($request->session()->has('email_template') && $request->session()->get('email_template') != null) {
            $email_subject = $request->input('subject');
            $email_content = $request->session()->get('email_template');
        } else {
            $email_subject = $request->input('subject');

            if($request->has('email_content') && $request->input('email_content') != ""){
                $email_content = $request->input('email_content');
            } else {
                $email_content = EmailTemplate::where('id', $request->input('template_email'))->first()->email_content;
            }
        }

        $emails = explode(",", $request->input('email'));

        foreach ($emails as $email):
            Mail::to(trim($email))->send(new TemplateMail($related_party, $processed_documents, $email_subject, $email_content));
        endforeach;

        return response()->json(['success' => 'Template sent successfully.']);
    }

    public function sendDocuments(Client $client, RelatedParty $related_party, Activity $activity, Request $request, Step $step)
    {
        //Todo
        $actionable_template_email = $activity->actionable;

        RelatedPartyMultipleAttachmentData::where('email', $client->email)->where('related_party_ma_id', $actionable_template_email->id)->where('related_party_id',$related_party->id)->where('client_id', $client->id)->delete();
        //Send to all templates
        $processed_templates = Array();
        $counter = 0;
        if($request->input('templates')) {
            $templates = explode(',',$request->input('templates'));
            foreach ($templates as $template_id):
                if($template_id != null && $template_id != '' && $template_id > 0) {
                    $template = Template::find($template_id);

                    $processed_templates[$counter]['file'] = $this->processTemplate($client, $related_party, $template->id, $template->file, $template->name);
                    $processed_templates[$counter]['type'] = 'template';

                    RelatedPartyMultipleAttachmentData::insert([
                        'template_id' => $template->id,
                        'email' => $request->input('email'),
                        'related_party_ma_id' => $actionable_template_email->id,
                        'client_id' => $client->id,
                        'related_party_id' => $related_party->id,
                        'user_id' => auth()->id(),
                        'duration' => 120,
                        'attachment_type' => 'template'
                        //'file' => $processed_template, To Do Add file name
                    ]);
                    $counter++;
                }
            endforeach;
        }

        if($request->input('documents')) {
            $documents = explode(',',$request->input('documents'));
            foreach ($documents as $document_id):
                $document = Document::find($document_id);
                $processed_templates[$counter]['file'] = $this->processDocument($client, $related_party, $document->id, $document->file, $document->name);
                $processed_templates[$counter]['type'] = 'document';

                RelatedPartyMultipleAttachmentData::insert([
                    'template_id' => $document->id,
                    'email' => $request->input('email'),
                    'related_party_ma_id' => $actionable_template_email->id,
                    'client_id' => $client->id,
                    'related_party_id' => $related_party->id,
                    'user_id' => auth()->id(),
                    'duration' => 120,
                    'attachment_type' => 'document'
                    //'file' => $processed_template, To Do Add file name
                ]);
                $counter++;
            endforeach;
        }

        //$email_signature = EmailSignature::where('user_id','=',auth()->id())->get();

        if($request->session()->has('email_template') && $request->session()->get('email_template') != null) {
            $email_subject = $request->input('subject');
            $email_content = $request->session()->get('email_template');
        } else {
            $email_subject = $request->input('subject');
            if($request->has('email_content') && $request->input('email_content') != ""){
                $email_content = $request->input('email_content');
            } else {
                $email_content = EmailTemplate::where('id', $request->input('template_email'))->first()->email_content;
            }
        }


        /*$a = new ActionableMultipleAttachmentData();
        $a->template_id = $request->input('template_email');
        $a->email = $request->input('email');
        $a->actionable_ma_id = $activity->actionable_id;
        $a->client_id = $client->id;
        $a->user_id = auth()->id();
        $a->duration = '120';
        $a->save();*/
        //'file' => $processed_template, To Do Add file name

        $emails = explode(",", $request->input('email'));

        foreach ($emails as $email):
            //Mail::to(trim($email))->send(new TemplateMail($client, $processed_templates, $email_content,$email_signature));
            Mail::to(trim($email))->send(new TemplateMail($client, $processed_templates, $email_subject, $email_content));
        endforeach;

        $request->session()->forget('email_template');

        return response()->json(['success' => 'Documents sent successfully.', 'docs' => $processed_templates]);
    }

    /*
    * @param Client
    * @param template file
    * @return the new generated processed template
    * Use to process a docx document, if document type is not .docx, return as it is
    */
    public function processTemplate(Client $client, RelatedParty $related_party, $template_id, $template_file, $template_name)
    {

        $filename = $template_name;
        $ext = pathinfo($template_file, PATHINFO_EXTENSION);

        //Only process docx files for now
        if ($ext == "pptx") {
            return $this->processPowerpointTemplate($related_party->id,$related_party->process_id,$template_id);
        } elseif ($ext == "docx") {
            return $this->processWordTemplate($client,$related_party,$template_file,$template_name);
        } else {
            return $template_file;
        }

    }

    public function processWordTemplate(Client $client, RelatedParty $related_party, $template_file, $template_name)
    {

        $filename = $template_name;
        $ext = pathinfo($template_file, PATHINFO_EXTENSION);

        //Only process docx files for now
        if ($ext != "docx") {
            return $template_file;
        }

        $related_party->load('referrer', 'introducer', 'business_unit');

        $templateProcessor = new TemplateProcessor(storage_path('app/templates/' . $template_file));
        $templateProcessor->setValue('date', date("Y/m/d"));
        $templateProcessor->setValue(
            ['client.first_name', 'client.last_name', 'client.email', 'client.contact', 'client.company', 'client.email', 'client.id_number', 'client.company_registration_number', 'client.cif_code', 'client.business_unit'],
            [$related_party->first_name, $related_party->last_name, $related_party->email, $related_party->contact, $related_party->company, $related_party->email, $related_party->id_number, $related_party->company_registration_number, $related_party->cif_code, $related_party->business_unit->name]
        );

        $templateProcessor->setValue(
            ['referrer.first_name', 'referrer.last_name', 'referrer.email', 'referrer.avatar'],
            isset($related_party->referrer) ?
                [
                    $related_party->referrer->first_name,
                    $related_party->referrer->last_name,
                    $related_party->referrer->email,
                    $related_party->referrer->contact
                ] :
                ['', '', '', '']
        );

        $templateProcessor->setValue(
            ['introducer.first_name', 'introducer.last_name', 'introducer.email', 'introducer.contact'],
            isset($related_party->introducer) ?
                [
                    $related_party->introducer->first_name,
                    $related_party->introducer->last_name,
                    $related_party->introducer->email,
                    $related_party->introducer->contact
                ] :
                ['', '', '', '']
        );

        $client_id = $client->id;
        $process_id = $related_party->process_id;
        $related_party_id = $related_party->id;
        $var_array = array();
        $value_array = array();

        $steps = Step::with(['activities.actionable.data'=>function ($q) use ($client_id,$related_party_id){
            $q->where('client_id',$client_id)->where('related_party_id',$related_party_id);
        }])->where('process_id',$process_id)->get();

        foreach($steps as $step) {

            foreach ($step["activities"] as $activity) {
                $var = '';
                switch ($activity['actionable_type']){
                    case 'App\RelatedPartyDropdown':
                        $var = 'activity.'.strtolower(str_replace(' ', '_', $activity->name));
                        array_push($var_array,$var);
                        break;

                    default:
                        $var = 'activity.'.strtolower(str_replace(' ', '_', $activity->name));
                        array_push($var_array, $var);
                        break;
                }

                if (isset($activity["actionable"]->data) && count($activity["actionable"]->data) > 0) {
                    foreach ($activity["actionable"]->data as $value) {

                        switch ($activity['actionable_type']){
                            case 'App\RelatedPartyDropdown':

                                $data = RelatedPartyDropdownItem::where('id',$value->related_party_dropdown_item_id)->first();
                                if($data){
                                    array_push($value_array, $data["name"]);
                                } else {
                                    array_push($value_array, '');
                                }
                                break;
                            case 'App\RelatedPartyBoolean':
                                $items = RelatedPartyBooleanData::where('client_id',$client_id)->where('related_party_boolean_id',$value->related_party_boolean_id)->first();

                                if($items){
                                    array_push($value_array, ($items->data == '0' ? 'No' : 'Yes'));
                                } else {
                                    array_push($value_array, '');
                                }

                                break;
                            default:

                                array_push($value_array, $value->data);
                                break;
                        }
                    }
                } else {

                    switch ($activity['actionable_type']){
                        case 'App\RelatedPartyDropdown':
                            $items = RelatedPartyDropdownItem::where('related_party_dropdown_id',$activity["actionable_id"])->get();
                            if($items){
                                foreach ($items as $item) {

                                    array_push($value_array, '');

                                }
                            } else {
                                array_push($value_array, '');
                            }

                            break;
                        default:

                            array_push($value_array, '');
                            break;
                    }
                }
            }
        }

        $templateProcessor->setValue(
            $var_array,$value_array
        );

        //Create directory to store processed templates, for future reference or to check what was sent to the client
        $processed_template_path = 'processedtemplates/' . date("Y") . DIRECTORY_SEPARATOR . date("m");
        if (!File::exists(storage_path('app/templates/' . $processed_template_path))) {
            Storage::makeDirectory('templates/' . $processed_template_path);
        }

        $processed_template = $processed_template_path . DIRECTORY_SEPARATOR . str_replace(' ','_',strtolower($filename)) . "_" . $client->id . "_" . date("Y_m_d_H_i_s") . "_" . uniqid() . ".docx";

        $templateProcessor->saveAs(storage_path('app/templates/' . $processed_template));

        return $processed_template;

    }

    public function processPowerpointTemplate($related_party_id,$process_id,$template_id)
    {
        // Grab the client
        $client = RelatedParty::where('id',$related_party_id);

        // Client details in an array
        $client = $client->first()->toArray();

        // What will eventually be sent to the report
        $output = [];
        $processData = [];

        // We have a client
        if($client) {
            // loop over steps to get the activity names, storing them in an assoc. array
            $steps = Step::where('process_id',$process_id)->orderBy('id')->get();

            foreach($steps as $step) {
                $activities = Activity::with(['actionable.data'=>function($query) use ($client){
                    $query->where('related_party_id',$client["id"])->orderBy('created_at','desc');
                }])->where('step_id',$step->id)->get();

                foreach($activities as $activity) {
                    if (strpos($activity['actionable_type'], 'RelatedParty') !== false) {
                        $completed_activity_clients_data = null;
                        switch ($activity['actionable_type']) {
                            case 'App\RelatedPartyBoolean':
                                $filter_data = ['' => 'All', 0 => 'No', 1 => 'Yes', 2 => 'Not Completed'];
                                $completed_activity_clients_data = RelatedPartyBooleanData::where('related_party_boolean_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'data')
                                    ->distinct()
                                    ->pluck('data', 'related_party_id');
                                break;
                            case 'App\RelatedPartyDate':
                                $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                $completed_activity_clients_data = RelatedPartyDateData::where('related_party_date_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'data')
                                    ->distinct()
                                    ->pluck('data', 'related_party_id');
                                break;
                            case 'App\RelatedPartyText':
                                $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                $completed_activity_clients_data = RelatedPartyTextData::where('related_party_text_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'data')
                                    ->distinct()
                                    ->pluck('data', 'related_party_id');
                                break;
                            case 'App\RelatedPartyTextarea':
                                $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                $completed_activity_clients_data = RelatedPartyTextareaData::where('related_party_textarea_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'data')
                                    ->distinct()
                                    ->pluck('data', 'related_party_id');
                                break;
                            case 'App\RelatedPartyDropdown':
                                $filter_data = RelatedPartyDropdownItem::where('related_party_dropdown_id', $activity['actionable_id'])
                                    ->select('name', 'id')
                                    ->distinct()
                                    ->pluck('name', 'id')
                                    ->prepend('All', 0);

                                /*if ($request->has('activity') && $request->input('activity') != '') {*/
                                $completed_activity_clients_data = RelatedPartyDropdownData::where('related_party_dropdown_id', $activity['actionable_id'])

                                    ->select('related_party_id', 'related_party_dropdown_item_id')
                                    ->distinct()
                                    //->get()->toArray();
                                    ->pluck('related_party_dropdown_item_id', 'related_party_id');
                                /*} else {
                                    $completed_activity_clients_data = ActionableDropdownData::where('actionable_dropdown_id', $activity['actionable_id'])
                                        ->select('client_id', 'actionable_dropdown_item_id')
                                        ->distinct()
                                        //->get()->toArray();
                                        ->pluck('actionable_dropdown_item_id', 'client_id');
                                }*/

                                // $tmp_filter_data2 = $filter_data->toArray();
                                // foreach($tmp_filter_data2 as $key=>$value):
                                //     array_push($tmp_filter_data, $key);
                                // endforeach;
                                break;
                            case 'App\RelatedPartyDocument':
                                $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                $completed_activity_clients_data = RelatedPartyDocumentData::where('related_party_document_id', $activity['actionable_id'])
                                    ->select('client_id', 'related_party_document_id')
                                    ->distinct()
                                    ->pluck('related_party_document_id', 'client_id');
                                break;
                            case 'App\RelatedPartyTemplateEmail':
                                $filter_data = Template::orderBy('name')
                                    ->select('name', 'id')
                                    ->distinct()
                                    ->pluck('name', 'id')
                                    ->prepend('All', 0);
                                $completed_activity_clients_data = RelatedPartyTemplateEmailData::where('related_party_template_email_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'template_id')
                                    ->distinct()
                                    ->pluck('template_id', 'related_party_id');
                                break;
                            case 'App\RelatedPartyNotification':
                                $filter_data = ['' => 'All', 1 => 'Sent', 0 => 'Not Sent'];
                                $completed_activity_clients_data = RelatedPartyNotificationData::where('related_party_notification_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'related_party_notification_id')
                                    ->distinct()
                                    ->pluck('related_party_notification_id', 'related_party_id');
                                break;
                            case 'App\RelatedPartyMultipleAttachment':
                                $filter_data = Template::orderBy('name')
                                    ->select('name', 'id')
                                    ->distinct()
                                    ->pluck('name', 'id')
                                    ->prepend('All', 0);
                                $completed_activity_clients_data = RelatedPartyMultipleAttachmentData::where('related_party_ma_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'template_id')
                                    ->distinct()
                                    ->pluck('template_id', 'related_party_id');
                                break;
                            default:
                                //todo capture defaults
                                break;
                        }

                        $data_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client['id']] : '';
                        $completed_value = '';
                        $selected_drop_down_names = '';

                        $data = '';
                        $yn_value = '';
                        switch ($activity['actionable_type']) {
                            case 'App\RelatedPartyBoolean':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) ? 'Yes' : 'No';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 2;
                                if (isset($completed_activity_clients_data[$client['id']]) && $completed_activity_clients_data[$client['id']] == '1') {
                                    $yn_value = "Yes";
                                }
                                if (isset($completed_activity_clients_data[$client['id']]) && $completed_activity_clients_data[$client['id']] == '0') {
                                    $yn_value = "No";
                                }
                                break;
                            case 'App\RelatedPartyDate':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client['id']] : '';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                break;
                            case 'App\RelatedPartyText':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client["id"]] : '';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                break;
                            case 'App\RelatedPartyTextarea':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client["id"]] : '';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                break;
                            case 'App\RelatedPartyDropdown':
                                $data_value = '';
                                /*if ($request->has('s') && $request->input('s') != '') {
                                    $selected_drop_down_items = ActionableDropdownData::with('item')->where('actionable_dropdown_id', $activity['actionable_id'])
                                        ->where('client_id', $client['id'])
                                        ->select('actionable_dropdown_item_id')
                                        ->distinct()
                                        ->get()->toArray();

                                    foreach ($selected_drop_down_items as $key => $selected_drop_down_item):
                                        //dd($selected_drop_down_item);
                                        if (in_array($selected_drop_down_item['actionable_dropdown_item_id'], $tmp_filter_data)) {
                                            if ($key == sizeof($selected_drop_down_items) - 1)
                                                $data_value .= $selected_drop_down_item['item']['name'];
                                            else
                                                $data_value .= $selected_drop_down_item['item']['name'] . ', ';
                                        }
                                    endforeach;
                                }*/
                                $data = RelatedPartyDropdownData::with('item')->where('related_party_id', $client['id'])->where('related_party_dropdown_id', $activity['actionable_id'])->first();
                                //dd($data->item->name);
                                $activity_data_value = isset($completed_activity_clients_data[$client['id']]) ? (isset($data->item->name) && $data->item->name != null ? $data->item->name : '') : '';
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) ? (isset($data->item->name) && $data->item->name != null ? $data->item->name : '') : '';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 0;
                                break;
                            case 'App\RelatedPartyDocument':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 'Completed' : 'Not Completed';

                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 0;
                                break;
                            case 'App\RelatedPartyTemplateEmail':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 'Completed' : 'Not Completed';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 0;
                                break;
                            case 'App\RelatedPartyNotification':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 'Completed' : 'Not Completed';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                break;
                            case 'App\RelatedPartyMultipleAttachment':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 'Completed' : 'Not Completed';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 0;
                                break;
                            default:
                                //todo capture defaults
                                break;

                        }


                        $processData[1] [] = ['name' => strtolower(str_replace(' ', '_', $activity['name'])),
                            'data' => $completed_value];


                    }
                }
            }
        }

        // Get the pptx template
        $template = Template::where('id', $template_id)->first();

        $presentation = new Presentation($template->file, ['client' => $client, 'activities' => $processData[1]]);

        // do whatevs
        $presentation->run();
        $downloadFile = $presentation->getDownloadPath();

        $headers = array(
            'Content-Type: application/vnd.ms-powerpoint',
        );

        //Create directory to store processed templates, for future reference or to check what was sent to the client
        $processed_template_path = 'processedtemplates/' . date("Y") . DIRECTORY_SEPARATOR . date("m");
        if (!File::exists(storage_path('app/templates/' . $processed_template_path))) {
            Storage::makeDirectory('templates/' . $processed_template_path);
        }

        $processed_template = $processed_template_path . DIRECTORY_SEPARATOR . str_replace(' ','_',strtolower($template->name)) . "_" . $client["id"] . "_" . date("Y_m_d_H_i_s") . "_" . uniqid() . ".pptx";

        $destinationPath= storage_path()."/app/templates/test.pptx";
        $success = \File::copy($downloadFile,storage_path('/app/templates/'.$processed_template));

        return $processed_template;
    }

    public function processDocument(Client $client, RelatedParty $related_party, $template_id, $template_file, $document_name)
    {
        $filename = $document_name;
        $ext = pathinfo($template_file, PATHINFO_EXTENSION);

        //Only process docx files for now
        if ($ext == "pptx") {
            return $this->processPowerpointDocument($related_party->id,$related_party->process_id,$template_id);
        } elseif ($ext == "docx") {
            return $this->processWordDocument($client->id,$related_party->id,$template_file,$document_name);
        } else {
            return $template_file;
        }

    }

    public function processWordDocument(Client $client, RelatedParty $related_party, $template_file, $document_name)
    {
        $filename = $document_name;
        $ext = pathinfo($template_file, PATHINFO_EXTENSION);

        //Only process docx files for now
        if ($ext != "docx") {
            return $template_file;
        }

        $related_party->load('referrer', 'introducer','business_unit');

        $templateProcessor = new TemplateProcessor(storage_path('app/documents/' . $template_file));
        $templateProcessor->setValue('date', date("Y/m/d"));
        $templateProcessor->setValue(
            ['client.first_name', 'client.last_name', 'client.email', 'client.contact', 'client.company', 'client.email', 'client.id_number', 'client.company_registration_number', 'client.cif_code', 'client.business_unit'],
            [$related_party->first_name, $related_party->last_name, $related_party->email, $related_party->contact, $related_party->company, $related_party->email, $related_party->id_number, $related_party->company_registration_number, $related_party->cif_code, $related_party->business_unit->name]
        );

        $templateProcessor->setValue(
            ['referrer.first_name', 'referrer.last_name', 'referrer.email', 'referrer.avatar'],
            isset($related_party->referrer) ?
                [
                    $related_party->referrer->first_name,
                    $related_party->referrer->last_name,
                    $related_party->referrer->email,
                    $related_party->referrer->contact
                ] :
                ['', '', '', '']
        );

        $templateProcessor->setValue(
            ['introducer.first_name', 'introducer.last_name', 'introducer.email', 'introducer.contact'],
            isset($related_party->introducer) ?
                [
                    $related_party->introducer->first_name,
                    $related_party->introducer->last_name,
                    $related_party->introducer->email,
                    $related_party->introducer->contact
                ] :
                ['', '', '', '']
        );

        $client_id = $client->id;
        $process_id = $related_party->process_id;
        $related_party_id = $related_party->id;
        $var_array = array();
        $value_array = array();

        $steps = Step::with(['activities.actionable.data'=>function ($q) use ($client_id,$related_party_id){
            $q->where('client_id',$client_id)->where('related_party_id',$related_party_id);
        }])->where('process_id',$process_id)->get();

        foreach($steps as $step) {

            foreach ($step["activities"] as $activity) {
                $var = '';
                switch ($activity['actionable_type']){
                    case 'App\RelatedPartyDropdown':
                        $var = 'activity.'.strtolower(str_replace(' ', '_', $activity->name));
                        array_push($var_array,$var);
                        break;

                    default:
                        $var = 'activity.'.strtolower(str_replace(' ', '_', $activity->name));
                        array_push($var_array, $var);
                        break;
                }

                if (isset($activity["actionable"]->data) && count($activity["actionable"]->data) > 0) {
                    foreach ($activity["actionable"]->data as $value) {

                        switch ($activity['actionable_type']){
                            case 'App\RelatedPartyDropdown':

                                $data = RelatedPartyDropdownItem::where('id',$value->related_party_dropdown_item_id)->first();
                                if($data){
                                    array_push($value_array, $data["name"]);
                                } else {
                                    array_push($value_array, '');
                                }
                                break;
                            case 'App\RelatedPartyBoolean':
                                $items = RelatedPartyBooleanData::where('client_id',$client_id)->where('related_party_boolean_id',$value->related_party_boolean_id)->first();

                                if($items){
                                    array_push($value_array, ($items->data == '0' ? 'No' : 'Yes'));
                                } else {
                                    array_push($value_array, '');
                                }

                                break;
                            default:

                                array_push($value_array, $value->data);
                                break;
                        }
                    }
                } else {

                    switch ($activity['actionable_type']){
                        case 'App\RelatedPartyDropdown':
                            $items = RelatedPartyDropdownItem::where('related_party_dropdown_id',$activity["actionable_id"])->get();
                            if($items){
                                foreach ($items as $item) {

                                    array_push($value_array, '');

                                }
                            } else {
                                array_push($value_array, '');
                            }

                            break;
                        default:

                            array_push($value_array, '');
                            break;
                    }
                }
            }
        }

        $templateProcessor->setValue(
            $var_array,$value_array
        );

        //Create directory to store processed templates, for future reference or to check what was sent to the client
        $processed_template_path = 'processeddocuments/' . date("Y") . DIRECTORY_SEPARATOR . date("m");
        if (!File::exists(storage_path('app/documents/' . $processed_template_path))) {
            Storage::makeDirectory('documents/' . $processed_template_path);
        }

        $processed_template = $processed_template_path . DIRECTORY_SEPARATOR . str_replace(' ','_',strtolower($filename)) . "_" . $client->id . "_" . date("Y_m_d_H_i_s") . "_" . uniqid() . ".docx";

        $templateProcessor->saveAs(storage_path('app/documents/' . $processed_template));

        return $processed_template;

    }

    public function processPowerpointDocument($related_party_id,$process_id,$template_id)
    {
        // Grab the client
        $client = RelatedParty::where('id',$related_party_id);

        // Client details in an array
        $client = $client->first()->toArray();

        // What will eventually be sent to the report
        $output = [];
        $processData = [];

        // We have a client
        if($client) {
            // loop over steps to get the activity names, storing them in an assoc. array
            $steps = Step::where('process_id',$process_id)->orderBy('id')->get();

            foreach($steps as $step) {
                $activities = Activity::with(['actionable.data'=>function($query) use ($client){
                    $query->where('related_party_id',$client["id"])->orderBy('created_at','desc');
                }])->where('step_id',$step->id)->get();

                foreach($activities as $activity) {
                    if (strpos($activity['actionable_type'], 'RelatedParty') !== false) {
                        $completed_activity_clients_data = null;
                        switch ($activity['actionable_type']) {
                            case 'App\RelatedPartyBoolean':
                                $filter_data = ['' => 'All', 0 => 'No', 1 => 'Yes', 2 => 'Not Completed'];
                                $completed_activity_clients_data = RelatedPartyBooleanData::where('related_party_boolean_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'data')
                                    ->distinct()
                                    ->pluck('data', 'related_party_id');
                                break;
                            case 'App\RelatedPartyDate':
                                $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                $completed_activity_clients_data = RelatedPartyDateData::where('related_party_date_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'data')
                                    ->distinct()
                                    ->pluck('data', 'related_party_id');
                                break;
                            case 'App\RelatedPartyText':
                                $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                $completed_activity_clients_data = RelatedPartyTextData::where('related_party_text_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'data')
                                    ->distinct()
                                    ->pluck('data', 'related_party_id');
                                break;
                            case 'App\RelatedPartyTextarea':
                                $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                $completed_activity_clients_data = RelatedPartyTextareaData::where('related_party_textarea_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'data')
                                    ->distinct()
                                    ->pluck('data', 'related_party_id');
                                break;
                            case 'App\RelatedPartyDropdown':
                                $filter_data = RelatedPartyDropdownItem::where('related_party_dropdown_id', $activity['actionable_id'])
                                    ->select('name', 'id')
                                    ->distinct()
                                    ->pluck('name', 'id')
                                    ->prepend('All', 0);

                                /*if ($request->has('activity') && $request->input('activity') != '') {*/
                                $completed_activity_clients_data = RelatedPartyDropdownData::where('related_party_dropdown_id', $activity['actionable_id'])

                                    ->select('related_party_id', 'related_party_dropdown_item_id')
                                    ->distinct()
                                    //->get()->toArray();
                                    ->pluck('related_party_dropdown_item_id', 'related_party_id');
                                /*} else {
                                    $completed_activity_clients_data = ActionableDropdownData::where('actionable_dropdown_id', $activity['actionable_id'])
                                        ->select('client_id', 'actionable_dropdown_item_id')
                                        ->distinct()
                                        //->get()->toArray();
                                        ->pluck('actionable_dropdown_item_id', 'client_id');
                                }*/

                                // $tmp_filter_data2 = $filter_data->toArray();
                                // foreach($tmp_filter_data2 as $key=>$value):
                                //     array_push($tmp_filter_data, $key);
                                // endforeach;
                                break;
                            case 'App\RelatedPartyDocument':
                                $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                $completed_activity_clients_data = RelatedPartyDocumentData::where('related_party_document_id', $activity['actionable_id'])
                                    ->select('client_id', 'related_party_document_id')
                                    ->distinct()
                                    ->pluck('related_party_document_id', 'client_id');
                                break;
                            case 'App\RelatedPartyTemplateEmail':
                                $filter_data = Template::orderBy('name')
                                    ->select('name', 'id')
                                    ->distinct()
                                    ->pluck('name', 'id')
                                    ->prepend('All', 0);
                                $completed_activity_clients_data = RelatedPartyTemplateEmailData::where('related_party_template_email_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'template_id')
                                    ->distinct()
                                    ->pluck('template_id', 'related_party_id');
                                break;
                            case 'App\RelatedPartyNotification':
                                $filter_data = ['' => 'All', 1 => 'Sent', 0 => 'Not Sent'];
                                $completed_activity_clients_data = RelatedPartyNotificationData::where('related_party_notification_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'related_party_notification_id')
                                    ->distinct()
                                    ->pluck('related_party_notification_id', 'related_party_id');
                                break;
                            case 'App\RelatedPartyMultipleAttachment':
                                $filter_data = Template::orderBy('name')
                                    ->select('name', 'id')
                                    ->distinct()
                                    ->pluck('name', 'id')
                                    ->prepend('All', 0);
                                $completed_activity_clients_data = RelatedPartyMultipleAttachmentData::where('related_party_ma_id', $activity['actionable_id'])
                                    ->select('related_party_id', 'template_id')
                                    ->distinct()
                                    ->pluck('template_id', 'related_party_id');
                                break;
                            default:
                                //todo capture defaults
                                break;
                        }

                        $data_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client['id']] : '';
                        $completed_value = '';
                        $selected_drop_down_names = '';

                        $data = '';
                        $yn_value = '';
                        switch ($activity['actionable_type']) {
                            case 'App\RelatedPartyBoolean':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) ? 'Yes' : 'No';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 2;
                                if (isset($completed_activity_clients_data[$client['id']]) && $completed_activity_clients_data[$client['id']] == '1') {
                                    $yn_value = "Yes";
                                }
                                if (isset($completed_activity_clients_data[$client['id']]) && $completed_activity_clients_data[$client['id']] == '0') {
                                    $yn_value = "No";
                                }
                                break;
                            case 'App\RelatedPartyDate':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client['id']] : '';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                break;
                            case 'App\RelatedPartyText':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client["id"]] : '';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                break;
                            case 'App\RelatedPartyTextarea':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client["id"]] : '';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                break;
                            case 'App\RelatedPartyDropdown':
                                $data_value = '';
                                /*if ($request->has('s') && $request->input('s') != '') {
                                    $selected_drop_down_items = ActionableDropdownData::with('item')->where('actionable_dropdown_id', $activity['actionable_id'])
                                        ->where('client_id', $client['id'])
                                        ->select('actionable_dropdown_item_id')
                                        ->distinct()
                                        ->get()->toArray();

                                    foreach ($selected_drop_down_items as $key => $selected_drop_down_item):
                                        //dd($selected_drop_down_item);
                                        if (in_array($selected_drop_down_item['actionable_dropdown_item_id'], $tmp_filter_data)) {
                                            if ($key == sizeof($selected_drop_down_items) - 1)
                                                $data_value .= $selected_drop_down_item['item']['name'];
                                            else
                                                $data_value .= $selected_drop_down_item['item']['name'] . ', ';
                                        }
                                    endforeach;
                                }*/
                                $data = RelatedPartyDropdownData::with('item')->where('related_party_id', $client['id'])->where('related_party_dropdown_id', $activity['actionable_id'])->first();
                                //dd($data->item->name);
                                $activity_data_value = isset($completed_activity_clients_data[$client['id']]) ? (isset($data->item->name) && $data->item->name != null ? $data->item->name : '') : '';
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) ? (isset($data->item->name) && $data->item->name != null ? $data->item->name : '') : '';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 0;
                                break;
                            case 'App\RelatedPartyDocument':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 'Completed' : 'Not Completed';

                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 0;
                                break;
                            case 'App\RelatedPartyTemplateEmail':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 'Completed' : 'Not Completed';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 0;
                                break;
                            case 'App\RelatedPartyNotification':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 'Completed' : 'Not Completed';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                break;
                            case 'App\RelatedPartyMultipleAttachment':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 'Completed' : 'Not Completed';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 0;
                                break;
                            default:
                                //todo capture defaults
                                break;

                        }


                        $processData[1] [] = ['name' => strtolower(str_replace(' ', '_', $activity['name'])),
                            'data' => $completed_value];


                    }
                }
            }
        }

        // Get the pptx template
        $template = Document::where('id', $template_id)->first();

        $presentation = new Presentation($template->file, ['client' => $client, 'activities' => $processData[1]]);

        // do whatevs
        $presentation->run();
        $downloadFile = $presentation->getDownloadPath();

        $headers = array(
            'Content-Type: application/vnd.ms-powerpoint',
        );

        //Create directory to store processed templates, for future reference or to check what was sent to the client
        $processed_template_path = 'processeddocuments/' . date("Y") . DIRECTORY_SEPARATOR . date("m");
        if (!File::exists(storage_path('app/documents/' . $processed_template_path))) {
            Storage::makeDirectory('documents/' . $processed_template_path);
        }

        $processed_template = $processed_template_path . DIRECTORY_SEPARATOR . str_replace(' ','_',strtolower($template->name)) . "_" . $client["id"] . "_" . date("Y_m_d_H_i_s") . "_" . uniqid() . ".pptx";

        $destinationPath= storage_path()."/app/documents/test.pptx";
        $success = \File::copy($downloadFile,storage_path('/app/documents/'.$processed_template));

        return $processed_template;
    }


    public function documents(Request $request,Client $client,RelatedParty $related_party)
    {
        $config = Config::first();

        if((strpos($request->headers->get('referer'),'reports') !== false) || (strpos($request->headers->get('referer'),'custom_report') !== false)) {
            $request->session()->put('path_route',$request->headers->get('referer'));
            $path = '1';
            $path_route = $request->session()->get('path_route');
        } else {
            $request->session()->forget('path_route');
            $path = '0';
            $path_route = '';
        }

        $step = Step::withTrashed()->find($client->step_id);
        $process_progress = $client->getProcessStepProgress($step);

        $client_progress = $client->process->getStageHex(0);

        if($client->step_id == $step->id)
            $client_progress = $client->process->getStageHex(1);

        if($client->step_id > $step->id)
            $client_progress = $client->process->getStageHex(2);

        $steps = Step::where('process_id', $client->process_id)->orderBy('order','asc')->get();

        $step_data = [];

        $c_step_order = Step::where('id', $client->step_id)->where('process_id', $client->process_id)->withTrashed()->first();

        foreach ($steps as $a_step) {

            $progress_color = $client->process->getStageHex(0);
            $step_stage = 0;

            if ($c_step_order["order"] == $a_step->order) {
                $progress_color = $client->process->getStageHex(1);
                $step_stage = 1;
            }

            if ($c_step_order["order"] > $a_step->order) {
                $progress_color = $client->process->getStageHex(2);
                $step_stage = 2;
            }

            /*if ($c_step_order["order"] == $a_step->order && $client->completed_at != null && $a_step->process_id == $client->process_id) {
                $progress_color = $client->process->getStageHex(2);
        /    }*/


            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'process_id' => $a_step->process_id,
                'progress_color' => $progress_color,
                'stage' => $step_stage
            ];

            array_push($step_data, $tmp_step);
        }

        $related_party_id = $related_party->id;

        $step = Step::with('activities.related_party.data')->where('process_id',$client->process_id)->first();
        $related_party_steps = Step::with(['activities.related_party.data'=>function($q) use ($related_party_id){
            $q->where('related_party_id',$related_party_id);
        }])->where('process_id',$related_party->process_id)->orderBy('order')->get();

        $related_party_step_data = [];

        $r_step_order = Step::where('id', $related_party->step_id)->where('process_id', $related_party->process_id)->withTrashed()->first();

        foreach ($related_party_steps as $a_step) {
            $progress_color = $client->process->getStageHex(0);
            $step_stage2 = 0;

            if ($r_step_order["order"] == $a_step->order) {
                $progress_color = $client->process->getStageHex(1);
                $step_stage2 = 1;
            }

            if ($r_step_order["order"] > $a_step->order) {
                $progress_color = $client->process->getStageHex(2);
                $step_stage2 = 2;
            }

            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'progress_color' => $progress_color,
                'process_id' => $a_step->process_id,
                'order' => $a_step->order,
                'stage2' => $step_stage2
            ];

            array_push($related_party_step_data, $tmp_step);
        }

        $related_parties = [];
        $this->getRelatedParties($client->id, $related_parties, 0);

        $project = Project::where('name','!=','')->whereNotNull('name')->get();
        $committee = Committee::where('name','!=','')->whereNotNull('name')->pluck('name','id')->prepend('Select','0');

        return view('relatedparties.documents')->with([
            'client' => $client,
            'client_id' => $client->id,
            'related_party_id' => $related_party->id,
            'process_id' => Process::where('id',$related_party->process_id)->first()->id,
            'related_parties' => $related_parties,
            'related_party' => $related_party,
            'related_party_steps' => $related_party_step_data,
            'process_progress' => $process_progress,
            'view_process_dropdown' => $client->clientProcessIfActivitiesExist(),
            'steps' => $step_data,
            'step' => $step,
            'max_step' => Step::where('process_id', $related_party->process_id)->orderBy('id', 'desc')->first()->id,
            'path' => $path,
            'path_route' => $path_route,
            'r' => (isset($config)?Step::where('process_id',$config->related_party_process)->orderBy('order','asc')->take(1)->first()->id:0),
            'business_unit' => BusinessUnits::orderBy('id','asc')->pluck('name','id'),
            'projects'=>$project,
            'committees'=>$committee,'trigger_types' => TriggerType::orderBy('id','asc')->pluck('name','id')->prepend('Please Select',''),
            'committee'=>($client->committee_id > 0 ? $client->committee_id : 0),
            'project'=>($client->project_id > 0 ? $client->project->name : ''),
            'trigger_type'=>($client->trigger_type_id > 0 ? $client->trigger_type_id : ''),
            'casenr'=>(isset($client->case_number) ? $client->case_number : ''),
            'instruction_date'=>(isset($client->instruction_date) ? $client->instruction_date : '')
        ]);
    }

    public function storeComment(Client $client, RelatedParty $related_party, Request $request)
    {
        $comment = new RelatedPartyComment;
        $comment->related_party_id = $related_party->id;
        $comment->user_id = auth()->id();
        $comment->comment = $request->input('comment');
        $comment->save();

        return redirect()->back()->with('flash_success', 'Comment added successfully');
    }

    public function completeStep(Client $client, RelatedParty $related_party, Process $process, Step $step){

        $step->load('activities.actionable.data');
        //dd($client->id);
        $activities_auto_completed = [];
        //return null;
        //todo just change switch logic to assign fake data for all activities
        foreach($step->activities as $activity){

            //Check if activity is not already set/completed
            if(!$client->isRelatedPartiesActivitieCompleted($activity,$related_party->id)){
                //if(!isset($activity->actionable['data'][0])){
                $found  = false;
                foreach ($activity->actionable['data'] as $datum){
                    if($datum->client_id == $related_party->client_id && $datum->related_party_id == $related_party->id){
                        $found = true;
                        break;
                    }
                }

                if(!$found){
                    $activities_auto_completed[] = $activity->id;
                    $actionable_text_data = $activity->actionable_type.'Data';
                    $actionable_data = new $actionable_text_data;
                    $actionable_data->client_id = $related_party->client_id;
                    $actionable_data->related_party_id = $related_party->id;
                    $actionable_data->user_id = auth()->id();
                    $actionable_data->duration = 120;
                    switch($activity->getRelatedPartyTypeName()){
                        case 'text':
                            $actionable_data->data = null;
                            $actionable_data->related_party_text_id = $activity->actionable_id;
                            $actionable_data->save();
                            break;
                        case 'textarea':
                            $actionable_data->data = null;
                            $actionable_data->related_party_textarea_id = $activity->actionable_id;
                            $actionable_data->save();
                            break;
                        case 'template_email':
                            $actionable_data->template_id = 1;
                            $actionable_data->email = null;
                            $actionable_data->related_party_template_email_id = $activity->actionable_id;
                            $actionable_data->save();
                            break;
                        case 'document_email':
                            //return 'document_email';
                            break;
                        case 'document':
                            $actionable_data->related_party_document_id = $activity->actionable_id;
                            $actionable_data->document_id = 1;
                            $actionable_data->save();
                            break;
                        case 'dropdown':
                            $item = RelatedPartyDropdownItem::where('related_party_dropdown_id', $activity->actionable_id)->take(1)->first();

                            $actionable_data->related_party_dropdown_id = $activity->actionable_id;
                            $actionable_data->related_party_dropdown_item_id = 0;
                            $actionable_data->save();
                            break;
                        case 'date':
                            $actionable_data->data = null;
                            $actionable_data->related_party_date_id = $activity->actionable_id;
                            $actionable_data->save();
                            break;
                        case 'boolean':
                            $actionable_data->data = null;
                            $actionable_data->related_party_boolean_id = $activity->actionable_id;
                            $actionable_data->save();
                            break;
                        case 'notification':
                            $notification = new Notification;
                            $notification->name = $client->company . ' has been updated: ' . $activity->name;
                            $notification->link = route('clients.progress', $client);
                            $notification->save();

                            $actionable_data->related_party_notification_id = $activity->actionable_id;
                            $actionable_data->notification_id = $notification->id;
                            $actionable_data->save();
                            break;
                        case 'multiple_attachment':
                            $actionable_data->template_id = 1;
                            $actionable_data->email = null;
                            $actionable_data->related_party_ma_id = $activity->actionable_id;
                            $actionable_data->save();
                            break;
                        default:
                            //return 'error';
                            break;
                    }
                }

            }
        }

        //Move process step to the next step if all activities completed
        $max_step = Step::orderBy('order','desc')->where('process_id', $related_party->process_id)->first();

        $n_step = Step::select('id')->orderBy('order','asc')->where('process_id', $related_party->process_id)->where('order','>',$step->order)->whereNull('deleted_at')->first();

        if($client->isRelatedPartyStepActivitiesCompleted($step,$related_party->id) && $step->order < $max_step->order  && $step->id != $max_step["id"]){
            $relatedparty = RelatedParty::find($related_party->id);
            //$client->step_id = $client->step_id + 1;
            $relatedparty->step_id = $n_step->id;
            $relatedparty->save();

            return response()->json(['success' => 'Template sent successfully.', 'activities_auto_completed' => $activities_auto_completed,'client_id'=>$related_party->client_id,'process_id'=>$related_party->process_id,'step_id'=>$n_step->id,'related_party_id'=>$related_party->id]);
        }

        if(!$n_step) {
            if ($client->isRelatedPartyStepActivitiesCompleted($step,$related_party->id)) {
                $relatedparty = RelatedParty::find($related_party->id);
                //$client->step_id = $client->step_id + 1;
                $relatedparty->process_id = $process->id;
                $relatedparty->step_id = $max_step->id;
                $relatedparty->save();
            }

            return response()->json(['success' => 'Template sent successfully.', 'activities_auto_completed' => $activities_auto_completed,'client_id'=>$related_party->client_id,'process_id'=>$related_party->process_id,'step_id'=>$max_step->id,'related_party_id'=>$related_party->id]);
        }

        return response()->json(['success' => 'Template sent successfully.', 'activities_auto_completed' => $activities_auto_completed,'client_id'=>$related_party->client_id,'process_id'=>$related_party->process_id,'step_id'=>$related_party->step_id,'related_party_id'=>$related_party->id]);
    }

    public function storeProgressing($related_party){
        $rrelated_party = RelatedParty::find($related_party);
        $rrelated_party->is_progressing = !$rrelated_party->is_progressing;
        if($rrelated_party->is_progressing == 0) {
            $rrelated_party->not_progressing_date = now();
        } else {
            $rrelated_party->not_progressing_date = null;
        }

        $rrelated_party->save();

        return redirect()->back()->with(['flash_info'=>'Related Party status updated successfully.']);
    }

    public function WorkItemQA($id)
    {

        $relatedparty = RelatedParty::find($id);
        $relatedparty->work_item_qa = 1;
        $relatedparty->work_item_qa_date = now();
        $relatedparty->qa_consultant = auth()->id();
        $relatedparty->save();

        return response()->json(['data' => 'success']);
    }
}
