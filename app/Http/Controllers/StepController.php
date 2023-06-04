<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use App\ActionableBoolean;
use App\ActionableDate;
use App\ActionableDocument;
use App\ActionableDocumentEmail;
use App\ActionableDropdown;
use App\ActionableDropdownItem;
use App\ActionableContent;
use App\ActionableHeading;
use App\ActionableImageUpload;
use App\ActionableMultipleAttachment;
use App\ActionableNotification;
use App\ActionablePercentage;
use App\ActionableInteger;
use App\ActionableAmount;
use App\ActionableSubheading;
use App\ActionableTemplateEmail;
use App\ActionableText;
use App\ActionableTextarea;
use App\ActivityMirrorValue;
use App\ActivityRule;
use App\ActivityStepVisibilityRule;
use App\ActivityStyle;
use App\ActivityVisibilityRule;
use App\Currency;
use App\Forms;
use App\FormSection;
use App\FormSectionInputs;
use App\ProcessGroup;
use App\RelatedPartyText;
use App\RelatedPartyTextarea;
use App\RelatedPartyDropdown;
use App\RelatedPartyBoolean;
use App\RelatedPartyDate;
use App\RelatedPartyDropdownItem;
use App\RelatedPartyDocument;
use App\RelatedPartyDocumentEmail;
use App\RelatedPartyMultipleAttachment;
use App\RelatedPartyNotification;
use App\RelatedPartyTemplateEmail;
use App\ActionableVideoYoutube;
use App\ActionableVideoUpload;
use App\Activity;
use App\Process;
use App\Role;
use App\Step;
use App\Http\Requests\StoreStepRequest;
use App\Http\Requests\UpdateStepRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Log;

class StepController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Process $process)
    {
        $all_columns = Schema::getColumnListing('clients');
        asort($all_columns);
        $exclude_columns = [
            'id',
            'referrer_id',
            'introducer_id',
            'office_id',
            'process_id',
            'step_id',
            'is_progressing',
            'not_progressing_date',
            'needs_approval',
            'cif_code',
            'business_unit_id',
            'case_number',
            'is_qa',
            'qa_start_date',
            'qa_end_date',
            'qa_consultant',
            'hash_first_name',
            'hash_last_name',
            'hash_company',
            'hash_email',
            'hash_contact',
            'hash_id_number',
            'hash_cif_code',
            'hash_company_registration_number',
            'consultant_id',
            'committee_id',
            'project_id',
            'trigger_type_id',
            'instruction_date',
            'assigned_date',
            'viewed',
            'completed',
            'completed_date',
            'completed_by',
            'out_of_scope',
            'work_item_qa',
            'work_item_qa_date',
            'crm_id',
            'parent_id',
            'deleted_at'
        ];
        $get_columns = array_diff($all_columns, $exclude_columns);

        return view('steps.create')->with(['process' => $process->load('pgroup','office.area.region.division'),'fields'=>json_encode($get_columns)]);
    }

    public function store(Process $process, StoreStepRequest $request)
    {
        //dd($request->input());
        $step = new Step;
        $step->name = $request->input('name');
        $step->colour = $request->input('step_colour');
        $step->process_id = $process->id;
        $step->order = Step::where('process_id', $process->id)->max('order') + 1;
        $step->group = $request->input('group_step');
        $step->signature = $request->input('signature_step');
        $step->save();

        $step_id = $step->id;

        //loop over each activity input
        foreach ($request->input('activities') as $activity_key => $activity_input) {
            $activity = new Activity();

                $actionable = $this->createActionable($activity_input['type']);

                $activity->name = $activity_input['name'];
                $activity->order = $activity_key + 1;
                $activity->actionable_id = (isset($actionable->id) ? $actionable->id : 0);
                $activity->actionable_type = $this->getActionableType($activity_input['type']);
                $activity->kpi = (isset($activity_input['kpi']) && $activity_input['kpi'] == "on") ? 1 : null;
                $activity->comment = (isset($activity_input['comment']) && $activity_input['comment'] == "on") ? 1 : null;
                $activity->value = (isset($activity_input['value']) && $activity_input['value'] == "on") ? 1 : null;
                $activity->procedure= (isset($activity_input['procedure']) && $activity_input['procedure'] == "on") ? 1 : null;
                $activity->client_activity = (isset($activity_input['client']) && $activity_input['client'] == "on") ? 1 : null;
                $activity->step_id = $step_id;
                $activity->threshold = (isset($activity_input['threshold']) ? $this->getThresholdAsSeconds($activity_input['threshold_time'], $activity_input['threshold_type']) : 0);
                $activity->weight = (isset($activity_input['weight']) ? $activity_input['weight'] : 0);
                $activity->tooltip = (isset($activity_input['tooltip']) ? $activity_input['tooltip'] : '');
                $activity->show_tooltip = (isset($activity_input['show_tooltip']) && $activity_input['show_tooltip'] == "on") ? 1 : 0;
                //todo add dependant activities
                $activity->user_id = (!isset($activity_input['user']) || $activity_input['user'] == 0) ? null : $activity_input['user'];
                $activity->grouped = (isset($activity_input['grouping']) && $activity_input['grouping'] == "on") ? 1 : 0;
                $activity->grouping = (isset($activity_input['grouping_value'])) ? $activity_input['grouping_value'] : null;
                $activity->report = (isset($activity_input['report']) && $activity_input['report'] == "on") ? 1 : 0;

                    $activity->client_bucket = (isset($activity_input['client_bucket']) && $activity_input['client_bucket'] == "on") ? 1 : 0;

                $activity->future_date = (isset($activity_input['future_date']) && $activity_input['future_date'] == "on") ? 1 : 0;
                $activity->position = (isset($activity_input['position']) ? $activity_input['position'] : 0);
                $activity->level = (isset($activity_input['level']) ? $activity_input['level'] : 0);
                $activity->color = (isset($activity_input['color']) && $activity_input['color'] != '#hsla(0,0%,0%,0)' ? $activity_input['color'] : null);
                $activity->text_content = (isset($activity_input['text_content']) && $activity_input['text_content'] != '' ? $activity_input['text_content'] : null);
                $activity->height = (isset($activity_input['height']) ? $activity_input['height'] : null);
                $activity->width = (isset($activity_input['width']) ? $activity_input['width'] : null);
                $activity->alignment = (isset($activity_input['alignment']) ? $activity_input['alignment'] : null);
                $activity->multiple_selection = (isset($activity_input['multiple_selection']) && $activity_input['multiple_selection'] == "on" ? 1 : 0);
                $activity->save();

                //if activity is a dropdown type
                if ($activity_input['type'] == 'dropdown') {

                    //only add dropdown items if there is input
                    if (isset($activity_input['dropdown_items'])) {

                        //loop over each dropdown item
                        foreach ($activity_input['dropdown_items'] as $dropdown_item) {
                            $actionable_dropdown_item = new ActionableDropdownItem;
                            $actionable_dropdown_item->actionable_dropdown_id = $actionable->id;
                            $actionable_dropdown_item->name = $dropdown_item;
                            $actionable_dropdown_item->save();
                        }
                    }
                }
            }

        return redirect(route('processes.show', [(isset($process->pgroup->id) ? $process->pgroup->id : '0'),$process->id]))->with('flash_success', 'Step added successfully.');
    }

    public function edit(Step $step)
    {
        $step->load('process.pgroup','process.office.area.region.division', 'activities');

        $activities_array = [];
        foreach ($step->activities as $activity) {
            $threshold = [
                'time' => $activity->threshold,
                'type' => 'seconds'
            ];

            if ($activity->threshold > 60) {
                $threshold = [
                    'time' => $activity->threshold / 60,
                    'type' => 'minutes'
                ];
            }

            if ($activity->threshold > 60 * 60) {
                $threshold = [
                    'time' => $activity->threshold / (60 * 60),
                    'type' => 'hours'
                ];
            }

            if ($activity->threshold > 60 * 60 * 24) {
                $threshold = [
                    'time' => $activity->threshold / (60 * 60 * 24),
                    'type' => 'days'
                ];
            }

            if($activity->currency_type != null || $activity->currency_type != '0') {
                $currencies = Currency::orderBy('currency')->where('id',$activity->currency_type)->first();
            } else {
                $currencies = ['id'=>0,'symbol'=> 'Please Select'];
            }

                $activity_array = [
                    'id' => $activity->id,
                    'step_id' => $activity->step_id,
                    'name' => $activity->name,
                    'type' => $activity->getTypeName(),
                    'client' => $activity->client_activity,
                    'kpi' => ($activity->kpi ==1 ? true : false),
                    'comment' => $activity->comment,
                    'value' => $activity->value,
                    'procedure' => $activity->procedure,
                    'is_dropdown_items_shown' => false,
                    'showPercentage' => false,
                    'uploadPercentage'=>'0',
                    'tooltip' => $activity->tooltip,
                    'show_tooltip' => ($activity->show_tooltip == 1 ? true : false),
                    'is_tooltip_shown' => false,
                    'is_text_content_shown' => false,
                    'is_grouping_items_shown' => ($step["group"] == "1" ? true : false),
                    'is_default_value_shown' => false,
                    'grouping_value' => ($activity->grouping != null ? $activity->grouping : 0),
                    'use_default_value' => ($activity->default_value != null ? 1 : ''),
                    'default_value' => ($activity->default_value != null ? $activity->default_value : ''),
                    'old_value' => ($activity->default_value != null ? $activity->default_value : ''),
                    'weight' => $activity->weight,
                    'dropdown_item' => '',
                    'dropdown_items' => [],
                    'files' => [],
                    'file_item' => '',
                    'threshold' => $threshold,
                    'user' => $activity->user_id ?? 0,
                    'report' => ($activity->report == "1" ? true : false),
                    'client_bucket' => ($activity->client_bucket == "1" ? true : false),
                    'position' => $activity->position,
                    'level' => $activity->level,
                    'color' => $activity->color,
                    'future_date' => ($activity->future_date == "1" ? true : false),
                    'show_styles'=>false,
                    'show_rules' => false,
                    'show_mirror' => false,
                    'show_process_rules' => false,
                    'show_step_rules' => false,
                    'show_activity_rules' => false,
                    'show_mirror_crm' => false,
                    'show_mirror_activity' => false,
                    'show_mirror_default' => false,
                    'text_content'=>$activity->text_content,
                    'height'=>$activity->height,
                    'width'=>$activity->width,
                    'alignment'=>$activity->alignment,
                    'mapped_field'=>$activity->mapped_field,
                    'multiple_selection'=>($activity->multiple_selection == "1" ? true : false),
                    'currencies' => $currencies["id"] ?? 0,
                ];

                $style_array = [];
                $styles = ActivityStyle::where('activity_id',$activity->id)->get();

                foreach ($styles as $style){
                    $style_array[] = [
                        'styleid'=>$style->id,
                        'boldtext'=>($style->bold == 1 ? true : false),
                        'italic'=>($style->italic == 1 ? true : false),
                        'underline'=>($style->underline == 1 ? true : false),
                        'fontpt'=>$style->font_size,
                        'fontfamily'=>$style->font_family
                    ];
                }

                $activity_array['styles'] = (isset($style_array) && count($style_array) > 0 ? $style_array : [['boldtext'=>null,'italic'=>null,'underline'=>null,'fontpt'=>null,'fontfamily'=>null]]);

                $rule_array = [];
                $rules = ActivityRule::where('activity_id',$activity->id)->get();

                foreach ($rules as $rule){
                    $rule_array[] = ['rule_id'=>$rule->id,'rule_value'=>$rule->activity_value,'rule_process'=>$rule->activity_process,'rule_step'=>$rule->activity_step,'processs'=>Process::orderBy('name')->pluck('name','id'),'stepss'=>Step::where('process_id',$rule->activity_process)->orderBy('order')->pluck('name','id')];
                }
                $activity_array['rules'] = (isset($rule_array) && count($rule_array) > 0 ? $rule_array : [['rule_value'=>'','rule_process'=>'','rule_step'=>'','processs'=>Process::orderBy('name')->pluck('name','id'),'stepss'=>'']]);


            $arule_array = [];
            $arules = ActivityVisibilityRule::where('activity_id',$activity->id)->get();

            foreach ($arules as $arule){

                $activity2 = Activity::find($arule->preceding_activity);

                $act = Activity::where('step_id',$activity->step_id)->where('id','!=',$activity->id)->where('order','<',$activity->order)->orderBy('order')->pluck('name','id');

                if($activity2->getTypeName() == 'dropdown'){
                    $arule_array[] = ['activities'=>(count($act) > 0  ? $act : ''),'dropdown'=>true,'boolean'=>false,'text'=>false,'dropdownitems'=>(isset($activity2->actionable->items) ? $activity2->actionable->items->pluck('name')->toArray() : null),'activity_id'=>$arule->preceding_activity,'activity_value'=>$arule->activity_value];
                } elseif($activity2->getTypeName() == 'boolean' || $activity2->getTypeName() == 'notification' || $activity2->getTypeName() == 'multiple_attachment' || $activity2->getTypeName() == 'document' || $activity2->getTypeName() == 'document'){
                    $arule_array[] = ['activities'=>(count($act) > 0  ? $act : ''),'dropdown'=>false,'boolean'=>true,'text'=>false,'dropdownitems'=>null,'activity_id'=>$arule->preceding_activity,'activity_value'=>$arule->activity_value];
                } else {
                    $arule_array[] = ['activities'=>(count($act) > 0  ? $act : ''),'dropdown'=>false,'boolean'=>false,'text'=>true,'dropdownitems'=>null,'activity_id'=>$arule->preceding_activity,'activity_value'=>$arule->activity_value];
                }
            }

            $activity_array['arules'] = (isset($arule_array) && count($arule_array) > 0 ? $arule_array : [['activities'=>Activity::where('step_id',$activity->step_id)->where('id','!=',$activity->id)->where('order','<',$activity->order)->orderBy('order')->pluck('name','id'),'dropdown'=>false,'boolean'=>false,'text'=>true,'dropdownitems'=>'','activity_id'=>'','activity_value'=>'']]);

            $srule_array = [];
            $srules = ActivityStepVisibilityRule::where('activity_id',$activity->id)->get();

            foreach ($srules as $srule){

                $steps = Step::where('process_id',$activity->step->process->id)->where('id','!=',$activity->step_id)->orderBy('order')->pluck('name','id');

                if($activity->getTypeName() == 'dropdown'){
                    $srule_array[] = ['steps'=>(count($steps) > 0  ? $steps : ''),'dropdown'=>true,'boolean'=>false,'text'=>false,'dropdownitems'=>(isset($activity->actionable->items) ? $activity->actionable->items->pluck('name')->toArray() : null),'step_id'=>$srule->activity_step,'activity_value'=>$srule->activity_value];
                } elseif($activity->getTypeName() == 'boolean' || $activity->getTypeName() == 'notification' || $activity->getTypeName() == 'multiple_attachment' || $activity->getTypeName() == 'document' || $activity->getTypeName() == 'document'){
                    $srule_array[] = ['steps'=>(count($steps) > 0  ? $steps : ''),'dropdown'=>false,'boolean'=>true,'text'=>false,'dropdownitems'=>null,'step_id'=>$srule->activity_step,'activity_value'=>$srule->activity_value];
                } else {
                    $srule_array[] = ['steps'=>(count($steps) > 0  ? $steps : ''),'dropdown'=>false,'boolean'=>false,'text'=>true,'dropdownitems'=>null,'step_id'=>$srule->activity_step,'activity_value'=>$srule->activity_value];
                }
            }

            $activity_array['srules'] = (isset($srule_array) && count($srule_array) > 0 ? $srule_array : [['steps'=>Step::where('process_id',$activity->step->process->id)->where('id','!=',$activity->step_id)->orderBy('order')->pluck('name','id'),'dropdown'=>false,'boolean'=>false,'text'=>true,'dropdownitems'=>'','step_id'=>'','activity_value'=>'']]);

                if ($activity->getTypeName() == 'dropdown') {
                    $activity_array['dropdown_items'] = $activity->actionable->items->pluck('name')->toArray();
                }

            $amirror_array = [];
            $cmirror_array = [];
            $dmirror_array = [];
            $mirrors = ActivityMirrorValue::where('activity_id',$activity->id)->get();

            foreach ($mirrors as $mirror){
                if($mirror->mirror_type == 'activity'){
                    $amirror_array[] = [
                        'mirror_atype'=>$this->getActionableType($activity->getTypeName()),
                        'mirror_process'=>$mirror->mirror_process_id,
                        'mirror_step'=>$mirror->mirror_step_id,
                        'mirror_activity'=>$mirror->mirror_activity_id,
                        'processs'=>Process::orderBy('name')->pluck('name','id'),
                        'stepss'=>Step::where('process_id',$mirror->mirror_process_id)->orderBy('order')->pluck('name','id'),
                        'activitiess'=>Activity::where('step_id',$mirror->mirror_step_id)->orderBy('order')->pluck('name','id')
                    ];
                } elseif($mirror->mirror_type == 'crm'){
                    $cmirror_array[] = [
                        'mirror_atype'=>$this->getActionableType($activity->getTypeName()),
                        'mirror_process'=>$mirror->mirror_process_id,
                        'mirror_step'=>$mirror->mirror_step_id,
                        'mirror_activity'=>$mirror->mirror_activity_id,
                        'processs'=>Forms::orderBy('name')->pluck('name','id'),
                        'stepss'=>FormSection::where('form_id',$mirror->mirror_process_id)->orderBy('order')->pluck('name','id'),
                        'activitiess'=>FormSectionInputs::where('form_section_id',$mirror->mirror_step_id)->orderBy('order')->pluck('name','id')
                    ];
                } else {
                    $dmirror_array[] = [
                        'mirror_atype'=>$this->getActionableType($activity->getTypeName()),
                        'mirror_process'=>$mirror->mirror_process_id,
                        'mirror_step'=>$mirror->mirror_step_id,
                        'mirror_activity'=>$mirror->mirror_activity_id,
                        'mirror_value'=>$mirror->mirror_column,
                        'processs'=>['full_name'=>'Full Name','first_name'=>'First Name','last_name'=>'Surname','initials'=>'Initials','id_number'=>'ID/Password Number','email'=>'Email','contact'=>'Cellphone Number'],
                        'stepss'=>FormSection::where('form_id',$mirror->mirror_process_id)->orderBy('order')->pluck('name','id'),
                        'activitiess'=>FormSectionInputs::where('form_section_id',$mirror->mirror_step_id)->orderBy('order')->pluck('name','id')
                    ];
                }
            }

            $activity_array['amirrors'] = (isset($amirror_array) && count($amirror_array) > 0 ? $amirror_array : [['processs'=>Process::orderBy('name')->pluck('name','id'),'stepss'=>'','activitiess'=>'']]);
            $activity_array['cmirrors'] = (isset($cmirror_array) && count($cmirror_array) > 0 ? $cmirror_array : [['processs'=>Forms::orderBy('name')->pluck('name','id'),'stepss'=>'','activitiess'=>'']]);
            $activity_array['dmirrors'] = (isset($dmirror_array) && count($dmirror_array) > 0 ? $dmirror_array : [['processs'=>['full_name'=>'Full Name','first_name'=>'First Name','last_name'=>'Surname','initials'=>'Initials','id_number'=>'ID/Password Number','email'=>'Email','contact'=>'Cellphone Number'],'stepss'=>'','activitiess'=>'']]);

            array_push($activities_array, $activity_array);
        }

        $all_columns = Schema::getColumnListing('clients');
        asort($all_columns);
        $exclude_columns = [
            'id',
            'referrer_id',
            'introducer_id',
            'office_id',
            'process_id',
            'step_id',
            'is_progressing',
            'not_progressing_date',
            'needs_approval',
            'cif_code',
            'business_unit_id',
            'case_number',
            'is_qa',
            'qa_start_date',
            'qa_end_date',
            'qa_consultant',
            'hash_first_name',
            'hash_last_name',
            'hash_company',
            'hash_email',
            'hash_contact',
            'hash_id_number',
            'hash_cif_code',
            'hash_company_registration_number',
            'consultant_id',
            'committee_id',
            'project_id',
            'trigger_type_id',
            'instruction_date',
            'assigned_date',
            'viewed',
            'completed',
            'completed_date',
            'completed_by',
            'out_of_scope',
            'work_item_qa',
            'work_item_qa_date',
            'crm_id',
            'parent_id',
            'deleted_at'
        ];
        $get_columns = array_diff($all_columns, $exclude_columns);
///dd($activities_array);
// dd($get_columns);
        $paramaters = [
            'step' => $step,
            'process_id' => $step->process->id,
            'activities' => json_encode($activities_array),
            'fields' => json_encode($get_columns),
            'roles' => Role::orderBy('name')->get()
        ];


        return view('steps.edit')->with($paramaters);
    }

    public function update(Step $step, UpdateStepRequest $request)
    {
//dd($request->input());
        $process = Process::where('id',$request->input('process_id'))->first();
        $existing_step = Step::with('process.pgroup')->where('name',$step->name)->first();


        if($existing_step != null){
            $step_id = $existing_step->id;


            $step->name = $request->input('name');
            $step->colour = $request->input('step_colour');
            $step->group = $request->input('group_step');
            $step->signature = $request->input('signature_step');
            $step->save();
        }
        //dd($request->input('activities'));
        $pactivities = array();
        if($request->input("activities") != null) {
            foreach ($request->input("activities") as $activities) {
                //dd($activities);
                array_push($pactivities, $activities["id"]);
            }
        }
        Activity::where('step_id',$step->id)->whereNotIn('id',$pactivities)->delete();


        //loop over each activity input
        if($request->input("activities") != null) {
                Log::debug($request->input("activities"));
                foreach ($request->input('activities') as $activity_key => $activity_input) {

                    $activity = $step->activities()->where('id', $activity_input['id'])->get()->first();
                    if(isset($activity_input['type'])) {
                        $activity_type = $step->activities()->where('id', $activity_input['id'])->where('actionable_type', $this->getActionableType($activity_input['type']))->get()->first();
                    } else {
                        $activity_type = $step->activities()->where('id', $activity_input['id'])->where('actionable_type', $this->getActionableType($activity_input['hidden_type']))->get()->first();
                    }

                    //if there is a previous activity matching the name and type, reactivate it else create a new one
                    if (!$activity) {
                        $new_activity = true;
                        if (!$activity_type) {
                            $new_activity_type = true;
                            $activity = new Activity();
                            $actionable = $this->createActionable($activity_input['type']);
                        } else {
                            $new_activity_type = false;
                            $activity->restore();
                            $actionable = $activity->actionable;
                        }

                    } else {
                        $new_activity = false;
                        if (!$activity_type) {
                            $new_activity_type = true;
                            $activity = Activity::find($activity_input['id']);
                            $actionable = $this->createActionable($activity_input['type']);
                        } else {
                            $new_activity_type = false;
                            $activity->restore();
                            $actionable = $activity->actionable;
                        }

                    }

                    $activity->name = $activity_input['name'];
                    $activity->order = $activity_key + 1;
                    $activity->actionable_id = (isset($actionable->id) ? $actionable->id : $actionable);
                    $activity->actionable_type = $this->getActionableType($activity_input['type']);
                    $activity->kpi = (isset($activity_input['kpi']) && $activity_input['kpi'] == "on") ? 1 : null;
                    $activity->comment = (isset($activity_input['comment']) && $activity_input['comment'] == "on") ? 1 : null;
                    $activity->value = (isset($activity_input['value']) && $activity_input['value'] == "on") ? 1 : null;
                    $activity->procedure = (isset($activity_input['procedure']) && $activity_input['procedure'] == "on") ? 1 : null;
                    $activity->client_activity = (isset($activity_input['client']) && $activity_input['client'] == "on") ? 1 : null;
                    $activity->step_id = $step->id;
                    $activity->threshold = (isset($activity_input['threshold']) ? $this->getThresholdAsSeconds($activity_input['threshold_time'], $activity_input['threshold_type']) : 0);
                    $activity->weight = (isset($activity_input['weight']) ? $activity_input['weight'] : 0);
                    $activity->tooltip = (isset($activity_input['tooltip']) ? $activity_input['tooltip'] : null);
                    $activity->show_tooltip = (isset($activity_input['show_tooltip']) && $activity_input['show_tooltip'] == "on" ? 1 : 0);
                    //todo add dependant activities
                    $activity->user_id = (!isset($activity_input['user']) || $activity_input['user'] == 0) ? null : $activity_input['user'];
                    $activity->grouped = (isset($activity_input['grouping']) && $activity_input['grouping'] == "on") ? 1 : 0;
                    $activity->grouping = (isset($activity_input['grouping_value'])) ? $activity_input['grouping_value'] : null;
                    $activity->default_value = (isset($activity_input['default_value'])) ? $activity_input['default_value'] : null;
                    $activity->report = (isset($activity_input['report']) && $activity_input['report'] == "on") ? 1 : 0;

                        $activity->client_bucket = (isset($activity_input['client_bucket']) && $activity_input['client_bucket'] == "on") ? 1 : 0;
                        $activity->mapped_field = (isset($activity_input['mapped_field']) ? $activity_input['mapped_field'] : null);
                    $activity->future_date = (isset($activity_input['future_date']) && $activity_input['future_date'] == "on") ? 1 : 0;
                    $activity->position = (isset($activity_input['position']) ? $activity_input['position'] : 0);
                    $activity->level = (isset($activity_input['level']) ? $activity_input['level'] : 0);
                    $activity->color = (isset($activity_input['color']) && $activity_input['color'] != '#hsla(0,0%,0%,0)' ? $activity_input['color'] : null);
                    $activity->text_content = (isset($activity_input['text_content']) && $activity_input['text_content'] != '' ? $activity_input['text_content'] : null);
                    $activity->height = (isset($activity_input['height']) ? $activity_input['height'] : null);
                    $activity->width = (isset($activity_input['width']) ? $activity_input['width'] : null);
                    $activity->alignment = (isset($activity_input['alignment']) ? $activity_input['alignment'] : null);
                    $activity->multiple_selection = (isset($activity_input['multiple_selection']) && $activity_input['multiple_selection'] == "on" ? 1 : 0);
                    $activity->save();


                    //if activity is a dropdown type
                    if ($activity_input['type'] == 'dropdown') {

                        //delete all previous dropdown items
                        ActionableDropdownItem::where('actionable_dropdown_id', (isset($actionable->id) ? $actionable->id : $actionable))->delete();


                        //only add dropdown items if there is input
                        if (isset($activity_input['dropdown_items'])) {

                            //loop over each dropdown item
                            foreach ($activity_input['dropdown_items'] as $dropdown_item) {

                                //if this is a reactivated activity, search for all old dropdowns
                                if (!$new_activity_type) {

                                    //find if there already a dropdown item for that activity
                                    $item = $actionable->items()->withTrashed()->where('name', $dropdown_item)->get()->first();

                                    //if there is a previous dropdown item, reactivate it else create a new one
                                    if (!$item) {
                                        $actionable_dropdown_item = new ActionableDropdownItem;
                                        $actionable_dropdown_item->actionable_dropdown_id = $actionable->id;
                                        $actionable_dropdown_item->name = $dropdown_item;
                                        $actionable_dropdown_item->save();
                                    } else {
                                        $item->restore();
                                    }

                                } // otherwise create a new dropdown item without searching
                                else {
                                    $actionable_dropdown_item = new ActionableDropdownItem;
                                    $actionable_dropdown_item->actionable_dropdown_id = (isset($actionable->id) ? $actionable->id : $actionable);
                                    $actionable_dropdown_item->name = $dropdown_item;
                                    $actionable_dropdown_item->save();
                                }
                            }
                        }
                    }

                    ActivityStyle::where('activity_id',$activity->id)->delete();

                    if(isset($activity_input['styles'])){

                        foreach ($activity_input['styles'] as $style){
                            if(isset($style['styleid'])){
                                $styles = ActivityStyle::find($style['styleid']);
                            } else {
                                $styles = new ActivityStyle();
                                $styles->activity_id = $activity->id;
                            }
                            $styles->bold = (isset($style['bold']) && $style['bold'] == 'on' ? 1 : 0);
                            $styles->italic = (isset($style['italic']) && $style['italic'] == 'on' ? 1 : 0);
                            $styles->underline = (isset($style['underline']) && $style['underline'] == 'on' ? 1 : 0);
                            $styles->font_size = (isset($style['fontpt']) ? $style['fontpt'] : null);
                            $styles->font_family = (isset($style['fontfamily']) ? $style['fontfamily'] : null);
                            $styles->save();
                        }
                    }

                    ActivityRule::where('activity_id',$activity->id)->delete();

                    if(isset($activity_input['rules'])){

                        foreach ($activity_input['rules'] as $rule){
                            if(isset($rule['rule_value']) && isset($rule['rule_process']) && isset($rule['rule_step'])) {
                                $rules = new ActivityRule;
                                $rules->activity_id = $activity->id;
                                $rules->activity_value = $rule['rule_value'];
                                $rules->activity_process = $rule['rule_process'];
                                $rules->activity_step = $rule['rule_step'];
                                $rules->save();
                            }
                        }
                    }

                    ActivityVisibilityRule::where('activity_id',$activity->id)->delete();

                    if(isset($activity_input['arules'])) {
                        foreach ($activity_input['arules'] as $arule) {
                            if(isset($arule['activity_id']) && isset($arule['activity_value'])) {
                                $arules = new ActivityVisibilityRule;
                                $arules->activity_id = $activity->id;
                                $arules->preceding_activity = $arule['activity_id'];
                                $arules->activity_value = $arule['activity_value'];
                                $arules->activity_step = $activity->step_id;
                                $arules->save();
                            }
                        }
                    }

                    ActivityStepVisibilityRule::where('activity_id',$activity->id)->delete();

                    if(isset($activity_input['srules'])) {
                        foreach ($activity_input['srules'] as $srule) {
                            if(isset($srule['rule_step']) && isset($srule['rule_value'])) {
                                $arules = new ActivityStepVisibilityRule;
                                $arules->activity_id = $activity->id;
                                $arules->activity_value = $srule['rule_value'];
                                $arules->activity_step = $srule['rule_step'];
                                $arules->save();
                            }
                        }
                    }

                    ActivityMirrorValue::where('activity_id',$activity->id)->delete();

                    if(isset($activity_input['amirrors'])) {
                        foreach ($activity_input['amirrors'] as $mirrorvalue) {
                            if (isset($mirrorvalue['mirror_process']) && isset($mirrorvalue['mirror_step']) && isset($mirrorvalue['mirror_activity'])) {
                                $mirror = new ActivityMirrorValue;
                                $mirror->activity_id = $activity->id;
                                $mirror->mirror_type = 'activity';
                                $mirror->mirror_process_id = $mirrorvalue["mirror_process"];
                                $mirror->mirror_step_id = $mirrorvalue["mirror_step"];
                                $mirror->mirror_activity_id = $mirrorvalue["mirror_activity"];
                                $mirror->save();
                            }
                        }
                    }

                    if(isset($activity_input['cmirrors'])) {
                        foreach ($activity_input['cmirrors'] as $mirrorvalue) {
                            if (isset($mirrorvalue['mirror_process']) && isset($mirrorvalue['mirror_step']) && isset($mirrorvalue['mirror_activity'])) {
                                $mirror = new ActivityMirrorValue;
                                $mirror->activity_id = $activity->id;
                                $mirror->mirror_type = 'crm';
                                $mirror->mirror_process_id = $mirrorvalue["mirror_process"];
                                $mirror->mirror_step_id = $mirrorvalue["mirror_step"];
                                $mirror->mirror_activity_id = $mirrorvalue["mirror_activity"];
                                $mirror->save();
                            }
                        }
                    }

                    if(isset($activity_input['dmirrors'])) {
                        foreach ($activity_input['dmirrors'] as $mirrorvalue) {
                            if (isset($mirrorvalue['mirror_value'])) {
                                $mirror = new ActivityMirrorValue;
                                $mirror->activity_id = $activity->id;
                                $mirror->mirror_type = 'default';
                                $mirror->mirror_column = $mirrorvalue["mirror_value"];
                                $mirror->save();
                            }
                        }
                    }


                }
            return redirect(route('processes.show', [(isset($process->pgroup) && $process->pgroup->id > 0 ? $process->pgroup->id : '0'),$process->id]))->with('flash_success', 'Step added successfully.');
        }
        return redirect(route('processes.show', [(isset($process->pgroup) && $process->pgroup->id > 0 ? $process->pgroup->id : '0'),$process->id]))->with('flash_danger', 'Error.');
    }

    public function destroy(Step $step)
    {
        $process_group = $step->process->pgroup->id;
        $process_id = $step->process_id;

        $step->delete();

        return redirect(route('processes.show', [$process_group,$process_id]))->with('flash_success', 'Step successfully deleted.');
    }

    public function move(Step $step, Request $request)
    {
        //To do - set order properly
        $process_group = Process::where('id',$step->process->id)->first();

        if ($request->input('direction') == 'up') {
            $next_step = Step::where('process_id', $step->process->id)->where('order', '<', $step->order)->orderBy('order', 'desc')->first();

            if ($next_step) {
                $old_order = $step ->order;
                $new_order = $next_step ->order;

                $step->order = $new_order;
                $next_step->order = $old_order;
                $step->save();
            }
        }

        if ($request->input('direction') == 'down') {

            $next_step = Step::where('process_id', $step->process->id)->where('order', '>', $step->order)->orderBy('order', 'asc')->first();

            if ($next_step) {
                $old_order = $step ->order;
                $new_order = $next_step ->order;

                $step->order = $new_order;
                $next_step->order = $old_order;
                $step->save();
            }
        }

        return redirect(route('processes.show', ['group_id'=>$process_group->process_group_id,'process'=>$step->process]))->with('flash_success', 'Process updated successfully.');
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

    public function getThresholdAsSeconds($time, $type)
    {
        switch ($type) {
            case 'seconds':
                return $time;
                break;
            case 'minutes':
                return $time * 60;
                break;
            case 'hours':
                return $time * 60 * 60;
                break;
            case 'days':
                return $time * 60 * 60 * 24;
                break;
            default:
                return $time;
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

    public function getSteps(Request $request){

        $step = [];
        $steps = Step::where('process_id',$request->input('process_id'))->orderBy('order')->get();

        foreach ($steps as $p){
            $step[$p->id] = $p->name;
        }
        return $step;
    }

    public function getRemainingSteps(Request $request){

        $process = Step::where('id',$request->input('step_id'))->first()->id;

        $steps = Step::where('id','!=',$request->input('step_id'))->orderBy('order')->get();

        $step = array();

        foreach ($steps as $p){
            $step[$p->id] = $p->name;
        }
        return $step;
    }

    public function getDependantSteps(Request $request){

        $activity = Activity::find($request->input('activity_id'));

        if(strpos($request->input('activity_value'),',') !== false && strlen($request->input('activity_value') > 1)){
            if(isset($activity) && $activity->getTypeName() == 'dropdown') {
                $vars = explode(',', $request->input('activity_value'));

                $dropdownitems = ActionableDropdownItem::select('name')->whereIn('id', $vars)->get();

                $steps = ActivityStepVisibilityRule::select('activity_step')->whereIn('activity_value', collect($dropdownitems)->toArray())->get();
                if (count($steps) > 0) {
                    $nonsteps = ActivityStepVisibilityRule::select('activity_step')->whereNotIn('activity_value', collect($dropdownitems)->toArray())->whereNotIn('activity_id', collect($steps)->toArray())->get();
                } else {
                    $nonsteps = ActivityStepVisibilityRule::select('activity_step')->whereNotIn('activity_value', collect($dropdownitems)->toArray())->get();
                }
            }
        } else {
            if(isset($activity) && $activity->getTypeName() == 'dropdown') {
                $dropdownitems = ActionableDropdownItem::where('id', $request->input('activity_value'))->first();
            }

            if(isset($dropdownitems)) {
                $steps = ActivityStepVisibilityRule::select('activity_step')->where('activity_value', $dropdownitems->name)->get();
                if(count($steps) > 0) {
                    $nonsteps = ActivityStepVisibilityRule::select('activity_step')->where('activity_value','!=', $dropdownitems->name)->whereNotIn('activity_id', collect($steps)->toArray())->get();
                } else {
                    $nonsteps = ActivityStepVisibilityRule::select('activity_step')->where('activity_value','!=',$dropdownitems->name)->get();
                }
            } else {
                $steps = ActivityStepVisibilityRule::select('activity_step')->where('activity_value',$request->input('activity_value'))->get();
                if(count($steps) > 0) {
                    $nonsteps = ActivityStepVisibilityRule::select('activity_step')->where('activity_value','!=', $request->input('activity_value'))->whereNotIn('activity_id', collect($steps)->toArray())->get();
                } else {
                    $nonsteps = ActivityStepVisibilityRule::select('activity_step')->where('activity_value','!=', $request->input('activity_value'))->get();
                }
            }
        }


        $prec_steps = [];
        if(isset($steps)) {
            foreach ($steps as $step) {
                if(!in_array($step->activity_step,$prec_steps,true)) {
                    array_push($prec_steps, $step->activity_step);
                }
                /*ActivityVisibilityRule::where('id',$activity->id)->update(['visible'=>1]);*/
            }
        }

        $prec_nonsteps = [];
        if(isset($nonsteps)) {
            foreach ($nonsteps as $nonstep) {
                if(!in_array($nonstep->activity_step,$prec_nonsteps,true)) {
                    array_push($prec_nonsteps, $nonstep->activity_step);
                }
                /*ActivityVisibilityRule::where('id',$nonactivity->id)->update(['visible'=>0]);*/
            }
        }

        return response()->json(['result'=>'success','steps' => $prec_steps,'nonsteps' => $prec_nonsteps]);
    }
}