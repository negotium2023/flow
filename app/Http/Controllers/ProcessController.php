<?php

namespace App\Http\Controllers;

use App\ActionableAmount;
use App\ActionableBoolean;
use App\ActionableContent;
use App\ActionableDate;
use App\ActionableDocument;
use App\ActionableDocumentEmail;
use App\ActionableDropdown;
use App\ActionableHeading;
use App\ActionableImageUpload;
use App\ActionableInteger;
use App\ActionableMultipleAttachment;
use App\ActionableNotification;
use App\ActionablePercentage;
use App\ActionableSubheading;
use App\ActionableTemplateEmail;
use App\ActionableText;
use App\ActionableTextarea;
use App\ActionableVideoUpload;
use App\ActionableVideoYoutube;
use App\Activity;
use App\Area;
use App\Client;
use App\ClientProcess;
use App\Http\Requests\UpdateProcessRequest;
use App\Office;
use App\OfficeUser;
use App\Process;
use App\ProcessArea;
use App\ProcessGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Step;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreProcessRequest;

class ProcessController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function groupIndex(Request $request)
    {
        $groups = [];

        $process_type_id = $request->has('f') ? $request->input('f') : 0;

        if ($request->has('q')) {
            $process_groups = ProcessGroup::where('form',$process_type_id)->where('name', 'LIKE', "%" . $request->input('q') . "%")->get();
        } else {
            $process_groups = ProcessGroup::where('form',$process_type_id)->get();
        }

        foreach($process_groups as $process_group){
            $groups[][$process_group->name] = [
                'id' => $process_group->id,
                'name' => $process_group->name,
                'pcount' => Process::where('process_group_id',$process_group->id)->where('form',$process_type_id)->count(),
                'created_at' => $process_group->created_at,
                'updated_at' => $process_group->updated_at
            ];
        }

        ksort($groups);

        $groups[]['None'] = [
            'id' => '0',
            'name' => 'None',
            'pcount' => Process::where('process_group_id',0)->where('process_type_id',$process_type_id)->count(),
            'created_at' => 'N/A',
            'updated_at' => 'N/A'
        ];

        //$groups += array_splice($groups,array_search('None',array_keys($groups)),1);

        $parameters = [
            'processes' => $groups,
            'type_name' => $process_type_id == 1 ? 'Forms' : 'Process',
            'process_type_id' => $process_type_id
        ];

        return view('processes.groupindex')->with($parameters);
    }

    public function groupCreate(Request $request)
    {
        $parameters = [
            'type_name' => 'Processes',
            'process_type_id' => $request->input('f')
        ];

        return view('processes.groupcreate')->with($parameters);
    }

    public function groupStore(Request $request){
        $process = new ProcessGroup;
        $process->name = $request->input('name');
        $process->form = $request->input('process_type_id');
        $process->save();

        return redirect(route('processesgroup.index').'?f='.$request->input('process_type_id'))->with('flash_success', 'Process group created successfully.');
    }

    public function groupEdit($group_id){
        $group = ProcessGroup::where('id',$group_id)->get();

        $parameters = [
            'process_groups' => $group
        ];

        return view('processes.groupedit')->with($parameters);
    }

    public function groupUpdate(Request $request,$group_id){
        $group = ProcessGroup::find($group_id);
        $group->name = $request->input('name');
        $group->save();

        return redirect(route('processesgroup.index'))->with('flash_success', 'Process updated successfully.');
    }

    public function groupDestroy(Request $request,$group_id){

        $processes = Process::where('process_group_id',$group_id)->get();

        if($processes->count()>0){

            return redirect(route('processes.index',$group_id))->with('flash_danger', 'There are still active sub-processes assigned to this process.');
        }

        $process = ProcessGroup::find($group_id);

        $process->destroy($group_id);
        $process_type_id = $request->has('t') ? $request->input('t') : 1;

        return redirect(route('processesgroup.index', ['t' => $process_type_id]))->with(['flash_success' => 'Sub-process deleted successfully.']);
    }

    public function index(Request $request,$group_id)
    {

        $process_type_id = $request->has('f') ? $request->input('f') : 0;

        $areas = auth()->user()->areas()->get()->map(function ($area){
                    return $area->id;
                })->toArray();

        $offices = auth()->user()->offices()->get()->map(function ($office){
            return $office->id;
        })->toArray();

        $processes = Process::with(['steps','pgroup','process_area.office.area.region.division'])
            ->where('process_group_id',$group_id)
            ->where('form', '=', $process_type_id);

        if ($request->has('q')) {
            $processes->where('name', 'LIKE', "%" . $request->input('q') . "%");
        }

        $processes = $processes->get();

        $parameters = [
            'process_group' => ($group_id == 0 ? 0 : ProcessGroup::where('id',$group_id)->first()),
            'process_groups' => ProcessGroup::pluck('name','id'),
            'processes' => $processes,
            'type_name' => $process_type_id == 1 ? 'Forms' : 'Processes',
            'type_name_single' => $process_type_id == 1 ? 'Form' : 'Process',
            'process_type_id' => $process_type_id
        ];

        return view('processes.index')->with($parameters);
    }

    public function show($processgroup, $process, Request $request)
    {

        $process = Process::with(['steps'=> function ($q){
            $q->whereNull('deleted_at');
        }],'process_area.office.area.region.division')->where('id',$process)->first();

        $process_type_id = $request->has('t') ? $request->input('t'):1;

        return view('processes.show')->with(['processgroup'=>$processgroup,'process' => $process, 'process_type_id' => $process_type_id]);
    }

    public function create(Request $request,$group_id)
    {
        $process_type_id = $request->has('f') ? $request->input('f') : 1;

        $parameters = [
            'process_group' => $group_id,
            'process_groups' => ProcessGroup::orderBy('name')->where('form',$process_type_id)->pluck('name','id')->prepend('Please Select','0'),
            'areas' => Area::whereHas('region.division')->orderBy('name')->get()->pluck('name', 'id')->prepend('All','0'),
            'offices' => Office::orderBy('name')->get()->pluck('name', 'id'),
            'type_name' => $process_type_id == 1 ? 'Form' : 'Process',
            'process_type_id' => $process_type_id
        ];

        return view('processes.create')->with($parameters);
    }

    public function store(StoreProcessRequest $request,$group_id)
    {
        foreach($request->input('area') as $area){
            if($area == '0'){

                $process = new Process;
                $process->name = $request->input('name');
                $process->global = 1;
                $process->process_group_id = $request->input('process');
                $process->not_started_colour = str_replace('#', '', $request->input('not_started_colour'));
                $process->started_colour = str_replace('#', '', $request->input('started_colour'));
                $process->completed_colour = str_replace('#', '', $request->input('completed_colour'));
                if($request->hasFile('document')){
                    $file = $request->file('document');
                    $name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".".$file->getClientOriginalExtension();
                    $stored = $file->storeAs('templates', $name);

                    $process->document = $name;
                }
                if($request->has('docfusion_process_id') && $request->input('docfusion_process_id') != ''){
                    $process->docfusion_process_id = $request->input('docfusion_process_id');
                }
                if($request->has('docfusion_template_id') && $request->input('docfusion_template_id') != ''){
                    $process->docfusion_template_id = $request->input('docfusion_template_id');
                }
                $process->process_type_id = $request->has('process_type_id') ? $request->input('process_type_id') : 1;
                $process->process_group_id = $group_id;
                $process->save();

                $offices = Office::get();

                foreach ($offices as $office){
                    $process_area = new ProcessArea();
                    $process_area->process_id = $process->id;
                    $process_area->area_id = $office->area->id;
                    $process_area->office_id = $office->id;
                    $process_area->save();
                }
            } else {
                $process = new Process;
                $process->name = $request->input('name');
                $process->process_group_id = $request->input('process');
                $process->not_started_colour = str_replace('#', '', $request->input('not_started_colour'));
                $process->started_colour = str_replace('#', '', $request->input('started_colour'));
                $process->completed_colour = str_replace('#', '', $request->input('completed_colour'));
                if($request->hasFile('document')){
                    $file = $request->file('document');
                    $name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".".$file->getClientOriginalExtension();
                    $stored = $file->storeAs('templates', $name);

                    $process->document = $name;
                }
                if($request->has('docfusion_process_id') && $request->input('docfusion_process_id') != ''){
                    $process->docfusion_process_id = $request->input('docfusion_process_id');
                }
                if($request->has('docfusion_template_id') && $request->input('docfusion_template_id') != ''){
                    $process->docfusion_template_id = $request->input('docfusion_template_id');
                }
                $process->process_type_id = 1;
                $process->form = $request->has('process_type_id') ? $request->input('process_type_id') : 1;
                $process->process_group_id = $group_id;
                $process->save();

                if($request->has('office') && count($request->input('office')) >=0) {
                    $offices = Office::whereHas('area', function ($q) use ($area) {
                        $q->where('id', $area);
                    })->whereIn('id', $request->input('office'))->get();

                    //dd($offices);
                    foreach ($offices as $office) {
                        $process_area = new ProcessArea();
                        $process_area->process_id = $process->id;
                        $process_area->area_id = $office->area->id;
                        $process_area->office_id = $office->id;
                        $process_area->save();
                    }
                } else {
                    $offices = Office::whereHas('area',function ($q) use ($area) {
                        $q->where('id', $area);
                    })->get();

                    $offices2 = Office::whereHas('area',function ($q) use ($area) {
                        $q->where('id', $area);
                    })->whereIn('id',$request->input('office'))->get();


                    foreach ($offices2 as $office){
                        $process_area = new ProcessArea();
                        $process_area->process_id = $process->id;
                        $process_area->area_id = $office->area->id;
                        $process_area->office_id = $office->id;
                        if(count($offices) == count($offices2)) {

                        }
                        $process_area->save();
                    }
                }
            }
        }

        $type_name = $request->has('process_type_id') && $request->input('process_type_id') == 2 ? 'Related Parties Structure' : 'Process';

        return redirect(route('processes.show',[$group_id,$process]))->with('flash_success', $type_name.' create successfully.');
    }

    public function edit($group_id,$process_id)
    {
        $process_area = [];

        $pas = ProcessArea::select('area_id')->where('process_id',$process_id)->distinct()->get();

        if(count($pas) == count(Area::whereHas('region.division')->orderBy('name')->get())){
            $process_area[0] = '0';
        } else {
            foreach ($pas as $pa) {

                array_push($process_area, $pa->area_id);

            }
        }

        //dd($process_area);

        $paramaters = [
            'process' => Process::where('id',$process_id)->first(),
            'process_groups' => ProcessGroup::orderBy('name')->pluck('name','id')->prepend('Please Select','0'),
            'areas' => Area::whereHas('region.division')->orderBy('name')->get()->pluck('name', 'id')->prepend('All','0'),
            'offices' => Office::orderBy('name')->get()->pluck('name', 'id'),
            'process_areas' => $process_area
        ];

        //dd($paramaters);

        return view('processes.edit')->with($paramaters);
    }

    public function update($process_group,$process_id, UpdateProcessRequest $request)
    {
        $global = 0;

        ProcessArea::where('process_id',$process_id)->delete();

        foreach($request->input('area') as $area){

            if($area == '0'){
                $offices = Office::get();

                foreach ($offices as $office){
                    $process_area = new ProcessArea();
                    $process_area->process_id = $process_id;
                    $process_area->area_id = $office->area->id;
                    $process_area->office_id = $office->id;
                    $global = 1;
                    $process_area->save();
                }
            } else {

                $offices = Office::whereHas('area',function ($q) use ($area) {
                    $q->where('id', $area);
                })->get();

                $offices2 = Office::whereHas('area',function ($q) use ($area) {
                    $q->where('id', $area);
                })->whereIn('id',$request->input('office'))->get();


                foreach ($offices2 as $office){
                    $process_area = new ProcessArea();
                    $process_area->process_id = $process_id;
                    $process_area->area_id = $office->area->id;
                    $process_area->office_id = $office->id;
                    if(count($offices) == count($offices2)) {
                        $global = 1;
                    } else {
                        $global = 0;
                    }
                    $process_area->save();
                }
            }
        }

        //dd($request->input());
        $process = Process::find($process_id);
        $process->name = $request->input('name');
        //$process->office_id = $request->input('office');
        /*$process->office_id = 0;*/
        $process->global = $global;
        $process->process_group_id = $request->input('process');
        $process->not_started_colour = str_replace('#', '', $request->input('not_started_colour'));
        $process->started_colour = str_replace('#', '', $request->input('started_colour'));
        $process->completed_colour = str_replace('#', '', $request->input('completed_colour'));
        if($request->hasFile('document')){
            $file = $request->file('document');
            $name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".".$file->getClientOriginalExtension();
            $stored = $file->storeAs('templates', $name);

            $process->document = $name;
        }
        if($request->has('docfusion_process_id') && $request->input('docfusion_process_id') != ''){
            $process->docfusion_process_id = $request->input('docfusion_process_id');
        }
        if($request->has('docfusion_template_id') && $request->input('docfusion_template_id') != ''){
            $process->docfusion_template_id = $request->input('docfusion_template_id');
        }
        $process->save();



        return redirect(route('processes.show',[$process_group,$process_id]))->with('flash_success', 'Process updated successfully.');
    }

    public function destroy($group_id,Process $process, $processid, Request $request){

        if($process->clients->count()>0){

            return redirect(route('processes.show',[(isset($group_id) ? $group_id : 0),$process->id]))->with('flash_danger', 'There are still active clients assigned to that process.');
        }

        $process->destroy($processid);

        $process_type_id = $request->has('t') ? $request->input('t') : 1;
        $type_name = $process_type_id == 2 ? 'Related Parties Structure' : 'Process';

        return redirect(route('processes.index', [''=>(isset($group_id) ? $group_id : 0),'t' => $process_type_id]))->with(['flash_success' => $type_name.' deleted successfully.']);
    }

    public function processStepCount($process_id){
        $count = Step::where('process_id',$process_id)->count();

        return response()->json($count);
    }

    public function getProcessFirstStep(Request $request, $clientid,$processid){

        //$first_step = Step::where('process_id',$processid)->orderBy('order')->first();
        $first_step = ClientProcess::where('process_id',$processid)->where('client_id',$clientid)->first();

        return response()->json($first_step['step_id']);
    }

    public function getNewProcesses($clientid){

        $client = Client::find($clientid);

        return response()->json($client->startNewProcessDropdown());
    }

    public function getNewForms($clientid){

        $client = Client::find($clientid);

        return response()->json($client->startNewFormDropdown());
    }

    public function getProcesses(){
        $process = [];
        $processes = Process::orderBy('name')->get();
        array_push($process,['id'=>'0','name'=>'Select Process']);
        foreach ($processes as $p){
            array_push($process,['id'=>$p->id,'name'=>$p->name]);
        }
        return response()->json($process);
    }

    public function getProcesses2(){


        $processes = Process::orderBy('name')->get();
        $process[0] = 'Select Process';
        foreach ($processes as $p){
            $process[$p->id] = $p->name;
        }
        return $process;
    }

    public function copy(Request $request, Process $process)
    {
        $process = $process->load(['steps.activities.actionable','pgroup','process_area']);

        $process_new = new Process();
        $process_new->name = $request->name;
        $process_new->not_started_colour = $process->not_started_colour;
        $process_new->started_colour = $process->started_colour;
        $process_new->completed_colour = $process->completed_colour;
        $process_new->global = $process->global;
        $process_new->process_type_id = $process->process_type_id;
        $process_new->process_group_id = $request->process_group;
        $process_new->docfusion_process_id = $process->docfusion_process_id;
        $process_new->docfusion_template_id = $process->docfusion_template_id;
        $process_new->save();

        foreach ($process->steps as $step){
            $step_new = new Step();
            $step_new->name = $step->name;
            $step_new->order = $step->order;
            $step_new->process_id = $process_new->id;
            $step_new->colour = $step->colour;
            $step_new->group = $step->group;
            $step_new->signature = $step->signature;
            $step_new->save();

            foreach ($step->activities as $activity){

                $activity_new = new Activity();
                $activity_new->name = $activity->name;
                $activity_new->order = $activity->order;
                $activity_new->actionable_id = $activity->actionable_id;
                $activity_new->actionable_type = $activity->actionable_type;
                $activity_new->kpi = $activity->kpi;
                $activity_new->client_activity = $activity->client_activity;
                $activity_new->step_id = $step_new->id;
                $activity_new->threshold = $activity->threshold;
                $activity_new->weight = $activity->weight;
                $activity_new->marker = $activity->marker;
                $activity_new->dependant_activity_id = $activity->dependant_activity_id;
                $activity_new->user_id = $activity->user_id;
                $activity_new->comment = $activity->comment;
                $activity_new->value = $activity->value;
                $activity_new->procedure = $activity->procedure;
                $activity_new->grouped = $activity->grouped;
                $activity_new->grouping = $activity->grouping;
                $activity_new->default_value = $activity->default_value;
                $activity_new->tooltip = $activity->tooltip;
                $activity_new->show_tooltip = $activity->show_tooltip;
                $activity_new->report = $activity->report;
                $activity_new->client_bucket = $activity->client_bucket;
                $activity_new->position = $activity->position;
                $activity_new->level = $activity->level;
                $activity_new->color = $activity->color;
                $activity_new->future_date = $activity->future_date;
                $activity_new->text_content = $activity->text_content;
                $activity_new->height = $activity->height;
                $activity_new->width = $activity->width;
                $activity_new->alignment = $activity->alignment;
                $activity_new->multiple_selection = $activity->multiple_selection;
                $activity_new->save();
            }
        }

        foreach ($process->process_area as $parea){
            $process_area = new ProcessArea();
            $process_area->process_id = $process_new->id;
            $process_area->area_id = $parea->area_id;
            $process_area->office_id = $parea->office_id;
            $process_area->save();
        }

        $request->session()->put('flash_success','Process successfully copied.');

        return response()->json($process_new);
    }
    public function getActionableType($type)
    {
        //activity type hook
        switch ($type) {
            case 'heading':
                return 'App\ActionableHeading';
                break;
            case 'subheading':
                return 'App\ActionableSubheading';
                break;
            case 'content':
                return 'App\ActionableContent';
                break;
            case 'text':
                return 'App\ActionableText';
                break;
            case 'textarea':
                return 'App\ActionableTextarea';
                break;
            case 'percentage':
                return 'App\ActionablePercentage';
                break;
            case 'integer':
                return 'App\ActionableInteger';
                break;
            case 'amount':
                return 'App\ActionableAmount';
                break;
            case 'videoyoutube':
                return 'App\ActionableVideoYoutube';
                break;
            case 'videoupload':
                return 'App\ActionableVideoUpload';
                break;
            case 'imageupload':
                return 'App\ActionableImageUpload';
                break;
            case 'template_email':
                return 'App\ActionableTemplateEmail';
                break;
            case 'document_email':
                return 'App\ActionableDocumentEmail';
                break;
            case 'document':
                return 'App\ActionableDocument';
                break;
            case 'dropdown':
                return 'App\ActionableDropdown';
                break;
            case 'date':
                return 'App\ActionableDate';
                break;
            case 'boolean':
                return 'App\ActionableBoolean';
                break;
            case 'notification':
                return 'App\ActionableNotification';
                break;
            case 'multiple_attachment':
                return 'App\ActionableMultipleAttachment';
            default:
                abort(500, 'Error');
                break;
        }
    }

    public function createActionable($type)
    {
        //activity type hook
        switch ($type) {
            case 'heading':
                return ActionableHeading::create();
                break;
            case 'subheading':
                return ActionableSubheading::create();
                break;
            case 'content':
                return ActionableContent::create();
                break;
            case 'text':
                return ActionableText::create();
                break;
            case 'textarea':
                return ActionableTextarea::create();
                break;
            case 'percentage':
                return ActionablePercentage::create();
                break;
            case 'integer':
                return ActionableInteger::create();
                break;
            case 'amount':
                return ActionableAmount::create();
                break;
            case 'videoupload':
                return ActionableVideoUpload::create();
                break;
            case 'imageupload':
                return ActionableImageUpload::create();
                break;
            case 'videoyoutube':
                return ActionableVideoYoutube::create();
                break;
            case 'template_email':
                return ActionableTemplateEmail::create();
                break;
            case 'document_email':
                return ActionableDocumentEmail::create();
                break;
            case 'document':
                return ActionableDocument::create();
                break;
            case 'dropdown':
                return ActionableDropdown::create();
                break;
            case 'date':
                return ActionableDate::create();
                break;
            case 'boolean':
                return ActionableBoolean::create();
                break;
            case 'notification':
                return ActionableNotification::create();
                break;
            case 'multiple_attachment':
                return ActionableMultipleAttachment::create();
            default:
                abort(500, 'Error');
                break;
        }
    }
}