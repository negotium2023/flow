<?php

namespace App\Http\Controllers;

use App\ActionableBooleanData;
use App\ActionableDateData;
use App\ActionableDocumentData;
use App\ActionableDropdownData;
use App\ActionableDropdownItem;
use App\ActionableTextareaData;
use App\ActionableTextData;
use App\Activity;
use App\ActivityInClientBasket;
use App\ActivityLog;
use App\ActivityStepVisibilityRule;
use App\ActivityVisibilityRule;
use App\Client;
use App\ClientHelper;
use App\ClientVerification;
use App\ClientVisibleActivity;
use App\ClientVisibleStep;
use App\Config;
use App\Document;
use App\EmailTemplate;
use App\FormInputAmountData;
use App\FormInputBooleanData;
use App\FormInputDateData;
use App\FormInputDropdownData;
use App\FormInputIntegerData;
use App\FormInputPercentageData;
use App\FormInputTextareaData;
use App\FormInputTextData;
use App\Forms;
use App\FormSection;
use App\HelperFunction;
use App\Log;
use App\Mail\ClientBasketEmail;
use App\Mail\ClientSaved;
use App\Step;
use App\User;
use App\Template;
use App\WhatsappTemplate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Process;


class ClientBasketController extends Controller
{
    private $helper;
    public function __construct()
    {
        $this->middleware('auth')->except(['clientProgress', 'clientStoreProgress']);

        $this->helper = new ClientHelper();
    }
    public function show(Request $request,Client $client,Process $process,Step $step,$is_form)
    {
        $helper = new HelperFunction();

        $activity_rule = ActivityVisibilityRule::select('*')->get();

        $act_vis = [];
        $act_invis = [];
        if($activity_rule) {

            $aarray = array();

            $activity_visibility = ClientVisibleActivity::select('activity_id')->where('client_id',$client->id)->get();

            foreach ($activity_visibility as $act){
                array_push($aarray,$act->activity_id);
            }

            foreach ($activity_rule as $av) {
                if (in_array($av->activity_id,$aarray,true)) {
                    array_push($act_vis, $av->activity_id);
                } else {
                    array_push($act_invis, $av->activity_id);
                }
            }
        }

        $step_rule = ActivityStepVisibilityRule::select('*')->get();

        $step_vis = [];
        $step_invis = [];
        if($step_rule) {

            $sarray = array();

            $step_visibility = ClientVisibleStep::select('step_id')->where('client_id',$client->id)->get();

            foreach ($step_visibility as $ste){
                array_push($sarray,$ste->step_id);
            }

            foreach ($step_rule as $sv) {
                if (in_array($sv->activity_step,$sarray,true)) {
                    array_push($step_vis, $sv->activity_step);
                } else {
                    array_push($step_invis, $sv->activity_step);
                }
            }
        }

        $parameters = [
            'client' => $client,
            'view_process_dropdown' => $client->clientProcessIfActivitiesExist(),
            'process_id'=>$process->id,
            'step'=>$step,
            'path' => $helper->getPath($request)['path'],
            'path_route' => $helper->getPath($request)['path_route'],
            'steps' => $this->helper->steps_data($client, $process),
            'activity_invisibil' => $act_invis,
            'step_invisibil' => $step_invis,
            'message_users' => User::where('id', '!=', Auth::id())->get(),
            'client_details' => $this->helper->clientDetails($client, 1)['forms'],
            'client_emails' => $this->helper->clientEmails($client, 1),
            'in_details_basket' => $this->helper->detailedClientBasket($client, 1)['cd'],
            'whatsapp_templates' => WhatsappTemplate::pluck('name','id')->prepend('Select',''),
            'is_form'=>$is_form
        ];

        return view('client.client-basket')->with($parameters);
    }

    public function clientBasketActivities(Client $client, Process $process)
    {
        //Client Basket Details
        $c_details_data = $this->helper->detailedClientBasket($client);
        $c_details_type = ['App\FormInputHeading', 'App\FormInputHeading'];
        $client_bascket_details = $this->formatClientBasket($c_details_data, 'inputs', $c_details_type);

        //Client Basket Activities
        $c_activity_data = $this->helper->getClientBasketActivities($client, $process);
        $c_activity_type = ['App\FormInputHeading', 'App\FormInputHeading'];
        $client_bascket_activities = $this->formatClientBasket($c_activity_data, 'activity', $c_activity_type);

        return response()->json([
            'client_basket_activities' => $client_bascket_activities,
            'client_basket_details' => $client_bascket_details
        ]);
    }

    private function formatClientBasket($data, $column, array $type){
        return collect($data)->map(function ($item) use($column, $type){
            return [
                'title' => $item['heading'],
                'body' => collect($item[$column])->map(function ($act) use($type){
                    $header = ($act->input_type == $type[0])?true:false;
                    $subHeader = ($act->input_type == $type[1])?true:false;
                    return [
                        'id' => $act->id,
                        'name' => $act->name,
                        'header' => $header,
                        'subHeader' => $subHeader
                    ];
                })
            ];
        });
    }

    public function includeActivityInClientBasket(Request $request)
    {
        $activity = ActivityInClientBasket::where('client_id', $request->client_id)->where('activity_id', $request->activity_id)->first()?? new ActivityInClientBasket();
        $activity->client_id = $request->client_id;
        $activity->activity_id = $request->activity_id;
        $activity->process_id = $request->process_id;
        $activity->in_client_basket = $request->status;
        $activity->save();

        $act = Activity::find($request->activity_id, ['name']);
        if($request->has('all') && $request->input('all') == 1) {
            $message = $request->status ? 'All activities were successfully included in the basket' : 'All activities were successfully removed from the basket';
        } else {
            $message = $request->status ? $act->name . ' included in the basket successfully' : $act->name . ' removed from the basket successfully';
        }
        return response()->json(['success'=> $message]);
    }

    public function clientProgress(Request $request,$client_id, $process_id, $step_id)
    {

        $is_valid = 0;

        $email = ($request->has('email') ? $request->input('email') : null);
        $client = ($request->has('client') ? $request->input('client') : null);
        $password= ($request->has('pwd') ? $request->input('pwd') : null);

        $client_verification = ClientVerification::where('login_ip_address',request()->ip())->where('last_session',Session::getId())->where('client_id',$client_id)->first();

        if(!$client_verification){
            $is_valid = 0;

            $verification = ClientVerification::where('client_id',$client)->where('email',$email)->where('password',$password)->first();

            if(!$verification){
                $is_valid = 0;
            } else {

                $v = ClientVerification::find($verification->id);
                $v->last_session = Session::getId();
                $v->login_ip_address = request()->ip();
                $v->save();

                $is_valid = 1;
            }
        } else {
            $is_valid = 1;
        }
        //$client_verification = ClientVerification::where('client_id',$client)->where('email',$email)->where('password',$password)->get();

        if($request->session()->get('path_route') != null) {

            $path = '1';
            $path_route = $request->session()->get('path_route');
        } else {
            $request->session()->forget('path_route');
            $path = '0';
            $path_route = '';
        }

        //dd($step["activities"]);
        $client = Client::withTrashed()->find($client_id);

        $client->with('process.office.area.region.division');

        $step = Step::find($step_id);
        $client_progress = $client->process->getStageHex(0);

        if($client->step_id == $step->id)
            $client_progress = $client->process->getStageHex(1);

        if($client->step_id > $step->id)
            $client_progress = $client->process->getStageHex(2);

        $steps = Step::where('process_id', $client->process_id)->orderBy('order','asc')->get();
        $c_step_order = Step::where('id',$client->step_id)->withTrashed()->first();
//dd($step["activities"]);
        $step_data = [];
        foreach ($steps as $a_step):
            $progress_color = $client->process->getStageHex(0);
            $step_stage = 0;

            if($c_step_order->order == $a_step->order) {
                $progress_color = $client->process->getStageHex(1);
                $step_stage = 1;
            }


            if($c_step_order->order > $a_step->order) {
                $progress_color = $client->process->getStageHex(2);
                $step_stage = 2;
            }


            $tmp_step = [
                'id' => $a_step->id,
                'name' => $a_step->name,
                'process_id' => $a_step->process_id,
                'progress_color' => $progress_color,
                'order' => $a_step->order,
                'stage' => $step_stage
            ];

            array_push($step_data, $tmp_step);

        endforeach;


        $max_step = Step::orderBy('order','desc')->where('process_id', $client->process_id)->first();


        $n_step = Step::select('id')->orderBy('order','asc')->where('process_id', $client->process_id)->where('order','>',$step->order)->whereNull('deleted_at')->first();

        $next_step = $step->id;

        if($next_step == $max_step->id)
            $next_step = $max_step->id;
        else
            $next_step = (isset($n_step->id) ? $n_step->id : $step->id);
        $template_email_options = EmailTemplate::orderBy('name')->pluck('name', 'id');

        $activity_comment = array();

        $in_basket = ActivityInClientBasket::select('activity_id')->where('client_id',$client_id)->where('process_id',$process_id)->where('in_client_basket',1)->get();

        $activities = $client->getClientBasketActivities($process_id);

        $details = [];
        $sections = [];
        $cd = [];

        $form = Forms::find($client->crm_id);
        if($form){
            $details = $form->getClientDetailsInputValues($client->id,$form->id);

            $sections = FormSection::with('form_section_inputs')->where('form_id', $client->crm_id)->get();

            $cd = $this->helper->clientBucketDetailIds($sections ,$client);

            $section_names_in_basket = $sections->keyBy('name')->filter(function ($step) use ($form){ return $step->form_id == $form->id; })->map(function ($step) use ($cd){
                return $step->form_section_inputs->filter(function ($activity) use ($cd){
                    return in_array($activity->id, $cd);
                });
            })->filter(function ($step){
                return count($step) > 0;
            });
        }
        $cb = $this->helper->clientBucketActivityIds($steps, $client, $client->process_id);


        $steps_names_in_basket = $steps->keyBy('name')->filter(function ($step) use ($process_id){ return $step->process_id == $process_id; })->map(function ($step) use ($cb){
            return $step->activities->filter(function ($activity) use ($cb){
                return in_array($activity->id, $cb);
            });
        })->filter(function ($step){
            return count($step) > 0;
        });

        $activity_rule = ActivityVisibilityRule::select('*')->get();

        $act_vis = [];
        $act_invis = [];
        if($activity_rule) {

            $aarray = array();

            $activity_visibility = ClientVisibleActivity::select('activity_id')->where('client_id',$client->id)->get();

            foreach ($activity_visibility as $act){
                array_push($aarray,$act->activity_id);
            }

            foreach ($activity_rule as $av) {
                if (in_array($av->activity_id,$aarray,true)) {
                    array_push($act_vis, $av->activity_id);
                } else {
                    array_push($act_invis, $av->activity_id);
                }
            }
        }

        $step_rule = ActivityStepVisibilityRule::select('*')->get();

        $step_vis = [];
        $step_invis = [];
        if($step_rule) {

            $sarray = array();

            $step_visibility = ClientVisibleStep::select('step_id')->where('client_id',$client->id)->get();
//dd($step_visibility);
            foreach ($step_visibility as $ste){
                array_push($sarray,$ste->step_id);
            }
//dd($sarray);
            foreach ($step_rule as $sv) {
                if (in_array($sv->activity_step,$sarray,true)) {
                    array_push($step_vis, $sv->activity_step);
                } else {
                    array_push($step_invis, $sv->activity_step);
                }
            }
        }

        $parameters = [
            'config'=> Config::first(),
            'activities' => $activities,
            'details' => $details,
            'clientid' => $client->id,
            'client' => $client,
            'activity_comment' => $activity_comment,
            'step' => $step,
            'active' => $step,
            'max_step' => $max_step->id,
            'next_step' => $next_step,
            'process_progress' => $client->getProcessStepProgress($step),
            'view_process_dropdown' => $client->clientProcessIfActivitiesExist(),
            'steps' => $step_data,
            'users' => User::select(DB::raw("CONCAT(first_name,' ',COALESCE(`last_name`,'')) AS full_name"), 'id')->orderBy('first_name')->orderBy('last_name')->pluck('full_name', 'id'),
            'documents' => Document::orderBy('name')->where('client_id', $client->id)->orWhere('client_id', null)->pluck('name', 'id'),
            'document_options' => Document::orderBy('name')->where('client_id', $client->id)->orWhere('client_id', null)->pluck('name', 'id'),
            'templates' => Template::where('template_type_id','2')->orderBy('name')->pluck('name', 'id'),
            'client_progress' => $client_progress,
            'template_email_options' => $template_email_options,
            'path' => $path,
            'path_route' => $path_route,
            'qa_complete' => '',
            'client_basket_activities'=>$cb,
            'client_basket_details'=>$cd,
            'process_id' => $process_id,
            'in_basket' => collect($in_basket)->toArray(),
            'is_valid' => $is_valid,
            'steps_names_in_basket' => $steps_names_in_basket->keys()->toArray(),
            'section_names_in_basket' => (isset($section_names_in_basket) && count($section_names_in_basket) > 0 ? $section_names_in_basket->keys()->toArray() : []),
            'activity_invisibil' => $act_invis,
            'step_invisibil' => $step_invis
        ];

        return view('client.clientactivity')->with($parameters);
    }

    public function clientStoreProgress(Client $client,Request $request,$process_id,$step_id){
        if($request->has('details') && $request->input('details') == '1'){
            $forms = FormSection::where('form_id',$client->crm_id)->get();

            foreach($forms as $form) {
                $id = $client->id;
                $form_section = FormSection::find($form->id);
                $form_section = $form_section->load(['form_section_inputs.input.data' => function ($query) use ($id) {
                    $query->where('client_id', $id);
                }]);

                $all_activities_completed = false;
                foreach ($form_section->form_section_inputs as $activity) {
                    if (is_null($request->input($activity->id))) {
                        if ($request->input('old_' . $activity->id) != $request->input($activity->id)) {

                            if (is_array($request->input($activity->id))) {

                                $old = explode(',', $request->input('old_' . $activity->id));
                                $diff = array_diff($old, $request->input($activity->id));
                                //dd($diff);

                            } else {
                                $old = $request->input('old_' . $activity->id);

                            }

                            switch ($activity->input_type) {
                                case 'App\FormInputBoolean':
                                    FormInputBooleanData::where('form_input_boolean_id', $activity->input_id)->where('client_id', $client->id)->delete();

                                    break;
                                case 'App\FormInputDate':
                                    FormInputDateData::where('form_input_date_id', $activity->input_id)->where('client_id', $client->id)->delete();
                                    break;
                                case 'App\FormInputText':
                                    FormInputTextData::where('form_input_text_id', $activity->input_id)->where('client_id', $client->id)->delete();

                                    break;
                                case 'App\FormInputAmount':
                                    FormInputAmountData::where('form_amount_text_id', $activity->input_id)->where('client_id', $client->id)->delete();

                                    break;
                                case 'App\FormInputPercentage':
                                    FormInputPercentageData::where('form_input_percentage_id', $activity->input_id)->where('client_id', $client->id)->delete();

                                    break;
                                case 'App\FormInputInteger':
                                    FormInputIntegerData::where('form_input_integer_id', $activity->input_id)->where('client_id', $client->id)->delete();

                                    break;
                                case 'App\FormInputTextarea':
                                    FormInputTextareaData::where('form_input_textarea_id', $activity->input_id)->where('client_id', $client->id)->delete();

                                    break;
                                case 'App\FormInputDropdown':
                                    FormInputDropdownData::where('form_input_dropdown_id', $activity->input_id)->where('client_id', $client->id)->delete();

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
                        if (is_array($request->input($activity->id))) {

                            $old = explode(',', $request->input('old_' . $activity->id));
                            $diff = array_diff($old, $request->input($activity->id));
                            //dd($diff);

                        } else {
                            $old = $request->input('old_' . $activity->id);
                        }

                        switch ($activity->input_type) {
                            case 'App\FormInputBoolean':
                                FormInputBooleanData::where('client_id', $client->id)->where('Form_input_boolean_id', $activity->input_id)->where('data', $old)->delete();

                                FormInputBooleanData::insert([
                                    'data' => $request->input($activity->id),
                                    'form_input_boolean_id' => $activity->input_id,
                                    'client_id' => $client->id,
                                    'user_id' => 0,
                                    'duration' => 120,
                                    'created_at' => now()
                                ]);
                                break;
                            case 'App\FormInputDate':
                                FormInputDateData::insert([
                                    'data' => $request->input($activity->id),
                                    'form_input_date_id' => $activity->input_id,
                                    'client_id' => $client->id,
                                    'user_id' => 0,
                                    'duration' => 120,
                                    'created_at' => now()
                                ]);
                                break;
                            case 'App\FormInputText':

                                FormInputTextData::insert([
                                    'data' => $request->input($activity->id),
                                    'form_input_text_id' => $activity->input_id,
                                    'client_id' => $client->id,
                                    'user_id' => 0,
                                    'duration' => 120,
                                    'created_at' => now()
                                ]);
                                break;
                            case 'App\FormInputAmount':

                                FormInputAmountData::insert([
                                    'data' => $request->input($activity->id),
                                    'form_input_amount_id' => $activity->input_id,
                                    'client_id' => $client->id,
                                    'user_id' => 0,
                                    'duration' => 120,
                                    'created_at' => now()
                                ]);
                                break;
                            case 'App\FormInputPercentage':

                                FormInputPercentageData::insert([
                                    'data' => $request->input($activity->id),
                                    'form_input_percentage_id' => $activity->input_id,
                                    'client_id' => $client->id,
                                    'user_id' => 0,
                                    'duration' => 120,
                                    'created_at' => now()
                                ]);
                                break;
                            case 'App\FormInputInteger':

                                FormInputIntegerData::insert([
                                    'data' => $request->input($activity->id),
                                    'form_input_integer_id' => $activity->input_id,
                                    'client_id' => $client->id,
                                    'user_id' => 0,
                                    'duration' => 120,
                                    'created_at' => now()
                                ]);
                                break;
                            case 'App\FormInputTextarea':

                                FormInputTextareaData::insert([
                                    'data' => $request->input($activity->id),
                                    'form_input_textarea_id' => $activity->input_id,
                                    'client_id' => $client->id,
                                    'user_id' => 0,
                                    'duration' => 120,
                                    'created_at' => now()
                                ]);
                                break;
                            case 'App\FormInputDropdown':
                                foreach ($request->input($activity->id) as $key => $value) {
                                    if (in_array($value, $old, true)) {

                                    } else {
                                        FormInputDropdownData::insert([
                                            'form_input_dropdown_id' => $activity->input_id,
                                            'form_input_dropdown_item_id' => $value,
                                            'client_id' => $client->id,
                                            'user_id' => 0,
                                            'duration' => 120,
                                            'created_at' => now()
                                        ]);
                                    }

                                    if (!empty($diff)) {
                                        FormInputDropdownData::where('client_id', $client->id)->where('form_input_dropdown_id', $activity->input_id)->whereIn('form_input_dropdown_item_id', $diff)->delete();
                                    }
                                }
                                break;
                            default:
                                //todo capture defaults
                                break;
                        }

                    }
                }
            }
        }
        if($request->has('activities') && $request->input('activities') == '1'){
            $log = new Log;
            $log->client_id = $client->id;
            $log->user_id = 0;
            $log->save();

            $id = $client->id;
            $steps = Step::with(['activities.actionable.data' => function ($query) use ($id) {
                $query->where('client_id', $id);
            }])->get();

            foreach ($steps as $step) {
                $all_activities_completed = false;
                foreach ($step->activities as $activity) {

                    if (is_null($request->input($activity->id))) {
                        if ($request->input('old_' . $activity->id) != $request->input($activity->id)) {

                            if (is_array($request->input($activity->id))) {

                                $old = explode(',', $request->input('old_' . $activity->id));
                                $diff = array_diff($old, $request->input($activity->id));
                                //dd($diff);

                                foreach ($request->input($activity->id) as $key => $value) {
                                    $activity_log = new ActivityLog;
                                    $activity_log->log_id = $log->id;
                                    $activity_log->activity_id = $activity->id;
                                    $activity_log->activity_name = $activity->name;
                                    $activity_log->old_value = $request->input('old_' . $activity->id);
                                    $activity_log->new_value = $value;
                                    $activity_log->save();
                                }
                            } else {
                                $old = $request->input('old_' . $activity->id);

                                $activity_log = new ActivityLog;
                                $activity_log->log_id = $log->id;
                                $activity_log->activity_id = $activity->id;
                                $activity_log->activity_name = $activity->name;
                                $activity_log->old_value = $request->input('old_' . $activity->id);
                                $activity_log->new_value = $request->input($activity->id);
                                $activity_log->save();
                            }

                            switch ($activity->actionable_type) {
                                case 'App\ActionableBoolean':
                                    ActionableBooleanData::where('actionable_boolean_id', $activity->actionable_id)->where('client_id', $client->id)->delete();

                                    break;
                                case 'App\ActionableDate':
                                    ActionableDateData::where('actionable_date_id', $activity->actionable_id)->where('client_id', $client->id)->delete();

                                    break;
                                case 'App\ActionableText':

                                    ActionableTextData::where('actionable_text_id', $activity->actionable_id)->where('client_id', $client->id)->delete();

                                    break;
                                case 'App\ActionableTextarea':
                                    ActionableTextareaData::where('actionable_textarea_id', $activity->actionable_id)->where('client_id', $client->id)->delete();

                                    break;
                                case 'App\ActionableDropdown':
                                    ActionableDropdownData::where('actionable_dropdown_id', $activity->actionable_id)->where('client_id', $client->id)->delete();

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
                        if (is_array($request->input($activity->id))) {

                            $old = explode(',', $request->input('old_' . $activity->id));
                            $diff = array_diff($old, $request->input($activity->id));
                            //dd($diff);

                            foreach ($request->input($activity->id) as $key => $value) {
                                $activity_log = new ActivityLog;
                                $activity_log->log_id = $log->id;
                                $activity_log->activity_id = $activity->id;
                                $activity_log->activity_name = $activity->name;
                                $activity_log->old_value = $request->input('old_' . $activity->id);
                                $activity_log->new_value = $value;
                                $activity_log->save();
                            }
                        } else {
                            $old = $request->input('old_' . $activity->id);

                            $activity_log = new ActivityLog;
                            $activity_log->log_id = $log->id;
                            $activity_log->activity_id = $activity->id;
                            $activity_log->activity_name = $activity->name;
                            $activity_log->old_value = $request->input('old_' . $activity->id);
                            $activity_log->new_value = $request->input($activity->id);
                            $activity_log->save();
                        }

                        switch ($activity->actionable_type) {
                            case 'App\ActionableBoolean':
                                ActionableBooleanData::where('client_id', $client->id)->where('actionable_boolean_id', $activity->actionable_id)->where('data', $old)->delete();

                                ActionableBooleanData::insert([
                                    'data' => $request->input($activity->id),
                                    'actionable_boolean_id' => $activity->actionable_id,
                                    'client_id' => $client->id,
                                    'user_id' => 0,
                                    'duration' => 120,
                                    'created_at' => now()
                                ]);

                                ActivityVisibilityRule::where('preceding_activity',$activity->id)->where('activity_value',$request->input($activity->id))->update(['visible'=>1]);
                                ActivityVisibilityRule::where('preceding_activity',$activity->id)->where('activity_value','!=',$request->input($activity->id))->update(['visible'=>0]);
                                break;
                            case 'App\ActionableDate':
                                ActionableDateData::where('client_id', $client->id)->where('actionable_date_id', $activity->actionable_id)->where('data', $old)->delete();

                                ActionableDateData::insert([
                                    'data' => $request->input($activity->id),
                                    'actionable_date_id' => $activity->actionable_id,
                                    'client_id' => $client->id,
                                    'user_id' => 0,
                                    'duration' => 120,
                                    'created_at' => now()
                                ]);

                                ActivityVisibilityRule::where('preceding_activity',$activity->id)->where('activity_value',$request->input($activity->id))->update(['visible'=>1]);
                                ActivityVisibilityRule::where('preceding_activity',$activity->id)->where('activity_value','!=',$request->input($activity->id))->update(['visible'=>0]);
                                break;
                            case 'App\ActionableText':
                                ActionableTextData::where('client_id', $client->id)->where('actionable_text_id', $activity->actionable_id)->where('data', $old)->delete();

                                ActionableTextData::insert([
                                    'data' => $request->input($activity->id),
                                    'actionable_text_id' => $activity->actionable_id,
                                    'client_id' => $client->id,
                                    'user_id' => 0,
                                    'duration' => 120,
                                    'created_at' => now()
                                ]);

                                ActivityVisibilityRule::where('preceding_activity',$activity->id)->where('activity_value',$request->input($activity->id))->update(['visible'=>1]);
                                ActivityVisibilityRule::where('preceding_activity',$activity->id)->where('activity_value','!=',$request->input($activity->id))->update(['visible'=>0]);
                                break;
                            case 'App\ActionableTextarea':
                                ActionableTextareaData::where('client_id', $client->id)->where('actionable_textarea_id', $activity->actionable_id)->where('data', $old)->delete();

                                $replace1 = str_replace('&nbsp;', ' ', $request->input($activity->id));
                                $replace2 = str_replace('&ndash;', '-', $replace1);
                                $replace3 = str_replace('&bull;', '- ', $replace2);
                                $replace4 = str_replace('&ldquo;', '"', $replace3);
                                $replace5 = str_replace('&rdquo;', '"', $replace4);
                                $replace6 = str_replace("&rsquo;", "'", $replace5);
                                $replace7 = str_replace("&lsquo;", "'", $replace6);

                                $data = $replace7;

                                ActionableTextareaData::insert([
                                    'data' => $data,
                                    'actionable_textarea_id' => $activity->actionable_id,
                                    'client_id' => $client->id,
                                    'user_id' => 0,
                                    'duration' => 120,
                                    'created_at' => now()
                                ]);

                                ActivityVisibilityRule::where('preceding_activity',$activity->id)->where('activity_value',$request->input($activity->id))->update(['visible'=>1]);
                                ActivityVisibilityRule::where('preceding_activity',$activity->id)->where('activity_value','!=',$request->input($activity->id))->update(['visible'=>0]);
                                break;
                            case 'App\ActionableDropdown':
                                foreach ($request->input($activity->id) as $key => $value) {
                                    if (in_array($value, $old, true)) {

                                    } else {
                                        ActionableDropdownData::insert([
                                            'actionable_dropdown_id' => $activity->actionable_id,
                                            'actionable_dropdown_item_id' => $value,
                                            'client_id' => $client->id,
                                            'user_id' => 0,
                                            'duration' => 120,
                                            'created_at' => now()
                                        ]);

                                        $dropdown_value = ActionableDropdownItem::where('id',$value)->first();

                                        ActivityVisibilityRule::where('preceding_activity',$activity->id)->where('activity_value',$dropdown_value->name)->update(['visible'=>1]);
                                        ActivityVisibilityRule::where('preceding_activity',$activity->id)->where('activity_value','!=',$dropdown_value->name)->update(['visible'=>0]);
                                    }

                                    if (!empty($diff)) {
                                        ActionableDropdownData::where('client_id', $client->id)->where('actionable_dropdown_id', $activity->actionable_id)->whereIn('actionable_dropdown_item_id', $diff)->delete();
                                    }


                                }
                                break;
                            default:
                                //todo capture defaults
                                break;
                        }

                    }
                }
            }
            //Handle files
            foreach($request->files as $key => $file):
                $file_activity = Activity::find($key);
                switch($file_activity->actionable_type){
                    case 'App\ActionableDocument':
                        $afile = $request->file($key);
                        $name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".".$afile->getClientOriginalExtension();
                        $stored = $afile->storeAs('documents', $name);

                        $document = new Document;
                        $document->name = $file_activity->name;
                        $document->file = $name;
                        $document->user_id = 0;
                        $document->client_id = $client->id;
                        $document->save();

                        ActionableDocumentData::insert([
                            'actionable_document_id' => $file_activity->actionable_id,
                            'document_id' => $document->id,
                            'client_id' => $client->id,
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

        Mail::to(array_values(array_filter([$client->consultant->email??null, $client->introducer->email??null])))
            ->send(new ClientSaved($client,$process_id,$step_id));
        return redirect()->back()->with(['flash_success' => "Details successfully captured."]);
    }

    public function sendClientEmail(Request $request){

        $client = Client::where('id',$request->input('client_id'))->first();



        $err = 0;
        $msg = 'Email successfully sent to:<br />';

        $process_id = $request->input('process_id');
        $step_id = $request->input('step_id');
        $process_name = Process::where('id',$process_id)->first()->name;

        foreach ($request->input('emails') as $email) {
            if($email) {
                $password = $this->generateRandomString(8);

                $cv = new ClientVerification();
                $cv->client_id = $client->id;
                $cv->email = $email;
                $cv->password = $password;
                $cv->save();


                Mail::to(trim($email))->send(new ClientBasketEmail($client,$process_name,$process_id,$step_id,$password,$email));
                if (Mail::failures()) {
                    $err++;
                } else {
                    $msg .= $email.'<br />';
                }
            }
        }


        // check for failures
        if ($err != 0) {
            return response()->json(['message' => 'Error']);
        }

        return response()->json(['message' => 'Success','success_msg' => $msg]);

    }

    function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
