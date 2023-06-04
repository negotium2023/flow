<?php

namespace App\Http\Controllers;

use App\Activity;
use App\ActivityInClientBasket;
use App\FormCategory;
use App\FormInputDropdownItem;
use App\FormInputRadioItem;
use App\FormInputCheckboxItem;
use App\FormLog;
use App\Forms;
use App\FormSection;
use App\FormSectionInputInClientBasket;
use App\FormSectionInputs;
use App\User;
use Illuminate\Http\Request;
use App\Client;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Step;
use App\Log;
use App\FormInputTextData;
use App\FormInputTextareaData;
use App\FormInputDropdownData;
use App\FormInputDateData;
use App\FormInputBooleanData;
use App\FormInputCheckboxData;
use App\FormInputRadioData;
use App\FormInputDocumentData;
use App\ClientForm;
use PhpOffice\PhpWord\Writer\PDF\DomPDF;
use PhpOffice\PhpWord\Settings;
use Illuminate\Support\Facades\Response;

class FormsController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
        //return $this->middleware('auth')->except('index');
    }

    public function index(Request $request){

        $forms = Forms::get();

        $parameters = [
            'forms' => $forms
        ];

        return view('forms.index')->with($parameters);
    }

    public function create(Request $request){

        $categories = FormCategory::orderBy('name')->pluck('name','id');

        $parameters = [
            'categories' => $categories
        ];

        return view('forms.create')->with($parameters);
    }

    public function store(Request $request){
        $forms = new Forms;
        $forms->name = $request->input('name');
        $forms->category_id = $request->input('category');
        $forms->label = $request->input('label');
        $forms->save();

        return redirect(route('forms.show',$forms->id))->with('flash_success', 'Form created successfully');
    }

    public function edit($form){

        $form = Forms::find($form);
        $categories = FormCategory::orderBy('name')->pluck('name','id');

        //dd($formfield_array);

        $paramaters = [
            'form' => $form,
            'categories' => $categories
        ];

        return view('forms.edit')->with($paramaters);
    }

    public function update($formid,Request $request){
        $form = Forms::find($formid);

        $form->name = $request->input('name');
        $form->category_id = $request->input('category');
        $form->label = $request->input('label');
        $form->save();

        return redirect(route('forms.show',$form))->with('flash_success', 'Form updated successfully.');
    }

    public function show(Forms $form){

        $formfields = FormSection::with('form_section_input.input.data')->where('form_id',$form->id)->get();

        $parameters = [
            'forms' => $form->load('sections'),
            'form_sections' => FormSection::where('form_id',$form->id)->orderBy('order')->pluck('name','id')->prepend('Please Select','0'),
            'formfields' => $formfields
        ];

        return view('forms.show')->with($parameters);
    }

    public function destroy($formid){

        Forms::destroy($formid);

        return redirect(route('forms.index'))->with('flash_success', 'Form deleted successfully.');
    }

    public function DynamicForm(Client $client,$form_id,$section_id){

        /*$client = Client::find($client_id);*/

        if($section_id == 0) {
            $client_forms = ClientForm::where('id',$form_id)->first();
            $form = Forms::withTrashed()->where('id',$client_forms->dynamic_form_id)->first();
            $form_step_first = FormSection::where('form_id',$client_forms->dynamic_form_id)->orderBy('order')->first()->id;
            $form_section = FormSection::find($form_step_first);
        } else {
            $form = Forms::withTrashed()->where('id',$form_id)->first();
            $form_section = FormSection::find($section_id);
        }

        $step = Step::withTrashed()->find(1);
        $process_progress = $client->getProcessStepProgress($step);
        $form_progress = $client->getFormsStepProgress($form_section);

        $client_progress = $client->process->getStageHex(0);

        if($client->step_id == $step->id)
            $client_progress = $client->process->getStageHex(1);

        if($client->step_id > $step->id)
            $client_progress = $client->process->getStageHex(2);

        $steps = Step::where('process_id', $client->process_id)->orderBy('order','asc')->get();
        $c_step_order = Step::where('id',$client->step_id)->withTrashed()->first();

        $step_data = [];
        foreach ($steps as $a_step):
            $progress_color = $client->process->getStageHex(0);

            if($c_step_order->order == $a_step->order)
                $progress_color = $client->process->getStageHex(1);

            if($c_step_order->order > $a_step->order)
                $progress_color = $client->process->getStageHex(2);


            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'process_id' => $a_step->process_id,
                'progress_color' => $progress_color,
                'order' => $a_step->order
            ];

            array_push($step_data, $tmp_step);

        endforeach;

        if($section_id == 0) {
            $client_forms = ClientForm::where('id', $form_id)->first();
            $sections = FormSection::where('form_id', $client_forms->dynamic_form_id)->orderBy('order', 'asc')->get();
        } else {
            $sections = FormSection::where('form_id', $form_id)->orderBy('order', 'asc')->get();
        }

        $form_data = [];
        foreach ($sections as $a_step):
            $progress_color = $client->process->getStageHex(0);

            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'form_id' => $a_step->form_id,
                'progress_color' => $progress_color,
                'order' => $a_step->order
            ];

            array_push($form_data, $tmp_step);

        endforeach;


        return view('clients.forms.form')->with([
            'client' => $client,
            'step'=>$step,
            'active' => $step,
            'process_progress' => $process_progress,
            'form_progress' => $form_progress,
            'form_section' => $form_section,
            'view_process_dropdown' => $client->clientProcessIfActivitiesExist(),
            'steps' => $step_data,
            'forms' => $form_data,
            'form' => $form
        ]);
    }

    public function storeDynamicForm(Client $client, Request $request){
        //dd($request);
        if($request->has('form_section_id') && $request->input('form_section_id') != ''){
            $log = new Log;
            $log->client_id = $client->id;
            $log->user_id = auth()->id();
            $log->save();

            $id = $client->id;
            $form_section = FormSection::find($request->input('form_section_id'));
            $form_section = $form_section->load(['form_section_inputs.input.data' => function ($query) use ($id) {
                $query->where('client_id', $id);
            }]);

            $all_activities_completed = false;
            foreach ($form_section->form_section_inputs as $activity) {
                if(is_null($request->input($activity->id))){
                    if($request->input('old_'.$activity->id) != $request->input($activity->id)){

                        if(is_array($request->input($activity->id))){

                            $old = explode(',',$request->input('old_'.$activity->id));
                            $diff = array_diff($old,$request->input($activity->id));
                            //dd($diff);

                            foreach($request->input($activity->id) as $key => $value) {
                                $activity_log = new FormLog;
                                $activity_log->log_id = $log->id;
                                $activity_log->input_id = $activity->id;
                                $activity_log->input_name = $activity->name;
                                $activity_log->old_value = $request->input('old_' . $activity->id);
                                $activity_log->new_value = $value;
                                $activity_log->save();
                            }
                        } else {
                            $old = $request->input('old_'.$activity->id);

                            $activity_log = new FormLog;
                            $activity_log->log_id = $log->id;
                            $activity_log->input_id = $activity->id;
                            $activity_log->input_name = $activity->name;
                            $activity_log->old_value = $request->input('old_'.$activity->id);
                            $activity_log->new_value = $request->input($activity->id);
                            $activity_log->save();
                        }

                        switch ($activity->actionable_type) {
                            case 'App\FormInputBoolean':
                                FormInputBooleanData::where('input_boolean_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            case 'App\FormInputDate':
                                FormInputDateData::where('input_date_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            case 'App\FormInputText':
                                FormInputTextData::where('input_text_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            case 'App\FormInputTextarea':
                                FormInputTextareaData::where('input_textarea_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            case 'App\FormInputDropdown':
                                FormInputDropdownData::where('input_dropdown_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            case 'App\FormInputRadio':
                                FormInputRadioData::where('input_radio_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            case 'App\FormInputCheckbox':
                                FormInputCheckboxData::where('input_dropdown_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            case 'App\FormInputDocument':
                                FormInputDocumentData::where('input_document_id',$activity->actionable_id)->where('client_id',$client->id)->delete();

                                break;
                            default:
                                //todo capture defaults
                                break;
                        }
                    }
                }

                if ($request->has($activity->id) && !is_null($request->input($activity->id))) {
                    //If value did not change, do not save it again or add it to log
                    if ($request->input('old_' . $activity->id) == $request->input($activity->id)) {
                        continue;
                    }
                    if(is_array($request->input($activity->id))){

                        $old = explode(',',$request->input('old_'.$activity->id));
                        $diff = array_diff($old,$request->input($activity->id));
                        //dd($diff);

                        foreach($request->input($activity->id) as $key => $value) {
                            $activity_log = new FormLog;
                            $activity_log->log_id = $log->id;
                            $activity_log->input_id = $activity->id;
                            $activity_log->input_name = $activity->name;
                            $activity_log->old_value = $request->input('old_' . $activity->id);
                            $activity_log->new_value = $value;
                            $activity_log->save();
                        }
                    } else {
                        $old = $request->input('old_'.$activity->id);

                        $activity_log = new FormLog;
                        $activity_log->log_id = $log->id;
                        $activity_log->input_id = $activity->id;
                        $activity_log->input_name = $activity->name;
                        $activity_log->old_value = $request->input('old_'.$activity->id);
                        $activity_log->new_value = $request->input($activity->id);
                        $activity_log->save();
                    }

                    //activity type hook
                    //dd($request);
                    switch ($activity->input_type) {
                        case 'App\FormInputBoolean':
                            FormInputBooleanData::where('client_id',$client->id)->where('Form_input_boolean_id',$activity->input_id)->where('data',$old)->delete();

                            FormInputBooleanData::insert([
                                'data' => $request->input($activity->id),
                                'form_input_boolean_id' => $activity->input_id,
                                'client_id' => $client->id,
                                'user_id' => auth()->id(),
                                'duration' => 120,
                                'created_at' => now()
                            ]);
                            break;
                        case 'App\FormInputDate':
                            FormInputDateData::insert([
                                'data' => $request->input($activity->id),
                                'form_input_date_id' => $activity->input_id,
                                'client_id' => $client->id,
                                'user_id' => auth()->id(),
                                'duration' => 120,
                                'created_at' => now()
                            ]);
                            break;
                        case 'App\FormInputText':

                            FormInputTextData::insert([
                                'data' => $request->input($activity->id),
                                'form_input_text_id' => $activity->input_id,
                                'client_id' => $client->id,
                                'user_id' => auth()->id(),
                                'duration' => 120,
                                'created_at' => now()
                            ]);
                            break;
                        case 'App\FormInputTextarea':

                            FormInputTextareaData::insert([
                                'data' => $request->input($activity->id),
                                'form_input_textarea_id' => $activity->input_id,
                                'client_id' => $client->id,
                                'user_id' => auth()->id(),
                                'duration' => 120,
                                'created_at' => now()
                            ]);
                            break;
                        case 'App\FormInputDropdown':
                            foreach($request->input($activity->id) as $key => $value){
                                if(in_array($value,$old,true)) {

                                } else {
                                    FormInputDropdownData::insert([
                                        'form_input_dropdown_id' => $activity->input_id,
                                        'form_input_dropdown_item_id' => $value,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);
                                }

                                if(!empty($diff)){
                                    FormInputDropdownData::where('client_id',$client->id)->where('form_input_dropdown_id',$activity->input_id)->whereIn('form_input_dropdown_item_id',$diff)->delete();
                                }
                            }
                            break;
                        case 'App\FormInputCheckbox':
                            foreach($request->input($activity->id) as $key => $value){
                                if(in_array($value,$old,true)) {

                                } else {
                                    FormInputCheckboxData::insert([
                                        'form_input_checkbox_id' => $activity->input_id,
                                        'form_input_checkbox_item_id' => $value,
                                        'client_id' => $client->id,
                                        'user_id' => auth()->id(),
                                        'duration' => 120,
                                        'created_at' => now()
                                    ]);
                                }

                                if(!empty($diff)){
                                    FormInputCheckboxData::where('client_id',$client->id)->where('form_input_checkbox_id',$activity->input_id)->whereIn('form_input_checkbox_item_id',$diff)->delete();
                                }
                            }
                            break;
                        case 'App\FormInputRadio':
                            //foreach($request->input($activity->id) as $key => $value){
                            //dd($old);
                            FormInputRadioData::where('client_id',$client->id)->where('form_input_radio_id',$activity->input_id)->delete();
                            FormInputRadioData::insert([
                                'form_input_radio_id' => $activity->input_id,
                                'form_input_radio_item_id' => $request->input($activity->id),
                                'client_id' => $client->id,
                                'user_id' => auth()->id(),
                                'duration' => 120,
                                'created_at' => now()
                            ]);


                            /*if(!empty($diff)){
                                FormInputRadioData::where('client_id',$client->id)->where('form_input_radio_id',$activity->input_id)->whereIn('form_input_radio_item_id',$diff)->delete();
                            }*/
                            //}
                            break;
                        case 'App\FormInputDocument':

                            FormInputDocumentData::insert([
                                'data' => $request->input($activity->id),
                                'form_input_document_id' => $activity->input_id,
                                'client_id' => $client->id,
                                'user_id' => auth()->id(),
                                'duration' => 120,
                                'created_at' => now()
                            ]);
                            break;
                        default:
                            //todo capture defaults
                            break;
                    }

                }
            }
        }

        $form = Forms::withTrashed()->where('id',$form_section->form_id)->first();

        $client_form = ClientForm::where('dynamic_form_id',$form->id)->where('client_id',$client->id)->get();

        if(count($client_form) == 0) {
            $document = new ClientForm();
            $document->name = $form->name;
            $document->form_type = $form->name;
            //$document->file = $form->file;
            $document->file = str_replace(' ','_',$form->name). "_" . ($client->company != null ? str_replace(' ','_',$client->company) : $client->first_name.'_'.$client->last_name) . ".pdf";
            $document->user_id = auth()->id();
            $document->dynamic_form = '1';
            $document->dynamic_form_id = $form->id;
            $document->client_id = $client->id;

            $document->save();
        }

        $filename = str_replace(' ','_',$form->name). "_" . ($client->company != null ? str_replace(' ','_',$client->company) : $client->first_name.'_'.$client->last_name) . ".pdf";

        if(isset($document->id)){
            return redirect()->back()->with(['flash_success' => 'Form details captured.<br /><a href="'.route('forms.process',['client_id'=>$client->id,'form_id'=>$document->id]).'" target="_blank">'.$filename.'</a>']);
        } else {
            $document = ClientForm::where('client_id',$client->id)->where('dynamic_form_id',$form->id)->first()->id;
            return redirect()->back()->with(['flash_success' => 'Form details captured.<br /><a href="'.route('forms.process',['client_id'=>$client->id,'form_id'=>$document]).'" target="_blank">'.$filename.'</a>']);
        }

    }

    public function editDynamicForm(Client $client,$form_id,$section_id){
//create dynamic form entry
//        $client = Client::find($client_id);

        $client_form = ClientForm::where('id',$form_id)->first();
//dd($form_id);
        $form = Forms::where('id',$client_form->dynamic_form_id)->first();

        $form_step_first = FormSection::where('form_id',$client_form->dynamic_form_id)->orderBy('order')->first()->id;

        $form_section = FormSection::find($form_step_first);

        $step = Step::withTrashed()->find($client->step_id);

        $process_progress = $client->getProcessStepProgress($step);
        $form_progress = $client->getFormsStepProgress($form_section);

        $client_progress = $client->process->getStageHex(0);

        if($client->step_id == $step->id)
            $client_progress = $client->process->getStageHex(1);

        if($client->step_id > $step->id)
            $client_progress = $client->process->getStageHex(2);

        $steps = Step::where('process_id', $client->process_id)->orderBy('order','asc')->get();
        $c_step_order = Step::where('id',$client->step_id)->withTrashed()->first();

        $step_data = [];
        foreach ($steps as $a_step):
            $progress_color = $client->process->getStageHex(0);

            if($c_step_order->order == $a_step->order)
                $progress_color = $client->process->getStageHex(1);

            if($c_step_order->order > $a_step->order)
                $progress_color = $client->process->getStageHex(2);


            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'process_id' => $a_step->process_id,
                'progress_color' => $progress_color,
                'order' => $a_step->order
            ];

            array_push($step_data, $tmp_step);

        endforeach;

        $sections = FormSection::where('form_id',$client_form->dynamic_form_id)->orderBy('order','asc')->get();

        $form_data = [];
        foreach ($sections as $a_step):
            $progress_color = $client->process->getStageHex(0);

            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'form_id' => $a_step->form_id,
                'progress_color' => $progress_color,
                'order' => $a_step->order
            ];

            array_push($form_data, $tmp_step);

        endforeach;


        return view('clients.forms.form')->with([
            'client' => $client,
            'step'=>$step,
            'active' => $step,
            'process_progress' => $process_progress,
            'form_progress' => $form_progress,
            'form_section' => $form_section,
            'view_process_dropdown' => $client->clientProcessIfActivitiesExist(),
            'steps' => $step_data,
            'forms' => $form_data,
            'form' => $form
        ]);
    }

    public function deleteDynamicForm(Client $client,$form){

    }

    public function getFormFirstSection($form_id){
        $form = FormSection::where('form_id',$form_id)->orderBy('order')->first()->id;

        return $form;
    }

    public function includeInputInClientBasket(Request $request)
    {
        $client = Client::find($request->input('client_id'));

        if($request->has('checked_values')) {
            foreach ($request->input('checked_values') as $value) {
                $activity = FormSectionInputInClientBasket::where('client_id', $request->input("client_id"))->where('input_id', $value)->first() ?? new FormSectionInputInClientBasket();
                $activity->client_id = $request->input("client_id");
                $activity->input_id = $value;
                $activity->form_id = $client->crm_id;
                $activity->in_client_basket = 1;
                $activity->save();
            }
        }

        if($request->has('nonchecked_values')) {
            foreach ($request->input('nonchecked_values') as $value) {
                $activity = FormSectionInputInClientBasket::where('client_id', $request->input("client_id"))->where('input_id', $value)->first() ?? new FormSectionInputInClientBasket();
                $activity->client_id = $request->input("client_id");
                $activity->input_id = $value;
                $activity->form_id = $client->crm_id;
                $activity->in_client_basket = 0;
                $activity->save();
            }
        }

        if($request->has('input_id')) {
                $activity = FormSectionInputInClientBasket::where('client_id', $request->input("client_id"))->where('input_id', $request->input("input_id"))->first();
                $activity->client_id = $request->input("client_id");
                $activity->input_id = $request->input("input_id");
                $activity->form_id = $client->crm_id;
                $activity->in_client_basket = 0;
                $activity->save();

            $message = 'The selected client detail was successfully removed from the basket';

            return response()->json(['message' => '1','success'=> $message]);
        }

        $message = 'All selected client details were successfully added to the basket';

        return response()->json(['message' => '1','success'=> $message]);
    }
}
