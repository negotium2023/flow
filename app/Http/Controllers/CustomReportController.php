<?php

namespace App\Http\Controllers;

use App\ActionableAmountData;
use App\ActionableBooleanData;
use App\ActionableDateData;
use App\ActionableDocumentData;
use App\ActionableDropdownItem;
use App\ActionableMultipleAttachmentData;
use App\ActionableNotificationData;
use App\ActionableTemplateEmailData;
use App\ActionableTextData;
use App\ActionableDropdownData;
use App\Activity;
use App\ActivityRelatedPartyLink;
use App\Committee;
use App\Config;
use App\Exports\CustomReportExport;
use App\FormInputBooleanData;
use App\FormInputDateData;
use App\FormInputDropdownData;
use App\FormInputTextareaData;
use App\FormInputTextData;
use App\Forms;
use App\FormSection;
use App\FormSectionInputs;
use App\Process;
use App\RelatedPartiesTree;
use App\RelatedParty;
use App\RelatedPartyBooleanData;
use App\RelatedPartyDateData;
use App\RelatedPartyDocumentData;
use App\RelatedPartyDropdownData;
use App\RelatedPartyMultipleAttachment;
use App\RelatedPartyMultipleAttachmentData;
use App\RelatedPartyNotificationData;
use App\RelatedPartyTemplateEmailData;
use App\RelatedPartyTextareaData;
use App\RelatedPartyTextData;
use App\Template;
use App\TriggerType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\CustomReport;
use App\CustomReportColumns;
use Illuminate\Support\Facades\Auth;
use App\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Step;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use App\ActionableTextareaData;
use Maatwebsite\Excel\Excel;

class CustomReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $parameters = [
            'reports' => CustomReport::orderBy('name')->with('user')->get()
        ];
        return view('customreports.index')->with($parameters);
    }

    public function create(){

        $configs = Config::first();

        $parameters = [
            'process' => Process::where('process_type_id',1)->pluck('name','id'),
            'crm' => Forms::orderBy('name')->pluck('name','id')
        ];
        return view('customreports.create')->with($parameters);
    }

    public function store(Request $request){
        $creport = new CustomReport();
        $creport->name = $request->input('name');
        $creport->process_id = ($request->input('group_step') == 'crm' ? $request->input('crm') : $request->input('process'));
        $creport->user_id = Auth::id();
        $creport->group_report = ($request->input('group_report') != null && $request->input('group_report') == 'on' ? 1 : 0);
        $creport->type = $request->input('group_step');
        $creport->save();

        $creport_id = $creport->id;

        foreach($request->input('activity') as $key => $value){
            $activity = new CustomReportColumns();
            $activity->custom_report_id = $creport_id;
            $activity->activity_id = $value;
            $activity->save();
        }

        return redirect(route('custom_report.index'))->with('flash_success', 'Custom report created successfully');
    }

    public function edit($custom_report_id){

        $report = CustomReport::where('id',$custom_report_id)->get();

        $parameters = [
            'reports' => $report,
            'process' => Process::where('process_type_id',1)->pluck('name','id'),
            'crm' => Forms::orderBy('name')->pluck('name','id')
        ];

        return view('customreports.edit')->with($parameters);
    }

    public function update($custom_report_id, Request $request){

        $creport = CustomReport::find($custom_report_id);
        $creport->name = $request->input('name');
        $creport->process_id = ($request->input('group_step') == 'crm' ? $request->input('crm') : $request->input('process'));
        $creport->user_id = Auth::id();
        $creport->group_report = ($request->input('group_report') != null && $request->input('group_report') == 'on' ? 1 : 0);
        $creport->type = $request->input('group_step');
        $creport->save();

        CustomReportColumns::where('custom_report_id',$custom_report_id)->delete();

        foreach($request->input('activity') as $key => $value){
            $activity = new CustomReportColumns();
            $activity->custom_report_id = $custom_report_id;
            $activity->activity_id = $value;
            $activity->save();
        }

        return redirect(route('custom_report.index'))->with('flash_success', 'Custom report updated successfully');
    }

    public function show(Request $request,$custom_report_id,$report_type){

        $request->session()->forget('path_route');

        $np = 0;
        $qa = 0;
        $total = 0;
        $rows = 0;

        $report_name = '';
        $report_columns = array();
        $activity_id = array();
        $rp_activity_id = array();
        $data = array();

        if($report_type == "process") {
            $report = CustomReport::with('custom_report_columns.activity_name')->where('id', $custom_report_id)->withTrashed()->first();

            $clients = Client::with(['process.steps2.activities.actionable.data'])->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'),
                DB::raw('ROUND(DATEDIFF(completed_at, created_at), 0) as completed_days'),
                DB::raw('CAST(AES_DECRYPT(`hash_company`, "Qwfe345dgfdg") AS CHAR(50)) hash_company'),
                DB::raw('CAST(AES_DECRYPT(`hash_first_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_first_name'),
                DB::raw('CAST(AES_DECRYPT(`hash_last_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_last_name'),
                DB::raw('CAST(AES_DECRYPT(`hash_cif_code`, "Qwfe345dgfdg") AS CHAR(50)) hash_cif_code'),
                DB::raw('CAST(`case_number` AS CHAR(50)) AS case_number')
            );

            if ($request->has('q') && $request->input('q') != '') {

                $clients = $clients->having('hash_company', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_first_name', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_last_name', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_cif_code', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('case_number', 'like', "%" . $request->input('q') . "%");
            }

            $clients = $clients->where('process_id', $report->process_id)->where('is_progressing', 1)->get();

            if ($request->has('user') && $request->input('user') != null) {
                $clients = $clients->filter(function ($client) use ($request) {
                    return $client->consultant_id == $request->input('user');
                });
            }

            if ($request->has('f') && $request->input('f') != '') {
                $p = $request->input('f');
                $clients = $clients->filter(function ($client) use ($p) {
                    return $client->instruction_date >= $p;
                });
            }

            if ($request->has('t') && $request->input('t') != '') {
                $p = $request->input('t');
                $clients = $clients->filter(function ($client) use ($p) {
                    return Carbon::parse($client->instruction_date)->format("Y-m-d") <= $p;
                });
            }

            if (isset($report) && $report->group_report == '0') {

                $report_name = $report->name;
                foreach ($report->custom_report_columns as $report_activity) {
                    array_push($report_columns, $report_activity->activity_name["name"]);
                    array_push($activity_id, $report_activity->activity_name["id"]);

                    $rp_activity = ActivityRelatedPartyLink::select('related_activity')->where('primary_activity', $report_activity->activity_name["id"])->first();

                    if ($rp_activity) {
                        array_push($rp_activity_id, $rp_activity->related_activity);
                    }
                }

                foreach ($clients as $client) {
                    if ($client) {
                        $data = [];

                        foreach ($activity_id as $key => $value) {
                            $activity = Activity::where('id', $value)->first();

                            switch ($activity["actionable_type"]) {
                                case 'App\ActionableBoolean':
                                    $yn_value = '';

                                    $data2 = ActionableBooleanData::where('client_id', $client->id)->where('actionable_boolean_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableDate':
                                    $data_value = '';

                                    $data2 = ActionableDateData::where('client_id', $client->id)->where('actionable_date_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if($data2){
                                        $data_value = $data2["data"];
                                    } else {
                                        $data_value = '';
                                    }

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableText':
                                    $data_value = '';

                                    $data2 = ActionableTextData::where('client_id', $client->id)->where('actionable_text_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if($data2){
                                        $data_value = $data2["data"];
                                    } else {
                                        $data_value = '';
                                    }

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableTextarea':
                                    $data_value = '';

                                    $data2 = ActionableTextareaData::where('client_id', $client->id)->where('actionable_textarea_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if($data2){
                                        $data_value = $data2["data"];
                                    } else {
                                        $data_value = '';
                                    }

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableDropdown':
                                    $data_value = '';

                                    $data2 = ActionableDropdownData::with('item')->where('client_id', $client->id)->where('actionable_dropdown_id', $activity->actionable_id)->get();

                                    if($data2){
                                        foreach ($data2 as $key => $value):
                                            if (count($data2) > 1) {
                                                if(isset($value["item"]["name"])){
                                                $data_value .= $value["item"]["name"] . ', ';
                                                }
                                            } else {
                                                if(isset($value["item"]["name"])){
                                                    $data_value .= $value["item"]["name"];
                                                } else {
                                                    $data_value .= '';
                                                }
                                            }
                                        endforeach;
                                        array_push($data, $data_value);
                                    } else {
                                        array_push($data, '');
                                    }
                                    break;
                                case 'App\ActionableDocument':
                                    $yn_value = '';

                                    $data2 = ActionableDocumentData::where('client_id', $client->id)->where('actionable_document_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["document_id"])) {
                                        $yn_value = "Yes";
                                    } else {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableTemplateEmail':
                                    $yn_value = '';

                                    $data2 = ActionableTemplateEmailData::where('client_id', $client->id)->where('actionable_template_email_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableNotification':
                                    $yn_value = '';

                                    $data2 = ActionableNotificationData::where('client_id', $client->id)->where('actionable_notification_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableMultipleAttachment':
                                    $yn_value = '';

                                    $data2 = ActionableMultipleAttachmentData::where('client_id', $client->id)->where('actionable_ma_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                        $client_data[$client->id] = [
                            'type' => 'P',
                            'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                            'id' => $client->id,
                            'process_id' => $client->process_id,
                            'step_id' => $client->step_id,
                            'case_nr' => $client->case_number,
                            'cif_code' => $client->cif_code,
                            'committee' => isset($client->committee) ? $client->committee->name : null,
                            'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                            'instruction_date' => $client->instruction_date,
                            'completed_at' => ($client->completed_at != null ? $client->completed_at : ''),
                            'date_submitted_qa' => $client->qa_start_date,
                            'assigned' => ($client->consultant_id != null ? 1 : 0),
                            'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                            'data' => $data
                        ];

                        $total++;
                    }
                }
            } else {
                $report_name = $report->name;
                foreach ($report->custom_report_columns as $report_activity) {

                    $rows = Activity::select(DB::raw("DISTINCT grouping"))->where('step_id', $report_activity->activity_name["step_id"])->where('grouping', '>', 0)->get()->count();

                    array_push($report_columns, $report_activity->activity_name["name"]);

                    if ($report_activity->activity_name["grouping"] > 0) {
                        array_push($activity_id, $report_activity->activity_name["id"]);
                    } else {
                        array_push($activity_id, $report_activity->activity_name["id"]);
                    }


                    $rp_activity = ActivityRelatedPartyLink::select('related_activity')->where('primary_activity', $report_activity->activity_name["id"])->first();

                    if ($rp_activity) {
                        array_push($rp_activity_id, $rp_activity->related_activity);
                    }
                }

                foreach ($clients as $client) {
                    if ($client) {
                        $data = [];

                        foreach ($activity_id as $key => $value) {
                            $activity = Activity::where('id', $value)->first();

                            switch ($activity["actionable_type"]) {
                                case 'App\ActionableBoolean':
                                    $yn_value = '';

                                    $data2 = ActionableBooleanData::where('client_id', $client->id)->where('actionable_boolean_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableDate':
                                    $data_value = '';

                                    $data2 = ActionableDateData::where('client_id', $client->id)->where('actionable_date_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if($data2){
                                        $data_value = $data2["data"];
                                    } else {
                                        $data_value = '';
                                    }

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableText':
                                    $data_value = '';

                                    $data2 = ActionableTextData::where('client_id', $client->id)->where('actionable_text_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    $data[$activity["id"]] = $data_value;
                                    //array_push($data, $data_value);
                                    break;
                                case 'App\ActionableTextarea':
                                    $data_value = '';

                                    $data2 = ActionableTextareaData::where('client_id', $client->id)->where('actionable_textarea_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableDropdown':
                                    $data_value = '';

                                    $data2 = ActionableDropdownData::with('item')->where('client_id', $client->id)->where('actionable_dropdown_id', $activity->actionable_id)->get();

                                    foreach ($data2 as $key => $value):
                                        if (count($data2) > 1) {
                                            $data_value .= $value["item"]["name"] . ', ';
                                        } else {
                                            $data_value .= $value["item"]["name"];
                                        }
                                    endforeach;
                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableDocument':
                                    $yn_value = '';

                                    $data2 = ActionableDocumentData::where('client_id', $client->id)->where('actionable_document_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["document_id"])) {
                                        $yn_value = "Yes";
                                    } else {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableTemplateEmail':
                                    $yn_value = '';

                                    $data2 = ActionableTemplateEmailData::where('client_id', $client->id)->where('actionable_template_email_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableNotification':
                                    $yn_value = '';

                                    $data2 = ActionableNotificationData::where('client_id', $client->id)->where('actionable_notification_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableMultipleAttachment':
                                    $yn_value = '';

                                    $data2 = ActionableMultipleAttachmentData::where('client_id', $client->id)->where('actionable_ma_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                        $client_data[$client->id] = [
                            'type' => 'P',
                            'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                            'id' => $client->id,
                            'process_id' => $client->process_id,
                            'step_id' => $client->step_id,
                            'case_nr' => $client->case_number,
                            'cif_code' => $client->cif_code,
                            'committee' => isset($client->committee) ? $client->committee->name : null,
                            'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                            'instruction_date' => $client->instruction_date,
                            'completed_at' => ($client->completed_at != null ? $client->completed_at : ''),
                            'date_submitted_qa' => $client->qa_start_date,
                            'assigned' => ($client->consultant_id != null ? 1 : 0),
                            'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                            'data' => $data
                        ];

                        $total++;
                    }
                }
            }
        }

        if($report_type == "crm"){
            $report = CustomReport::with('custom_report_columns.crm_name')->where('id',$custom_report_id)->withTrashed()->first();

            $clients = Client::with(['crm.sections.form_section_inputs.input.data'])->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'),
                DB::raw('ROUND(DATEDIFF(completed_at, created_at), 0) as completed_days'),
                DB::raw('CAST(AES_DECRYPT(`hash_company`, "Qwfe345dgfdg") AS CHAR(50)) hash_company'),
                DB::raw('CAST(AES_DECRYPT(`hash_first_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_first_name'),
                DB::raw('CAST(AES_DECRYPT(`hash_last_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_last_name'),
                DB::raw('CAST(AES_DECRYPT(`hash_cif_code`, "Qwfe345dgfdg") AS CHAR(50)) hash_cif_code'),
                DB::raw('CAST(`case_number` AS CHAR(50)) AS case_number')
            );

            if ($request->has('q') && $request->input('q') != '') {

                $clients = $clients->having('hash_company', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_first_name', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_last_name', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_cif_code', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('case_number', 'like', "%" . $request->input('q') . "%");
            }

            $clients = $clients->get();

            if ($request->has('user') && $request->input('user') != null) {
                $clients = $clients->filter(function ($client) use ($request) {
                    return $client->consultant_id == $request->input('user');
                });
            }

            if ($request->has('f') && $request->input('f') != '') {
                $p = $request->input('f');
                $clients = $clients->filter(function ($client) use ($p) {
                    return $client->instruction_date >= $p;
                });
            }

            if ($request->has('t') && $request->input('t') != '') {
                $p = $request->input('t');
                $clients = $clients->filter(function ($client) use ($p) {
                    return Carbon::parse($client->instruction_date)->format("Y-m-d") <= $p;
                });
            }

            if(isset($report) && $report->group_report == '0') {

                $report_name = $report->name;
                foreach ($report->custom_report_columns as $report_activity) {
                    if($report_activity->crm_name["input_type"] != 'App\FormInputSubheading' && $report_activity->crm_name["input_type"] != 'App\FormInputHeading') {
                        array_push($report_columns, $report_activity->crm_name["name"]);
                        array_push($activity_id, $report_activity->crm_name["id"]);
                    }
                }
//dd($report_columns);
                foreach ($clients as $client) {
                    if ($client) {
                        $data = [];

                        foreach ($activity_id as $key => $value) {
                            $activity = FormSectionInputs::where('id', $value)->first();

                            switch ($activity["input_type"]) {
                                case 'App\FormInputBoolean':
                                    $yn_value = '';

                                    $data2 = FormInputBooleanData::where('client_id', $client->id)->where('form_input_boolean_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputDate':
                                    $data_value = '';

                                    $data2 = FormInputDateData::where('client_id', $client->id)->where('form_input_date_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if($data2){
                                        $data_value = $data2["data"];
                                    } else {
                                        $data_value = '';
                                    }

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputText':
                                    $data_value = '';

                                    $data2 = FormInputTextData::where('client_id', $client->id)->where('form_input_text_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputTextarea':
                                    $data_value = '';

                                    $data2 = FormInputTextareaData::where('client_id', $client->id)->where('form_input_textarea_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputDropdown':
                                    $data_value = '';

                                    $data2 = FormInputDropdownData::with('item')->where('client_id', $client->id)->where('form_input_dropdown_id', $activity->input_id)->get();

                                    foreach ($data2 as $key => $value):
                                        if (count($data2) > 1) {
                                            $data_value .= $value["item"]["name"] . ', ';
                                        } else {
                                            $data_value .= $value["item"]["name"];
                                        }
                                    endforeach;
                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputDocument':
                                    $yn_value = '';

                                    $data2 = FormInputDocumentData::where('client_id', $client->id)->where('form_input_document_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["document_id"])) {
                                        $yn_value = "Yes";
                                    } else {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputTemplateEmail':
                                    $yn_value = '';

                                    $data2 = FormInputTemplateEmailData::where('client_id', $client->id)->where('form_input_template_email_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputNotification':
                                    $yn_value = '';

                                    $data2 = FormInputNotificationData::where('client_id', $client->id)->where('form_input_notification_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputMultipleAttachment':
                                    $yn_value = '';

                                    $data2 = FormInputMultipleAttachmentData::where('client_id', $client->id)->where('form_input_ma_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                        $client_data[$client->id] = [
                            'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                            'id' => $client->id,
                            'process_id' => $client->process_id,
                            'step_id' => $client->step_id,
                            'case_nr' => $client->case_number,
                            'cif_code' => $client->cif_code,
                            'committee' => isset($client->committee) ? $client->committee->name : null,
                            'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                            'instruction_date' => $client->instruction_date,
                            'completed_at' => ($client->completed_at != null ? $client->completed_at : ''),
                            'date_submitted_qa' => $client->qa_start_date,
                            'assigned' => ($client->consultant_id != null ? 1 : 0),
                            'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                            'data' => $data
                        ];

                        $total++;
                    }
                }
            } else {
                $report_name = $report->name;
                foreach ($report->custom_report_columns as $report_activity) {

                    $rows = FormSectionInputs::select(DB::raw("DISTINCT grouping"))->where('form_section_id',$report_activity->crm_name["form_section_id"])->where('grouping','>',0)->get()->count();

                    array_push($report_columns, $report_activity->crm_name["name"]);

                    if($report_activity->activity_name["grouping"] > 0) {
                        array_push($activity_id, $report_activity->crm_name["id"]);
                    } else {
                        array_push($activity_id, $report_activity->crm_name["id"]);
                    }
                }

                foreach ($clients as $client) {
                    if ($client) {
                        $data = [];

                        foreach ($activity_id as $key => $value) {
                            $activity = FormSectionInputs::where('id', $value)->first();

                            switch ($activity["actionable_type"]) {
                                case 'App\FormInputBoolean':
                                    $yn_value = '';

                                    $data2 = FormInputBooleanData::where('client_id', $client->id)->where('form_input_boolean_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputDate':
                                    $data_value = '';

                                    $data2 = FormInputDateData::where('client_id', $client->id)->where('form_input_date_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if($data2){
                                        $data_value = $data2["data"];
                                    } else {
                                        $data_value = '';
                                    }

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputText':
                                    $data_value = '';

                                    $data2 = FormInputTextData::where('client_id', $client->id)->where('form_input_text_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    $data[$activity["id"]] = $data_value;
                                    //array_push($data, $data_value);
                                    break;
                                case 'App\FormInputTextarea':
                                    $data_value = '';

                                    $data2 = FormInputTextareaData::where('client_id', $client->id)->where('form_input_textarea_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputDropdown':
                                    $data_value = '';

                                    $data2 = FormInputDropdownData::with('item')->where('client_id', $client->id)->where('form_input_dropdown_id', $activity->input_id)->get();

                                    foreach ($data2 as $key => $value):
                                        if (count($data2) > 1) {
                                            $data_value .= $value["item"]["name"] . ', ';
                                        } else {
                                            $data_value .= $value["item"]["name"];
                                        }
                                    endforeach;
                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputDocument':
                                    $yn_value = '';

                                    $data2 = FormInputDocumentData::where('client_id', $client->id)->where('form_input_document_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["document_id"])) {
                                        $yn_value = "Yes";
                                    } else {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputTemplateEmail':
                                    $yn_value = '';

                                    $data2 = FormInputTemplateEmailData::where('client_id', $client->id)->where('form_input_template_email_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputNotification':
                                    $yn_value = '';

                                    $data2 = FormInputNotificationData::where('client_id', $client->id)->where('form_input_notification_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputMultipleAttachment':
                                    $yn_value = '';

                                    $data2 = FormInputMultipleAttachmentData::where('client_id', $client->id)->where('input_ma_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                        $client_data[$client->id] = [
                            'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                            'id' => $client->id,
                            'process_id' => $client->process_id,
                            'step_id' => $client->step_id,
                            'case_nr' => $client->case_number,
                            'cif_code' => $client->cif_code,
                            'committee' => isset($client->committee) ? $client->committee->name : null,
                            'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                            'instruction_date' => $client->instruction_date,
                            'completed_at' => ($client->completed_at != null ? $client->completed_at : ''),
                            'date_submitted_qa' => $client->qa_start_date,
                            'assigned' => ($client->consultant_id != null ? 1 : 0),
                            'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                            'data' => $data
                        ];

                        $total++;
                    }
                }
            }

        }
// dd($client_data[3]);
        $step_names = Step::select('id', 'name', 'process_id')->get();
        // dd($step_names);

        $parameters = [
            'np' => $np,
            'qa' => $qa,
            'report_type' => $report_type,
            'report_id' => $custom_report_id,
            'report_name' => $report_name,
            'fields' => $report_columns,
            'clients' => (isset($client_data) ? $client_data : []),
            'committee' => Committee::orderBy('name')->pluck('name', 'id')->prepend('All committees', 'all'),
            'trigger' => TriggerType::orderBy('name')->pluck('name', 'id')->prepend('All trigger types', 'all'),
            'assigned_user' => Client::all()->keyBy('consultant_id')->map(function ($consultant){
                return isset($consultant->consultant)?$consultant->consultant->first_name.' '.$consultant->consultant->last_name:null;
            })->sort(),
            'activity' => '',
            'total' => $total,
            'step_names' => $step_names
        ];
        return view('customreports.show')->with($parameters);
    }

    public function destroy($custom_report_id){
        CustomReport::destroy($custom_report_id);
        return redirect()->route("custom_report.index")->with('flash_success','Custom report deleted successfully');
    }

    public function export(Excel $excel,Request $request,$custom_report_id,$report_type){

        $np = 0;
        $qa = 0;
        $total = 0;
        $rows = 0;

        $report_name = '';
        $report_columns = array();
        $activity_id = array();
        $rp_activity_id = array();
        $data = array();


        if($report_type == "process") {
            $report = CustomReport::with('custom_report_columns.activity_name')->where('id', $custom_report_id)->withTrashed()->first();

            $clients = Client::with(['referrer', 'process.steps2.activities.actionable.data', 'introducer', 'consultant', 'committee', 'trigger'])->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'),
                DB::raw('ROUND(DATEDIFF(completed_at, created_at), 0) as completed_days'),
                DB::raw('CAST(AES_DECRYPT(`hash_company`, "Qwfe345dgfdg") AS CHAR(50)) hash_company'),
                DB::raw('CAST(AES_DECRYPT(`hash_first_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_first_name'),
                DB::raw('CAST(AES_DECRYPT(`hash_last_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_last_name'),
                DB::raw('CAST(AES_DECRYPT(`hash_cif_code`, "Qwfe345dgfdg") AS CHAR(50)) hash_cif_code'),
                DB::raw('CAST(`case_number` AS CHAR(50)) AS case_number')
            );

            if ($request->has('q') && $request->input('q') != '') {

                $clients = $clients->having('hash_company', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_first_name', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_last_name', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_cif_code', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('case_number', 'like', "%" . $request->input('q') . "%");
            }

            // $clients = $clients->get();
            $clients = $clients->where('process_id', $report->process_id)->where('is_progressing', 1)->get();

            if ($request->has('user') && $request->input('user') != null) {
                $clients = $clients->filter(function ($client) use ($request) {
                    return $client->consultant_id == $request->input('user');
                });
            }

            if ($request->has('f') && $request->input('f') != '') {
                $p = $request->input('f');
                $clients = $clients->filter(function ($client) use ($p) {
                    return $client->instruction_date >= $p;
                });
            }

            if ($request->has('t') && $request->input('t') != '') {
                $p = $request->input('t');
                $clients = $clients->filter(function ($client) use ($p) {
                    return Carbon::parse($client->instruction_date)->format("Y-m-d") <= $p;
                });
            }

            if (isset($report) && $report->group_report == '0') {

                $report_name = $report->name;
                foreach ($report->custom_report_columns as $report_activity) {
                    array_push($report_columns, $report_activity->activity_name["name"]);
                    array_push($activity_id, $report_activity->activity_name["id"]);
                }

                foreach ($clients as $client) {
                    if ($client) {
                        $data = [];

                        foreach ($activity_id as $key => $value) {
                            $activity = Activity::where('id', $value)->first();

                            switch ($activity["actionable_type"]) {
                                case 'App\ActionableBoolean':
                                    $yn_value = '';

                                    $data2 = ActionableBooleanData::where('client_id', $client->id)->where('actionable_boolean_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableDate':
                                    $data_value = '';

                                    $data2 = ActionableDateData::where('client_id', $client->id)->where('actionable_date_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if($data2){
                                        $data_value = $data2["data"];
                                    } else {
                                        $data_value = '';
                                    }

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableText':
                                    $data_value = '';

                                    $data2 = ActionableTextData::where('client_id', $client->id)->where('actionable_text_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if($data2){
                                        $data_value = $data2["data"];
                                        } else {
                                            $data_value = '';
                                        }

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableTextarea':
                                    $data_value = '';

                                    $data2 = ActionableTextareaData::where('client_id', $client->id)->where('actionable_textarea_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if($data2){
                                    $data_value = $data2["data"];
                                    } else {
                                        $data_value = '';
                                    }

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableDropdown':
                                    $data_value = '';

                                    $data2 = ActionableDropdownData::with('item')->where('client_id', $client->id)->where('actionable_dropdown_id', $activity->actionable_id)->get();

                                    foreach ($data2 as $key => $value):
                                        if (count($data2) > 1) {
                                            if(isset($value["item"]["name"])){
                                                $data_value .= $value["item"]["name"] . ', ';
                                            } else {
                                                $data_value .= '';
                                            }
                                        } else {
                                            if(isset($value["item"]["name"])){
                                            $data_value .= $value["item"]["name"];
                                        } else {
                                            $data_value .= '';
                                        }
                                        }
                                    endforeach;
                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableDocument':
                                    $yn_value = '';

                                    $data2 = ActionableDocumentData::where('client_id', $client->id)->where('actionable_document_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["document_id"])) {
                                        $yn_value = "Yes";
                                    } else {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableTemplateEmail':
                                    $yn_value = '';

                                    $data2 = ActionableTemplateEmailData::where('client_id', $client->id)->where('actionable_template_email_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableNotification':
                                    $yn_value = '';

                                    $data2 = ActionableNotificationData::where('client_id', $client->id)->where('actionable_notification_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableMultipleAttachment':
                                    $yn_value = '';

                                    $data2 = ActionableMultipleAttachmentData::where('client_id', $client->id)->where('actionable_ma_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                        $client_data[$client->id] = [
                            'type' => 'P',
                            'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                            'id' => $client->id,
                            'step_id' => $client->step_id,
                            'case_nr' => $client->case_number,
                            'cif_code' => $client->cif_code,
                            'committee' => isset($client->committee) ? $client->committee->name : null,
                            'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                            'instruction_date' => $client->instruction_date,
                            'completed_at' => ($client->completed_at != null ? $client->completed_at : ''),
                            'date_submitted_qa' => $client->qa_start_date,
                            'assigned' => ($client->consultant_id != null ? 1 : 0),
                            'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                            'data' => $data
                        ];

                        $total++;
                    }
                }
            } else {
                $report_name = $report->name;
                foreach ($report->custom_report_columns as $report_activity) {

                    $rows = Activity::select(DB::raw("DISTINCT grouping"))->where('step_id', $report_activity->activity_name["step_id"])->where('grouping', '>', 0)->get()->count();

                    array_push($report_columns, $report_activity->activity_name["name"]);

                    if ($report_activity->activity_name["grouping"] > 0) {
                        array_push($activity_id, $report_activity->activity_name["id"]);
                    } else {
                        array_push($activity_id, $report_activity->activity_name["id"]);
                    }
                }

                foreach ($clients as $client) {
                    if ($client) {
                        $data = [];

                        foreach ($activity_id as $key => $value) {
                            $activity = Activity::where('id', $value)->first();

                            switch ($activity["actionable_type"]) {
                                case 'App\ActionableBoolean':
                                    $yn_value = '';

                                    $data2 = ActionableBooleanData::where('client_id', $client->id)->where('actionable_boolean_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableDate':
                                    $data_value = '';

                                    $data2 = ActionableDateData::where('client_id', $client->id)->where('actionable_date_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableText':
                                    $data_value = '';

                                    $data2 = ActionableTextData::where('client_id', $client->id)->where('actionable_text_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    $data[$activity["id"]] = $data_value;
                                    //array_push($data, $data_value);
                                    break;
                                case 'App\ActionableTextarea':
                                    $data_value = '';

                                    $data2 = ActionableTextareaData::where('client_id', $client->id)->where('actionable_textarea_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableDropdown':
                                    $data_value = '';

                                    $data2 = ActionableDropdownData::with('item')->where('client_id', $client->id)->where('actionable_dropdown_id', $activity->actionable_id)->get();

                                    foreach ($data2 as $key => $value):
                                        if (count($data2) > 1) {
                                            $data_value .= $value["item"]["name"] . ', ';
                                        } else {
                                            $data_value .= $value["item"]["name"];
                                        }
                                    endforeach;
                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableDocument':
                                    $yn_value = '';

                                    $data2 = ActionableDocumentData::where('client_id', $client->id)->where('actionable_document_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["document_id"])) {
                                        $yn_value = "Yes";
                                    } else {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableTemplateEmail':
                                    $yn_value = '';

                                    $data2 = ActionableTemplateEmailData::where('client_id', $client->id)->where('actionable_template_email_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableNotification':
                                    $yn_value = '';

                                    $data2 = ActionableNotificationData::where('client_id', $client->id)->where('actionable_notification_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableMultipleAttachment':
                                    $yn_value = '';

                                    $data2 = ActionableMultipleAttachmentData::where('client_id', $client->id)->where('actionable_ma_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                        $client_data[$client->id] = [
                            'type' => 'P',
                            'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                            'id' => $client->id,
                            'step_id' => $client->step_id,
                            'case_nr' => $client->case_number,
                            'cif_code' => $client->cif_code,
                            'committee' => isset($client->committee) ? $client->committee->name : null,
                            'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                            'instruction_date' => $client->instruction_date,
                            'completed_at' => ($client->completed_at != null ? $client->completed_at : ''),
                            'date_submitted_qa' => $client->qa_start_date,
                            'assigned' => ($client->consultant_id != null ? 1 : 0),
                            'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                            'data' => $data
                        ];

                        $total++;
                    }
                }

            }
        }

        if($report_type == "crm") {
            $report = CustomReport::with('custom_report_columns.crm_name')->where('id',$custom_report_id)->withTrashed()->first();

            $clients = Client::with(['crm.sections.form_section_inputs.input.data'])->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'),
                DB::raw('ROUND(DATEDIFF(completed_at, created_at), 0) as completed_days'),
                DB::raw('CAST(AES_DECRYPT(`hash_company`, "Qwfe345dgfdg") AS CHAR(50)) hash_company'),
                DB::raw('CAST(AES_DECRYPT(`hash_first_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_first_name'),
                DB::raw('CAST(AES_DECRYPT(`hash_last_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_last_name'),
                DB::raw('CAST(AES_DECRYPT(`hash_cif_code`, "Qwfe345dgfdg") AS CHAR(50)) hash_cif_code'),
                DB::raw('CAST(`case_number` AS CHAR(50)) AS case_number')
            );

            if ($request->has('q') && $request->input('q') != '') {

                $clients = $clients->having('hash_company', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_first_name', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_last_name', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_cif_code', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('case_number', 'like', "%" . $request->input('q') . "%");
            }

            $clients = $clients->get();

            if ($request->has('user') && $request->input('user') != null) {
                $clients = $clients->filter(function ($client) use ($request) {
                    return $client->consultant_id == $request->input('user');
                });
            }

            if ($request->has('f') && $request->input('f') != '') {
                $p = $request->input('f');
                $clients = $clients->filter(function ($client) use ($p) {
                    return $client->instruction_date >= $p;
                });
            }

            if ($request->has('t') && $request->input('t') != '') {
                $p = $request->input('t');
                $clients = $clients->filter(function ($client) use ($p) {
                    return Carbon::parse($client->instruction_date)->format("Y-m-d") <= $p;
                });
            }

            if(isset($report) && $report->group_report == '0') {

                $report_name = $report->name;
                foreach ($report->custom_report_columns as $report_activity) {
                    if($report_activity->crm_name["input_type"] != 'App\FormInputSubheading' && $report_activity->crm_name["input_type"] != 'App\FormInputHeading') {
                        array_push($report_columns, $report_activity->crm_name["name"]);
                        array_push($activity_id, $report_activity->crm_name["id"]);
                    }
                }
//dd($report_columns);
                foreach ($clients as $client) {
                    if ($client) {
                        $data = [];

                        foreach ($activity_id as $key => $value) {
                            $activity = FormSectionInputs::where('id', $value)->first();

                            switch ($activity["input_type"]) {
                                case 'App\FormInputBoolean':
                                    $yn_value = '';

                                    $data2 = FormInputBooleanData::where('client_id', $client->id)->where('form_input_boolean_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputDate':
                                    $data_value = '';

                                    $data2 = FormInputDateData::where('client_id', $client->id)->where('form_input_date_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputText':
                                    $data_value = '';

                                    $data2 = FormInputTextData::where('client_id', $client->id)->where('form_input_text_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputTextarea':
                                    $data_value = '';

                                    $data2 = FormInputTextareaData::where('client_id', $client->id)->where('form_input_textarea_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputDropdown':
                                    $data_value = '';

                                    $data2 = FormInputDropdownData::with('item')->where('client_id', $client->id)->where('form_input_dropdown_id', $activity->input_id)->get();

                                    foreach ($data2 as $key => $value):
                                        if (count($data2) > 1) {
                                            $data_value .= $value["item"]["name"] . ', ';
                                        } else {
                                            $data_value .= $value["item"]["name"];
                                        }
                                    endforeach;
                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputDocument':
                                    $yn_value = '';

                                    $data2 = FormInputDocumentData::where('client_id', $client->id)->where('form_input_document_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["document_id"])) {
                                        $yn_value = "Yes";
                                    } else {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputTemplateEmail':
                                    $yn_value = '';

                                    $data2 = FormInputTemplateEmailData::where('client_id', $client->id)->where('form_input_template_email_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputNotification':
                                    $yn_value = '';

                                    $data2 = FormInputNotificationData::where('client_id', $client->id)->where('form_input_notification_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputMultipleAttachment':
                                    $yn_value = '';

                                    $data2 = FormInputMultipleAttachmentData::where('client_id', $client->id)->where('form_input_ma_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                        $client_data[$client->id] = [
                            'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                            'id' => $client->id,
                            'step_id' => $client->step_id,
                            'case_nr' => $client->case_number,
                            'cif_code' => $client->cif_code,
                            'committee' => isset($client->committee) ? $client->committee->name : null,
                            'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                            'instruction_date' => $client->instruction_date,
                            'completed_at' => ($client->completed_at != null ? $client->completed_at : ''),
                            'date_submitted_qa' => $client->qa_start_date,
                            'assigned' => ($client->consultant_id != null ? 1 : 0),
                            'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                            'data' => $data
                        ];

                        $total++;
                    }
                }
            } else {
                $report_name = $report->name;
                foreach ($report->custom_report_columns as $report_activity) {

                    $rows = FormSectionInputs::select(DB::raw("DISTINCT grouping"))->where('form_section_id',$report_activity->crm_name["form_section_id"])->where('grouping','>',0)->get()->count();

                    array_push($report_columns, $report_activity->crm_name["name"]);

                    if($report_activity->activity_name["grouping"] > 0) {
                        array_push($activity_id, $report_activity->crm_name["id"]);
                    } else {
                        array_push($activity_id, $report_activity->crm_name["id"]);
                    }
                }

                foreach ($clients as $client) {
                    if ($client) {
                        $data = [];

                        foreach ($activity_id as $key => $value) {
                            $activity = FormSectionInputs::where('id', $value)->first();

                            switch ($activity["actionable_type"]) {
                                case 'App\FormInputBoolean':
                                    $yn_value = '';

                                    $data2 = FormInputBooleanData::where('client_id', $client->id)->where('form_input_boolean_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputDate':
                                    $data_value = '';

                                    $data2 = FormInputDateData::where('client_id', $client->id)->where('form_input_date_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputText':
                                    $data_value = '';

                                    $data2 = FormInputTextData::where('client_id', $client->id)->where('form_input_text_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    $data[$activity["id"]] = $data_value;
                                    //array_push($data, $data_value);
                                    break;
                                case 'App\FormInputTextarea':
                                    $data_value = '';

                                    $data2 = FormInputTextareaData::where('client_id', $client->id)->where('form_input_textarea_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputDropdown':
                                    $data_value = '';

                                    $data2 = FormInputDropdownData::with('item')->where('client_id', $client->id)->where('form_input_dropdown_id', $activity->input_id)->get();

                                    foreach ($data2 as $key => $value):
                                        if (count($data2) > 1) {
                                            $data_value .= $value["item"]["name"] . ', ';
                                        } else {
                                            $data_value .= $value["item"]["name"];
                                        }
                                    endforeach;
                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputDocument':
                                    $yn_value = '';

                                    $data2 = FormInputDocumentData::where('client_id', $client->id)->where('form_input_document_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["document_id"])) {
                                        $yn_value = "Yes";
                                    } else {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputTemplateEmail':
                                    $yn_value = '';

                                    $data2 = FormInputTemplateEmailData::where('client_id', $client->id)->where('form_input_template_email_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputNotification':
                                    $yn_value = '';

                                    $data2 = FormInputNotificationData::where('client_id', $client->id)->where('form_input_notification_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputMultipleAttachment':
                                    $yn_value = '';

                                    $data2 = FormInputMultipleAttachmentData::where('client_id', $client->id)->where('input_ma_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                        $client_data[$client->id] = [
                            'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                            'id' => $client->id,
                            'step_id' => $client->step_id,
                            'case_nr' => $client->case_number,
                            'cif_code' => $client->cif_code,
                            'committee' => isset($client->committee) ? $client->committee->name : null,
                            'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                            'instruction_date' => $client->instruction_date,
                            'completed_at' => ($client->completed_at != null ? $client->completed_at : ''),
                            'date_submitted_qa' => $client->qa_start_date,
                            'assigned' => ($client->consultant_id != null ? 1 : 0),
                            'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                            'data' => $data
                        ];

                        $total++;
                    }
                }
            }

        }

        $step_names = Step::select('id', 'name', 'process_id')->get();

            $parameters = [
                'np' => $np,
                'qa' => $qa,
                'report_id' => $custom_report_id,
                'report_name' => $report_name,
                'fields' => $report_columns,
                'clients' => (isset($client_data) ? $client_data : []),
                'activity' => '',
                'total' => $total,
                'step_names' => $step_names
            ];

            // dd($client_data);
        return $excel->download(new CustomReportExport($client_data,$report_columns,$step_names), 'clients_'.date('Y_m_d_H_i_s').'.xlsx');
    }

    public function pdfexport($custom_report_id,$report_type,Request $request)
    {

        $request->session()->forget('path_route');

        $np = 0;
        $qa = 0;
        $total = 0;
        $rows = 0;

        $report_name = '';
        $report_columns = array();
        $activity_id = array();
        $rp_activity_id = array();
        $data = array();


        if($report_type == "process") {
            $report = CustomReport::with('custom_report_columns.activity_name')->where('id', $custom_report_id)->withTrashed()->first();

            $clients = Client::with(['referrer', 'process.steps2.activities.actionable.data', 'introducer', 'consultant', 'committee', 'trigger'])->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'),
                DB::raw('ROUND(DATEDIFF(completed_at, created_at), 0) as completed_days'),
                DB::raw('CAST(AES_DECRYPT(`hash_company`, "Qwfe345dgfdg") AS CHAR(50)) hash_company'),
                DB::raw('CAST(AES_DECRYPT(`hash_first_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_first_name'),
                DB::raw('CAST(AES_DECRYPT(`hash_last_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_last_name'),
                DB::raw('CAST(AES_DECRYPT(`hash_cif_code`, "Qwfe345dgfdg") AS CHAR(50)) hash_cif_code'),
                DB::raw('CAST(`case_number` AS CHAR(50)) AS case_number')
            );

            if ($request->has('q') && $request->input('q') != '') {

                $clients = $clients->having('hash_company', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_first_name', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_last_name', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_cif_code', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('case_number', 'like', "%" . $request->input('q') . "%");
            }

            // $clients = $clients->get();
            $clients = $clients->where('process_id', $report->process_id)->where('is_progressing', 1)->get();

            if ($request->has('user') && $request->input('user') != null) {
                $clients = $clients->filter(function ($client) use ($request) {
                    return $client->consultant_id == $request->input('user');
                });
            }

            if ($request->has('f') && $request->input('f') != '') {
                $p = $request->input('f');
                $clients = $clients->filter(function ($client) use ($p) {
                    return $client->instruction_date >= $p;
                });
            }

            if ($request->has('t') && $request->input('t') != '') {
                $p = $request->input('t');
                $clients = $clients->filter(function ($client) use ($p) {
                    return Carbon::parse($client->instruction_date)->format("Y-m-d") <= $p;
                });
            }

            if (isset($report) && $report->group_report == '0') {

                $report_name = $report->name;
                foreach ($report->custom_report_columns as $report_activity) {
                    array_push($report_columns, $report_activity->activity_name["name"]);
                    array_push($activity_id, $report_activity->activity_name["id"]);

                    $rp_activity = ActivityRelatedPartyLink::select('related_activity')->where('primary_activity', $report_activity->activity_name["id"])->first();

                    if ($rp_activity) {
                        array_push($rp_activity_id, $rp_activity->related_activity);
                    }
                }

                foreach ($clients as $client) {
                    if ($client) {
                        $data = [];

                        foreach ($activity_id as $key => $value) {
                            $activity = Activity::where('id', $value)->first();

                            switch ($activity["actionable_type"]) {
                                case 'App\ActionableBoolean':
                                    $yn_value = '';

                                    $data2 = ActionableBooleanData::where('client_id', $client->id)->where('actionable_boolean_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableDate':
                                    $data_value = '';

                                    $data2 = ActionableDateData::where('client_id', $client->id)->where('actionable_date_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = (isset($data2["data"]) ? $data2["data"] : null);

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableText':
                                    $data_value = '';

                                    $data2 = ActionableTextData::where('client_id', $client->id)->where('actionable_text_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = (isset($data2["data"]) ? $data2["data"] : null);

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableTextarea':
                                    $data_value = '';

                                    $data2 = ActionableTextareaData::where('client_id', $client->id)->where('actionable_textarea_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableDropdown':
                                    $data_value = '';

                                    $data2 = ActionableDropdownData::with('item')->where('client_id', $client->id)->where('actionable_dropdown_id', $activity->actionable_id)->get();

                                    foreach ($data2 as $key => $value):
                                        if (count($data2) > 1) {
                                            $data_value .= (isset($value["item"]["name"]) ? $value["item"]["name"] : null) . ', ';
                                        } else {
                                            $data_value .= (isset($value["item"]["name"]) ? $value["item"]["name"] : null);
                                        }
                                    endforeach;
                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableDocument':
                                    $yn_value = '';

                                    $data2 = ActionableDocumentData::where('client_id', $client->id)->where('actionable_document_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["document_id"])) {
                                        $yn_value = "Yes";
                                    } else {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableTemplateEmail':
                                    $yn_value = '';

                                    $data2 = ActionableTemplateEmailData::where('client_id', $client->id)->where('actionable_template_email_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableNotification':
                                    $yn_value = '';

                                    $data2 = ActionableNotificationData::where('client_id', $client->id)->where('actionable_notification_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableMultipleAttachment':
                                    $yn_value = '';

                                    $data2 = ActionableMultipleAttachmentData::where('client_id', $client->id)->where('actionable_ma_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                        $client_data[$client->id] = [
                            'type' => 'P',
                            'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                            'id' => $client->id,
                            'step_id' => $client->step_id,
                            'case_nr' => $client->case_number,
                            'cif_code' => $client->cif_code,
                            'committee' => isset($client->committee) ? $client->committee->name : null,
                            'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                            'instruction_date' => $client->instruction_date,
                            'completed_at' => ($client->completed_at != null ? $client->completed_at : ''),
                            'date_submitted_qa' => $client->qa_start_date,
                            'assigned' => ($client->consultant_id != null ? 1 : 0),
                            'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                            'data' => $data
                        ];

                        $total++;
                    }
                }
            } else {
                $report_name = $report->name;
                foreach ($report->custom_report_columns as $report_activity) {

                    $rows = Activity::select(DB::raw("DISTINCT grouping"))->where('step_id', $report_activity->activity_name["step_id"])->where('grouping', '>', 0)->get()->count();

                    array_push($report_columns, $report_activity->activity_name["name"]);

                    if ($report_activity->activity_name["grouping"] > 0) {
                        array_push($activity_id, $report_activity->activity_name["id"]);
                    } else {
                        array_push($activity_id, $report_activity->activity_name["id"]);
                    }

                }

                foreach ($clients as $client) {
                    if ($client) {
                        $data = [];

                        foreach ($activity_id as $key => $value) {
                            $activity = Activity::where('id', $value)->first();

                            switch ($activity["actionable_type"]) {
                                case 'App\ActionableBoolean':
                                    $yn_value = '';

                                    $data2 = ActionableBooleanData::where('client_id', $client->id)->where('actionable_boolean_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableDate':
                                    $data_value = '';

                                    $data2 = ActionableDateData::where('client_id', $client->id)->where('actionable_date_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableText':
                                    $data_value = '';

                                    $data2 = ActionableTextData::where('client_id', $client->id)->where('actionable_text_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    $data[$activity["id"]] = $data_value;
                                    //array_push($data, $data_value);
                                    break;
                                case 'App\ActionableTextarea':
                                    $data_value = '';

                                    $data2 = ActionableTextareaData::where('client_id', $client->id)->where('actionable_textarea_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableDropdown':
                                    $data_value = '';

                                    $data2 = ActionableDropdownData::with('item')->where('client_id', $client->id)->where('actionable_dropdown_id', $activity->actionable_id)->get();

                                    foreach ($data2 as $key => $value):
                                        if (count($data2) > 1) {
                                            $data_value .= $value["item"]["name"] . ', ';
                                        } else {
                                            $data_value .= $value["item"]["name"];
                                        }
                                    endforeach;
                                    array_push($data, $data_value);
                                    break;
                                case 'App\ActionableDocument':
                                    $yn_value = '';

                                    $data2 = ActionableDocumentData::where('client_id', $client->id)->where('actionable_document_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["document_id"])) {
                                        $yn_value = "Yes";
                                    } else {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableTemplateEmail':
                                    $yn_value = '';

                                    $data2 = ActionableTemplateEmailData::where('client_id', $client->id)->where('actionable_template_email_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableNotification':
                                    $yn_value = '';

                                    $data2 = ActionableNotificationData::where('client_id', $client->id)->where('actionable_notification_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\ActionableMultipleAttachment':
                                    $yn_value = '';

                                    $data2 = ActionableMultipleAttachmentData::where('client_id', $client->id)->where('actionable_ma_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                        $client_data[$client->id] = [
                            'type' => 'P',
                            'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                            'id' => $client->id,
                            'step_id' => $client->step_id,
                            'client_id' => '',
                            'case_nr' => $client->case_number,
                            'cif_code' => $client->cif_code,
                            'committee' => isset($client->committee) ? $client->committee->name : null,
                            'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                            'instruction_date' => $client->instruction_date,
                            'completed_at' => ($client->completed_at != null ? $client->completed_at : ''),
                            'date_submitted_qa' => $client->qa_start_date,
                            'assigned' => ($client->consultant_id != null ? 1 : 0),
                            'consultant' => '',
                            'data' => $data
                        ];

                        $total++;
                    }
                }

            }
        }

        if($report_type == "crm") {
            $report = CustomReport::with('custom_report_columns.crm_name')->where('id',$custom_report_id)->withTrashed()->first();

            $clients = Client::with(['crm.sections.form_section_inputs.input.data'])->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'),
                DB::raw('ROUND(DATEDIFF(completed_at, created_at), 0) as completed_days'),
                DB::raw('CAST(AES_DECRYPT(`hash_company`, "Qwfe345dgfdg") AS CHAR(50)) hash_company'),
                DB::raw('CAST(AES_DECRYPT(`hash_first_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_first_name'),
                DB::raw('CAST(AES_DECRYPT(`hash_last_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_last_name'),
                DB::raw('CAST(AES_DECRYPT(`hash_cif_code`, "Qwfe345dgfdg") AS CHAR(50)) hash_cif_code'),
                DB::raw('CAST(`case_number` AS CHAR(50)) AS case_number')
            );

            if ($request->has('q') && $request->input('q') != '') {

                $clients = $clients->having('hash_company', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_first_name', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_last_name', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('hash_cif_code', 'like', "%" . $request->input('q') . "%")
                    ->orHaving('case_number', 'like', "%" . $request->input('q') . "%");
            }

            $clients = $clients->get();

            if ($request->has('user') && $request->input('user') != null) {
                $clients = $clients->filter(function ($client) use ($request) {
                    return $client->consultant_id == $request->input('user');
                });
            }

            if ($request->has('f') && $request->input('f') != '') {
                $p = $request->input('f');
                $clients = $clients->filter(function ($client) use ($p) {
                    return $client->instruction_date >= $p;
                });
            }

            if ($request->has('t') && $request->input('t') != '') {
                $p = $request->input('t');
                $clients = $clients->filter(function ($client) use ($p) {
                    return Carbon::parse($client->instruction_date)->format("Y-m-d") <= $p;
                });
            }

            if(isset($report) && $report->group_report == '0') {

                $report_name = $report->name;
                foreach ($report->custom_report_columns as $report_activity) {
                    if($report_activity->crm_name["input_type"] != 'App\FormInputSubheading' && $report_activity->crm_name["input_type"] != 'App\FormInputHeading') {
                        array_push($report_columns, $report_activity->crm_name["name"]);
                        array_push($activity_id, $report_activity->crm_name["id"]);
                    }
                }
//dd($report_columns);
                foreach ($clients as $client) {
                    if ($client) {
                        $data = [];

                        foreach ($activity_id as $key => $value) {
                            $activity = FormSectionInputs::where('id', $value)->first();

                            switch ($activity["input_type"]) {
                                case 'App\FormInputBoolean':
                                    $yn_value = '';

                                    $data2 = FormInputBooleanData::where('client_id', $client->id)->where('form_input_boolean_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputDate':
                                    $data_value = '';

                                    $data2 = FormInputDateData::where('client_id', $client->id)->where('form_input_date_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputText':
                                    $data_value = '';

                                    $data2 = FormInputTextData::where('client_id', $client->id)->where('form_input_text_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputTextarea':
                                    $data_value = '';

                                    $data2 = FormInputTextareaData::where('client_id', $client->id)->where('form_input_textarea_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputDropdown':
                                    $data_value = '';

                                    $data2 = FormInputDropdownData::with('item')->where('client_id', $client->id)->where('form_input_dropdown_id', $activity->input_id)->get();

                                    foreach ($data2 as $key => $value):
                                        if (count($data2) > 1) {
                                            $data_value .= $value["item"]["name"] . ', ';
                                        } else {
                                            $data_value .= $value["item"]["name"];
                                        }
                                    endforeach;
                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputDocument':
                                    $yn_value = '';

                                    $data2 = FormInputDocumentData::where('client_id', $client->id)->where('form_input_document_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["document_id"])) {
                                        $yn_value = "Yes";
                                    } else {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputTemplateEmail':
                                    $yn_value = '';

                                    $data2 = FormInputTemplateEmailData::where('client_id', $client->id)->where('form_input_template_email_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputNotification':
                                    $yn_value = '';

                                    $data2 = FormInputNotificationData::where('client_id', $client->id)->where('form_input_notification_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputMultipleAttachment':
                                    $yn_value = '';

                                    $data2 = FormInputMultipleAttachmentData::where('client_id', $client->id)->where('form_input_ma_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                        $client_data[$client->id] = [
                            'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                            'id' => $client->id,
                            'step_id' => $client->step_id,
                            'case_nr' => $client->case_number,
                            'cif_code' => $client->cif_code,
                            'committee' => isset($client->committee) ? $client->committee->name : null,
                            'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                            'instruction_date' => $client->instruction_date,
                            'completed_at' => ($client->completed_at != null ? $client->completed_at : ''),
                            'date_submitted_qa' => $client->qa_start_date,
                            'assigned' => ($client->consultant_id != null ? 1 : 0),
                            'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                            'data' => $data
                        ];

                        $total++;
                    }
                }
            } else {
                $report_name = $report->name;
                foreach ($report->custom_report_columns as $report_activity) {

                    $rows = FormSectionInputs::select(DB::raw("DISTINCT grouping"))->where('form_section_id',$report_activity->crm_name["form_section_id"])->where('grouping','>',0)->get()->count();

                    array_push($report_columns, $report_activity->crm_name["name"]);

                    if($report_activity->activity_name["grouping"] > 0) {
                        array_push($activity_id, $report_activity->crm_name["id"]);
                    } else {
                        array_push($activity_id, $report_activity->crm_name["id"]);
                    }
                }

                foreach ($clients as $client) {
                    if ($client) {
                        $data = [];

                        foreach ($activity_id as $key => $value) {
                            $activity = FormSectionInputs::where('id', $value)->first();

                            switch ($activity["actionable_type"]) {
                                case 'App\FormInputBoolean':
                                    $yn_value = '';

                                    $data2 = FormInputBooleanData::where('client_id', $client->id)->where('form_input_boolean_id', $activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputDate':
                                    $data_value = '';

                                    $data2 = FormInputDateData::where('client_id', $client->id)->where('form_input_date_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputText':
                                    $data_value = '';

                                    $data2 = FormInputTextData::where('client_id', $client->id)->where('form_input_text_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    $data[$activity["id"]] = $data_value;
                                    //array_push($data, $data_value);
                                    break;
                                case 'App\FormInputTextarea':
                                    $data_value = '';

                                    $data2 = FormInputTextareaData::where('client_id', $client->id)->where('form_input_textarea_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    $data_value = $data2["data"];

                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputDropdown':
                                    $data_value = '';

                                    $data2 = FormInputDropdownData::with('item')->where('client_id', $client->id)->where('form_input_dropdown_id', $activity->input_id)->get();

                                    foreach ($data2 as $key => $value):
                                        if (count($data2) > 1) {
                                            $data_value .= $value["item"]["name"] . ', ';
                                        } else {
                                            $data_value .= $value["item"]["name"];
                                        }
                                    endforeach;
                                    array_push($data, $data_value);
                                    break;
                                case 'App\FormInputDocument':
                                    $yn_value = '';

                                    $data2 = FormInputDocumentData::where('client_id', $client->id)->where('form_input_document_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["document_id"])) {
                                        $yn_value = "Yes";
                                    } else {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputTemplateEmail':
                                    $yn_value = '';

                                    $data2 = FormInputTemplateEmailData::where('client_id', $client->id)->where('form_input_template_email_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputNotification':
                                    $yn_value = '';

                                    $data2 = FormInputNotificationData::where('client_id', $client->id)->where('form_input_notification_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                case 'App\FormInputMultipleAttachment':
                                    $yn_value = '';

                                    $data2 = FormInputMultipleAttachmentData::where('client_id', $client->id)->where('input_ma_id', $activity->input_id)->orderBy('created_at', 'desc')->take(1)->first();

                                    if (isset($data2["data"]) && $data2["data"] == '1') {
                                        $yn_value = "Yes";
                                    }
                                    if (isset($data2["data"]) && $data2["data"] == '0') {
                                        $yn_value = "No";
                                    }
                                    array_push($data, $yn_value);
                                    break;
                                default:
                                    //todo capture defaults
                                    break;
                            }

                        }
                        $client_data[$client->id] = [
                            'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                            'id' => $client->id,
                            'step_id' => $client->step_id,
                            'case_nr' => $client->case_number,
                            'cif_code' => $client->cif_code,
                            'committee' => isset($client->committee) ? $client->committee->name : null,
                            'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                            'instruction_date' => $client->instruction_date,
                            'completed_at' => ($client->completed_at != null ? $client->completed_at : ''),
                            'date_submitted_qa' => $client->qa_start_date,
                            'assigned' => ($client->consultant_id != null ? 1 : 0),
                            'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                            'data' => $data
                        ];

                        $total++;
                    }
                }
            }

        }
//dd($report_columns);
        /*$parameters = [
            'np' => $np,
            'qa' => $qa,
            'report_type' => $report_type,
            'report_id' => $custom_report_id,
            'report_name' => $report_name,
            'fields' => $report_columns,
            'clients' => (isset($client_data) ? $client_data : []),
            'committee' => Committee::orderBy('name')->pluck('name', 'id')->prepend('All committees', 'all'),
            'trigger' => TriggerType::orderBy('name')->pluck('name', 'id')->prepend('All trigger types', 'all'),
            'assigned_user' => Client::all()->keyBy('consultant_id')->map(function ($consultant){
                return isset($consultant->consultant)?$consultant->consultant->first_name.' '.$consultant->consultant->last_name:null;
            })->sort(),
            'activity' => '',
            'total' => $total
        ];*/

        $step_names = Step::select('id', 'name', 'process_id')->get();

        $parameters = [
            'report_id' => $custom_report_id,
            'report_name' => $report_name,
            'fields' => $report_columns,
            'clients' => $client_data,
            'steps' => FormSection::orderBy('form_id')->orderBy('order')->pluck('name', 'id')->prepend('All crm', ''),
            'activity' => '',
            'step_names' => $step_names
        ];

        $pdf = PDF::loadView('pdf.customreport2', $parameters)->setPaper('a4')->setOrientation('landscape');
        return $pdf->download('clients_'.date('Y_m_d_H_i_s').'.pdf');
    }

    public function getActivities(Request $request,$processid){

        $step_arr = array();
        $activities_arr = array();

        if($request->input('type') == "process") {
            $steps = Step::where('process_id', $processid)->orderBy('order', 'asc')->get();

            foreach($steps as $step){
                $step_arr2 = array();

                $step_arr2['id'] = $step->id;
                $step_arr2['name'] = $step->name;

                //array_push($step_arr, $step_arr2);

                $activities = Activity::select('activities.id','activities.name','activities.actionable_type','activities.grouping')->leftJoin('steps','steps.id','activities.step_id')->where('steps.process_id',$processid)->where('steps.deleted_at',null)->where('activities.deleted_at',null)->where('activities.step_id',$step->id)->orderBy('activities.step_id','asc')->orderBy('activities.order','asc')->get();
                //dd($activities);
                $step_arr2['activity'] = array();
                foreach($activities as $activity){

                    if($activity->grouping != null && $activity->grouping > 0) {
                        if($activity->grouping == 1) {
                            array_push($step_arr2['activity'], [
                                'id' => $activity->id,
                                'name' => $activity->name,
                                'type' => $activity->actionable_type,
                                'step' => $activity->step_id,
                                'grouping' => '1'
                            ]);
                        }
                    } else {
                        array_push($step_arr2['activity'], [
                            'id' => $activity->id,
                            'name' => $activity->name,
                            'type' => $activity->actionable_type,
                            'step' => $activity->step_id,
                            'grouping' => '0'
                        ]);
                    }


                }

                array_push($step_arr,$step_arr2);
            }
        }

        if($request->input('type') == "crm") {
            $steps = FormSection::where('form_id', $processid)->orderBy('order', 'asc')->get();

            foreach($steps as $step){
                $step_arr2 = array();

                $step_arr2['id'] = $step->id;
                $step_arr2['name'] = $step->name;

                //array_push($step_arr, $step_arr2);

                $activities = FormSectionInputs::select('form_section_inputs.id','form_section_inputs.name','form_section_inputs.input_type','form_section_inputs.grouping')->leftJoin('form_sections','form_sections.id','form_section_inputs.form_section_id')->where('form_sections.form_id',$processid)->where('form_sections.deleted_at',null)->where('form_section_inputs.deleted_at',null)->where('form_section_inputs.form_section_id',$step->id)->orderBy('form_section_inputs.form_section_id','asc')->orderBy('form_section_inputs.order','asc')->get();
                //dd($activities);
                $step_arr2['activity'] = array();
                foreach($activities as $activity){
                    if($activity->input_type != "App\Subheading" && $activity->input_type != "App\Heading") {
                        if ($activity->grouping != null && $activity->grouping > 0) {
                            if ($activity->grouping == 1) {
                                array_push($step_arr2['activity'], [
                                    'id' => $activity->id,
                                    'name' => $activity->name,
                                    'type' => $activity->input_type,
                                    'step' => $activity->form_section_id,
                                    'grouping' => '1'
                                ]);
                            }
                        } else {
                            array_push($step_arr2['activity'], [
                                'id' => $activity->id,
                                'name' => $activity->name,
                                'type' => $activity->input_type,
                                'step' => $activity->form_section_id,
                                'grouping' => '0'
                            ]);
                        }
                    }


                }

                array_push($step_arr,$step_arr2);
            }
        }





        return response()->json($step_arr);
    }

    public function getSelectedActivities($custom_report_id){

        $sa = array();

        $process = CustomReport::where('id',$custom_report_id)->first();
//return $process;
        $selected_activities = CustomReportColumns::select('activity_id')->where('custom_report_id',$custom_report_id)->get();
//return $selected_activities;
        foreach($selected_activities as $result){
            array_push($sa,$result->activity_id);
        }

        $step_arr = array();
        $activities_arr = array();

        if($process->type == "process") {
            $steps = Step::where('process_id', $process->process_id)->orderBy('order', 'asc')->get();

            foreach ($steps as $step) {
                $step_arr2 = array();

                $step_arr2['id'] = $step->id;
                $step_arr2['name'] = $step->name;

                $activities = Activity::select('activities.id', 'activities.name', 'activities.actionable_type', 'activities.grouping')->leftJoin('steps', 'steps.id', 'activities.step_id')->where('activities.step_id', $step->id)->where('steps.process_id', $process->process_id)->where('steps.deleted_at', null)->where('activities.deleted_at', null)->where('activities.step_id', $step->id)->orderBy('activities.step_id', 'asc')->orderBy('activities.order', 'asc')->get();

                $step_arr2['activity'] = array();
                foreach ($activities as $activity) {

                    if (($key = array_search($activity->id, $sa)) === false) {
                        if ($activity->grouping != null && $activity->grouping > 0) {
                            if ($activity->grouping == 1) {
                                array_push($step_arr2['activity'], [
                                    'id' => $activity->id,
                                    'name' => $activity->name,
                                    'type' => $activity->actionable_type,
                                    'selected' => '1',
                                    'grouping' => '1'
                                ]);
                            }
                        } else {
                            array_push($step_arr2['activity'], [
                                'id' => $activity->id,
                                'name' => $activity->name,
                                'type' => $activity->actionable_type,
                                'selected' => '1',
                                'grouping' => '0'
                            ]);
                        }
                    } else {
                        if ($activity->grouping != null && $activity->grouping > 0) {
                            if ($activity->grouping == 1) {
                                array_push($step_arr2['activity'], [
                                    'id' => $activity->id,
                                    'name' => $activity->name,
                                    'type' => $activity->actionable_type,
                                    'selected' => '0',
                                    'grouping' => '1'
                                ]);
                            }
                        } else {
                            array_push($step_arr2['activity'], [
                                'id' => $activity->id,
                                'name' => $activity->name,
                                'type' => $activity->actionable_type,
                                'selected' => '0',
                                'grouping' => '0'
                            ]);
                        }
                    }

                }

                array_push($step_arr, $step_arr2);
            }
        }

        if($process->type == "crm") {

            $steps = FormSection::where('form_id', $process->process_id)->orderBy('order', 'asc')->get();

            foreach ($steps as $step) {
                $step_arr2 = array();

                $step_arr2['id'] = $step->id;
                $step_arr2['name'] = $step->name;

                $activities = FormSectionInputs::select('form_section_inputs.id', 'form_section_inputs.name', 'form_section_inputs.input_type', 'form_section_inputs.grouping')->leftJoin('form_sections', 'form_sections.id', 'form_section_inputs.form_section_id')->where('form_section_inputs.form_section_id', $step->id)->where('form_sections.form_id', $process->process_id)->where('form_sections.deleted_at', null)->where('form_section_inputs.deleted_at', null)->where('form_section_inputs.form_section_id', $step->id)->orderBy('form_section_inputs.form_section_id', 'asc')->orderBy('form_section_inputs.order', 'asc')->get();

                $step_arr2['activity'] = array();
                foreach ($activities as $activity) {

                    if($activity->input_type != "App\Subheading" && $activity->input_type != "App\Heading") {
                        if (($key = array_search($activity->id, $sa)) === false) {
                            if ($activity->grouping != null && $activity->grouping > 0) {
                                if ($activity->grouping == 1) {
                                    array_push($step_arr2['activity'], [
                                        'id' => $activity->id,
                                        'name' => $activity->name,
                                        'type' => $activity->input_type,
                                        'selected' => '0',
                                        'grouping' => '1'
                                    ]);
                                }
                            } else {
                                array_push($step_arr2['activity'], [
                                    'id' => $activity->id,
                                    'name' => $activity->name,
                                    'type' => $activity->input_type,
                                    'selected' => '0',
                                    'grouping' => '0'
                                ]);
                            }
                        } else {
                            if ($activity->grouping != null && $activity->grouping > 0) {
                                if ($activity->grouping == 1) {
                                    array_push($step_arr2['activity'], [
                                        'id' => $activity->id,
                                        'name' => $activity->name,
                                        'type' => $activity->input_type,
                                        'selected' => '1',
                                        'grouping' => '1'
                                    ]);
                                }
                            } else {
                                array_push($step_arr2['activity'], [
                                    'id' => $activity->id,
                                    'name' => $activity->name,
                                    'type' => $activity->input_type,
                                    'selected' => '1',
                                    'grouping' => '0'
                                ]);
                            }
                        }
                    }

                }

                array_push($step_arr, $step_arr2);
            }
        }

//        return $step_arr;

        return response()->json($step_arr);
    }
}
