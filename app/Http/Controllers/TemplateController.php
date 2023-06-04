<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\RelatedParty;
use App\Template;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Process;
use App\Step;
use App\Client;

class TemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $templates = Template::with('user');

        if ($request->has('q')) {
            $templates->where('name', 'LIKE', "%" . $request->input('q') . "%");
        }

        return view('templates.index')->with(['templates' => $templates->get()]);
    }

    public function create()
    {
        $parameters = [
           'process' => Process::with('steps.activities.actionable')->where('process_type_id','1')->orderBy('name')->pluck('name','id')->prepend('Please select','0')
        ];

        return view('templates.create')->with($parameters);
    }

    public function store(StoreTemplateRequest $request)
    {
        $template = new Template;
        $name = '';
        if ($request->hasFile('file')) {
            //$request->file('file')->store('templates');
            //ToDo: The above save every file as .bin, Please fix if you have a better way of uploading documents
            $file = $request->file('file');
            $name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".".$file->getClientOriginalExtension();
            $stored = $file->storeAs('templates', $name);
        }

        $template->name = $request->input('name');
        $template->file = $name;
        $template->template_type_id = $request->input('ttype');
        $template->user_id = auth()->id();
        $template->save();

        return redirect(route('templates.index'))->with('flash_success', 'Template uploaded successfully');
    }

    public function show(Template $template)
    {
        //
    }

    public function edit(Template $template)
    {
        $parameters = [
            'template' => $template,
            'process' => Process::with('steps.activities.actionable')->orderBy('name')->pluck('name','id')->prepend('Please select','0')
        ];

        return view('templates.edit')->with($parameters);
    }

    public function update(Template $template, UpdateTemplateRequest $request)
    {
        if ($request->hasFile('file')) {
            //$request->file('file')->store('templates');
            $file = $request->file('file');
            $name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".".$file->getClientOriginalExtension();
            $stored = $file->storeAs('templates', $name);

            $template->file = $name;
        }

        $template->process_id = $request->input('process');
        $template->name = $request->input('name');
        $template->template_type_id = $request->input('ttype');
        $template->save();

        return redirect(route('templates.index'))->with('flash_success', 'Template updated successfully');
    }

    public function destroy($id)
    {
        //DB::table("templates")->delete($id);
        Template::destroy($id);
        //File::delete($request->input('q'));
        return redirect(route('templates.index'))->with('flash_success', 'Template deleted successfully');
    }

    public function email(Template $template, Client $client){
        $email = $client->email;

        return "sent";
    }

    public function getClientVars()
    {
        $client = Client::first();
        /*$related_party = RelatedParty::first();*/

        $activity_array = array();

        foreach(collect($client)->toArray() as $column_name => $value) {
            $exclude = ['referrer_id','introducer_id','deleted_at','created_at','updated_at','office_id','process_id','step_id','is_progressing','not_progressing_date','completed_at','needs_approval','id','business_unit_id','trigger_type_id','is_qa','hash_first_name','hash_last_name','hash_company','hash_cif_code','hash_contact','hash_company_registration_number','hash_email','hash_id_number','consultant_id','project_id','committee_id','qa_consultant','viewed','completed','completed_by','out_of_scope','work_item_qa','work_item_qa_date'];
            if(!in_array($column_name,$exclude)) {
                array_push($activity_array,[
                    'step' => 'Client',
                    'name' => ucwords(str_replace('_',' ',$column_name)),
                    'variable' => 'client.' . $column_name
                ]);
            }
        }

        array_push($activity_array,[
            'step' => 'Client',
            'name' => ucwords('Business Unit'),
            'variable' => 'client.business_unit'
        ]);

        array_push($activity_array,[
            'step' => 'Client',
            'name' => ucwords('Committee'),
            'variable' => 'client.committee'
        ]);

        array_push($activity_array,[
            'step' => 'Client',
            'name' => ucwords('Trigger Type'),
            'variable' => 'client.trigger_type'
        ]);

        array_push($activity_array,[
            'step' => 'Client',
            'name' => ucwords('Out of Scope'),
            'variable' => 'client.out_of_scope'
        ]);

        array_push($activity_array,[
            'step' => 'Client',
            'name' => ucwords('Project'),
            'variable' => 'client.project'
        ]);

        /*foreach(collect($related_party)->toArray() as $column_name => $value) {
            $exclude = ['referrer_id','introducer_id','deleted_at','created_at','updated_at','office_id','process_id','step_id','is_progressing','not_progressing_date','completed_at','needs_approval','id','business_unit_id','trigger_type_id','is_qa','hash_first_name','hash_last_name','hash_company','hash_cif_code','hash_contact','hash_company_registration_number','hash_email','hash_id_number','consultant_id','project_id','committee_id','qa_consultant','viewed','completed','completed_by','out_of_scope','work_item_qa','work_item_qa_date','client_id','related_party_parent_id'];
            if(!in_array($column_name,$exclude)) {
                array_push($activity_array,[
                    'step' => 'Related Party',
                    'name' => ucwords(str_replace('_',' ',$column_name)),
                    'variable' => 'related_party.' . $column_name
                ]);
            }
        }
        array_push($activity_array,[
            'step' => 'Related Party',
            'name' => ucwords('Business Unit'),
            'variable' => 'related_party.business_unit'
        ]);

        array_push($activity_array,[
            'step' => 'Related Party',
            'name' => ucwords('Committee'),
            'variable' => 'related_party.committee'
        ]);

        array_push($activity_array,[
            'step' => 'Related Party',
            'name' => ucwords('Trigger Type'),
            'variable' => 'related_party.trigger_type'
        ]);

        array_push($activity_array,[
            'step' => 'Related Party',
            'name' => ucwords('Out of Scope'),
            'variable' => 'related_party.out_of_scope'
        ]);

        array_push($activity_array,[
            'step' => 'Related Party',
            'name' => ucwords('Project'),
            'variable' => 'related_party.project'
        ]);*/

        return $activity_array;


    }

    public function getVars($process_id)
    {
        $steps =  Step::with('activities.actionable')->where('process_id',$process_id)->get();

        $activity_array = array();

        foreach ($steps as $step){
            foreach($step["activities"] as $activity){
                array_push($activity_array,[
                    'step' => $step->name,
                    'name' => $activity->name,
                    'variable' => 'activity.'.strtolower(str_replace(' ','_',$activity->name))
                ]);
            }
        }

        return $activity_array;


    }
}
