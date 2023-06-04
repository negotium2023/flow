<?php

namespace App\Http\Controllers;

use App\ActivityRelatedPartyLink;
use App\Committee;
use App\OfficeUser;
use App\RelatedPartiesTree;
use App\RelatedPartyDropdown;
use App\TriggerType;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Shared\Html;
use App\Http\Requests\GenerateReportRequest;
use App\ActionableMultipleAttachmentData;
use App\ActionableTemplateEmailData;
use App\ActionableDropdownData;
use App\ActionableDropdownItem;
use App\ActionableDocumentData;
use App\ActionableBooleanData;
use App\ActionableTextData;
use App\ActionableTextareaData;
use App\ActionableDateData;
use App\ActionableNotificationData;
use App\RelatedPartyMultipleAttachmentData;
use App\RelatedPartyTemplateEmailData;
use App\RelatedPartyDropdownData;
use App\RelatedPartyDropdownItem;
use App\RelatedPartyDocumentData;
use App\RelatedPartyBooleanData;
use App\RelatedPartyTextData;
use App\RelatedPartyTextareaData;
use App\RelatedPartyDateData;
use App\RelatedPartyNotificationData;
use App\ActionsAssigned;
use App\Referrer;
use App\RelatedParty;
use App\Template;
use App\Activity;
use App\ActivityLog;
use App\Client;
use App\ClientUser;
use App\Config;
use App\Process;
use App\Step;
use App\User;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Report;
use App\Http\Requests\UpdateReportRequest;
use App\Exports\DynamicReportExport;
use Maatwebsite\Excel\Excel;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use App\Presentation;
use Illuminate\Support\Arr;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use App\Log;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;
use PhpOffice\PhpPresentation\Style\Border;
use PhpOffice\PhpPresentation\Shape\Drawing;
use HTMLtoOpenXML;
use Jupitern\Docx\DocxMerge;



class ReportController extends Controller {
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $parameters = [
            'reports' => Report::orderBy('name')->with('user', 'activity')->get()
        ];
        return view('reports.view')->with($parameters);
    }

    public function auditReport(Request $request){


        $logs = Log::with('user','client','activities.activity');

        $logs->whereHas('client',function($q){
            $q->where('is_qa',0);
        });

        if($request->input('f') && $request->input('f') != '') {
            $logs = $logs->where('created_at', '>=', $request->input('f'));
        }

        if($request->input('t') && $request->input('t') != '') {
            $logs = $logs->where('created_at', '<=', $request->input('t'));
        }

        $logs = $logs->orderBy('created_at','desc')->limit(1000)->get();

        $log_array = new Collection();

        foreach($logs as $log){

            foreach($log->activities as $activity){

                if($activity->old_value != null) {
                    $action = 'Activity Updated';
                } else {
                    $action = 'Activity Captured';
                }

                $new_value = $activity->new_value;

                if($activity->activity["actionable_type"] == 'App\ActionableBoolean'){
                    $new_value = ($activity->new_value != null && $activity->new_value == 1 ? 'Yes' : 'No');
                } elseif($activity->activity["actionable_type"] == 'App\ActionableDropdown'){
                    if($activity->new_value != null) {
                        $dd = ActionableDropdownItem::withTrashed()->where('id', $activity->new_value)->first();
                        $new_value = (isset($dd) && $dd != null ? $dd->name : '');
                    } else {
                        $new_value = '';
                    }
                    //dd($new_value);
                } elseif($activity->activity["actionable_type"] == 'App\ActionableNotification'){
                    $notifications = explode(',',$activity->new_value);
                    $data_value = '';
                    foreach ($notifications as $notification){
                        $user_name = User::withTrashed()->where('id',$notification)->first();
                        if($user_name["id"] != null) {
                            if ($data_value == '') {
                                $data_value = $user_name["first_name"] . ' ' . $user_name["last_name"];
                            } else {
                                $data_value .= "," . $user_name["first_name"] . ' ' . $user_name["last_name"];
                            }
                        }
                    }
                    $new_value = htmlentities($data_value);
                }
                $log_array->push([
                    'client' => ($log->client["company"] != null && $log->client["company"] != '' ? $log->client["company"] : $log->client["first_name"].' '.$log->client["last_name"]),
                    'relatedparty' => ($log->related_party_id != null ? ($log->relatedparty["company"] != null && $log->relatedparty["company"] != '' ? $log->relatedparty["company"] : $log->relatedparty["first_name"].' '.$log->relatedparty["last_name"]) : ''),
                    'id' => $activity->id,
                    'client_id' => $log->client["id"],
                    'action' => $action,
                    'user_id' => $log->user["id"],
                    'user' => $log->user["first_name"].' '.$log->user["last_name"],
                    'activity_id' => $activity->activity_id,
                    'activity_name' => $activity->activity_name,
                    'old_value' => $activity->old_value,
                    'new_value' => $new_value,
                    'created_at' => Carbon::parse($activity->created_at)->format('Y-m-d')
                ]);
            }
        }

        if($request->has('activities_search') && $request->input('activities_search') !='' ){
            $activity_input = $request->input('activities_search');

            $log_array = $log_array->filter(function ($log) use ($activity_input) {
                return $log['activity_name'] == $activity_input;
            });
        }

        if($request->has('client_search') && $request->input('client_search') !='' && $request->input('client_search') != 0 ){
            $client_input = $request->input('client_search');

            $log_array = $log_array->filter(function ($log) use ($client_input) {
                return $log['client_id'] == $client_input;
            });
        }

        if($request->has('user') && $request->input('user') !='' ){
            $user = $request->input('user');

            $log_array = $log_array->filter(function ($log) use ($user) {
                return $log['user_id'] == $user;
            });
        }

        $client_array = [];

        $clients = Client::get();

        $client_array[0] = 'All';

        foreach ($clients as $ca){
            $client_array[$ca->id] = ($ca->company != null && $ca->company != '' ? $ca->company : $ca->first_name.' '.$ca->last_name);
        }

        $rp_array = [];

        $rp = Client::get();

        $rp_array[0] = 'All';

        foreach ($rp as $ca){
            $rp_array[$ca->id] = ($ca->company != null && $ca->company != '' ? $ca->company : $ca->first_name.' '.$ca->last_name);
        }

        $parameters = [
            'activities' => Activity::select('id','name')->distinct('name')->get(),
            'clients_dropdown' => $client_array,
            'related_party_dropdown' => $rp_array,
            'users' => User::select('id', DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->get(),
            'log_array' => $log_array
        ];

        return view('reports.auditreport')->with($parameters);

    }

    public function generateReport(Request $request){

        $clients = Client::with('referrer', 'process.steps.activities.actionable.data', 'introducer')
            ->select('*', DB::raw("CONCAT(first_name,' ',COALESCE(`last_name`,'')) as full_name"))
            ->where('is_progressing','1')
            ->where('is_qa','0');

        if ($request->has('s') && $request->input('s') != '') {
            $clients->where(function ($query) use ($request) {
                $query->where('company', 'like', "%" . $request->input('s') . "%")
                    ->orWhere('first_name', 'like', "%" . $request->input('s') . "%")
                    ->orWhere('last_name', 'like', "%" . $request->input('s') . "%")
                    ->orWhere('email', 'like', "%" . $request->input('s') . "%");
            });
        }

        $orgonogram = new RelatedParty();


        $parameters = [ 'clients' => $clients->get(),
            'templates' => Template::orderBy('name')->where('template_type_id',1)->pluck('name','id')->prepend('Select','0')
        ];

        return view('reports.generate_report')->with($parameters);

    }

    public function generateReportExport(GenerateReportRequest $request){
        //dd($request);
        $template = Template::where('id', $request->input('template'))->first();

        if($template->type() == 'docx'){
            return $this->wordExport($request->input('client_id'),$template->process_id,$template->id,$request->input('report_reason'),$request->input('report_description'),$request->input('report_committee'),$request->input('report_date'));
        }

        if($template->type() == 'pptx'){
            return $this->powerpointExport($request->input('client_id'),$template->process_id,$template->id,$request->input('report_reason'),$request->input('report_description'),$request->input('report_committee'),$request->input('report_date'));
        }
    }

    public function wordExport($client_id,$process_id,$template_id,$reason,$title,$committee,$report_date){
        $client = Client::where('id',$client_id)->first();

        $template_file = Template::withTrashed()->where('id',$template_id)->first();

        $processed_template1 = $this->wordPrimaryExport($client_id,$process_id,$template_id,$reason,$title,$committee,$report_date);
        $processed_template2 = $this->wordRelatedPartyExport($client_id,$process_id,$template_id,$reason,$title,$committee,$report_date);

        if($processed_template2) {
            $dm = new DocxMerge();
            $dm->addFiles([
                storage_path("app/forms/" . $processed_template1),
                storage_path("app/forms/" . $processed_template2)
            ])->save(storage_path('app/forms/' . str_replace(' ', '_', $template_file->name) . '_' . ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.$client->last_name)) . '.docx'), true);

            //shell_exec('libreoffice --headless --convert-to pdf '.storage_path('app/forms/' .$processed_template).' --outdir '.storage_path('app/forms/' . $processed_template_path));
            return Storage::download('forms/' . str_replace(' ', '_', $template_file->name) . '_' . ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.$client->last_name)) . '.docx');
        } else {
            return Storage::download('forms/' . $processed_template1);
        }
    }

    public function wordPrimaryExport($client_id,$process_id,$template_id,$reason,$title,$committee,$report_date){

        $total = 1;
        $client = Client::where('id',$client_id)->first();
        $client_cnt = Client::select('id')->where('id',$client_id)->first();

        $string = ($client->company != null ? preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.' '.$client->last_name));

        $name = strtolower(str_replace(' ','_',$string));

        $total = $total + count(collect($client_cnt)->toArray());

        $related_party_tree = RelatedPartiesTree::select('related_party_id')->get();

        $related_parties = RelatedParty::select('id')->where('client_id', $client_id)->whereIn('id',collect($related_party_tree)->toArray())->get();

        $total = $total + count(collect($related_parties)->toArray());

        $template_file = Template::withTrashed()->where('id',$template_id)->first();

        $steps = Step::with(['activities.actionable.data'=>function ($q) use ($client_id){
            $q->where('client_id',$client_id);
        }])->get();


        $templateProcessor = new TemplateProcessor(storage_path('app/templates/' . $template_file->file));
        $templateProcessor->setImageValue('image',array('path' => storage_path('/app/forms/'.$name.'.jpg'), 'width' => 1600, 'height' => 250, 'ratio' => true));
        $templateProcessor->setValue('${date}', date("Y/m/d"));
        $templateProcessor->setValue('${report_name}', (isset($reason) ? htmlentities($reason) : ''));
        $templateProcessor->setValue('${report_title}', (isset($title) ? htmlentities($title) : ''));
        $templateProcessor->setValue('${report_committee}', (isset($committee) ? htmlentities($committee) : ''));
        $templateProcessor->setValue('${report_date}', (isset($report_date) ? $report_date : ''));
        $templateProcessor->setValue('${client_name}', ($client->company != null ? htmlentities($client->company) : htmlentities($client->first_name.' '.$client->last_name)));
        $templateProcessor->setValue('${client.cif_code}', ($client->cif_code != null ? '('.$client->cif_code.')' : ''));
        $templateProcessor->setValue('${client.id_number}', ($client->company != null ? htmlentities($client->company_registration_number) : ''));
        $templateProcessor->setValue('${total}', $total);

        $var_array = array();
        $value_array = array();

        foreach(collect($client)->toArray() as $column_name => $value) {
            $exclude = ['referrer_id','introducer_id','deleted_at','created_at','updated_at','office_id','process_id','step_id','is_progressing','not_progressing_date','completed_at','needs_approval','id'];
            if(!in_array($column_name,$exclude)) {
                array_push($var_array, 'client.' . $column_name);
                array_push($value_array, $value);
            }
        }

        foreach($steps as $step) {

            $group_count = 0;

            if($step->group > 0) {
                $group_count = $client->groupCompletedActivities(Step::find($step->id), $client->id);
                $group_count = (int)$group_count;
                //dd((int)$group_count);
            }

            foreach ($step["activities"] as $activity) {
                $var = '';
                switch ($activity['actionable_type']){
                    case 'App\ActionableDropdown':
                        if($activity->grouping != null && $activity->grouping > 0) {
                            $variable = 'activity.'.strtolower(str_replace(' ', '_', $step->name)).'.' . strtolower(str_replace(' ', '_', $activity->name));
                            try {
                                $templateProcessor->cloneRow($variable, $group_count);
                            } catch (\Exception $e) {

                            }
                            //$var = 'activity.' . strtolower(str_replace(' ', '_', $activity->name.'#'.$activity->grouping));
                        }
                        //array_push($var_array,$var);
                        break;

                    default:
                        if($activity->grouping != null && $activity->grouping > 0) {
                            $variable = 'activity.'.strtolower(str_replace(' ', '_', $step->name)).'.' . strtolower(str_replace(' ', '_', $activity->name));

                            try {
                                $templateProcessor->cloneRow($variable, $group_count);
                            } catch (\Exception $e) {

                            }
                            //$var = 'activity.' . strtolower(str_replace(' ', '_', $activity->name.'#'.$activity->grouping));
                        }
                        //array_push($var_array, $var);
                        break;
                }


                if (isset($activity["actionable"]->data) && count($activity["actionable"]->data) > 0) {

                    foreach ($activity["actionable"]->data as $value) {
                        if($value->client_id == $client_id) {

                            switch ($activity['actionable_type']) {
                                case 'App\ActionableDropdown':
                                    $dropdown = ActionableDropdownData::where('client_id',$client_id)->where('actionable_dropdown_id',$value->actionable_dropdown_id)->first();
                                    $data = ActionableDropdownItem::where('id', $dropdown->actionable_dropdown_item_id)->first();

                                    if($activity->name == 'New / Existing'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.new_/_existing}', ($data ? $data["name"] : ''));
                                    }
                                    if($activity->name == 'KYC status'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.kyc_status}', ($data ? $data["name"] : ''));
                                    }
                                    if($activity->name == 'CASA'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.casa}', ($data ? $data["name"] : ''));
                                    }
                                    if($activity->name == 'EDD rating'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.edd_rating}', ($data ? $data["name"] : ''));
                                    }

                                    break;
                                case 'App\ActionableDate':

                                    $data = ActionableDateData::where('client_id',$client_id)->where('actionable_date_id',$value->actionable_date_id)->first();

                                    if($activity->name == 'KYC last verified'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.kyc_last_verified}', ($data && $data != null && $data["data"] != '' ? '('.$data["data"].')' : ''));
                                    }
                                    if($activity->name == 'Existing ABSA client since'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.existing_absa_client_since}', ($data && $data != null && $data["data"] != '' ? '('.$data["data"].')' : ''));
                                    }

                                    if($activity->name == 'Date of EDD report'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.date_of_edd_report}', ($data && $data != null && $data["data"] != '' ? $data["data"] : ''));
                                    }

                                    if($activity->name == 'Date opened'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.date_opened}', ($data && $data != null && $data["data"] != '' ? '('.$data["data"].')' : ''));
                                    }
                                    if ($activity->grouping != null && $activity->grouping > 0) {
                                        $variable = 'activity.' . strtolower(str_replace(' ', '_', $activity->name));
                                        try {
                                            $templateProcessor->cloneRow($variable, $group_count);
                                        } catch (\Exception $e) {

                                        }
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.' . strtolower(str_replace(' ', '_', $activity->name . '#' . $activity->grouping)).'}',($data["data"] != null ? $data["data"] : 'N/A'));
                                    }

                                    break;
                                case 'App\ActionableBoolean':
                                    $items = ActionableBooleanData::where('client_id', $client_id)->where('actionable_boolean_id', $value->actionable_boolean_id)->first();


                                    if($activity->name == 'PEP'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.pep}', ($items && $items->data == '0' ? 'No' : 'Yes'));
                                    }
                                    elseif($activity->name == 'Sanctions'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.sanctions}', ($items && $items->data == '0' ? 'No' : 'Yes'));
                                    }
                                    elseif($activity->name == '# STRs'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.#_strs}', ($items && $items->data == '0' ? 'No' : 'Yes'));
                                    }
                                    elseif($activity->name == 'Litigation'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.litigation}', ($items && $items->data == '0' ? 'No' : 'Yes'));
                                    }
                                    elseif($activity->name == 'Adverse Media'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.adverse_media}', ($items && $items->data == '0' ? 'No' : 'Yes'));
                                    }


                                    break;
                                case 'App\ActionableTextarea':
                                    $parser = new HTMLtoOpenXML\Parser();

                                    $value = ActionableTextareaData::where('client_id', $client_id)->where('actionable_textarea_id', $value->actionable_textarea_id)->orderBy('id','DESC')->first();

                                    \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(false);
                                    $ooXml = $parser->fromHTML($value["data"]);
                                    if ($activity->name == 'Background') {
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.background}', ($ooXml ? $ooXml : ''));
                                    }
                                    if ($activity->name == 'Recommendation') {
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.recommendation}', ($ooXml ? $ooXml : ''));
                                    }
                                    if ($activity->name == 'Reputational Risk Considerations') {
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.reputational_risk_considerations}', ($ooXml ? $ooXml : ''));
                                    }
                                    if ($activity->name == 'Additional investigation notes') {

                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.additional_investigation_notes}', ($ooXml ? $ooXml : ''));
                                    }
                                    if ($activity->name == 'Complete field') {
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.complete_field}', ($ooXml ? $ooXml : ''));
                                    }

                                    \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

                                    break;
                                case 'App\ActionableText':


                                    $value = ActionableTextData::where('client_id', $client_id)->where('actionable_text_id', $value->actionable_text_id)->orderBy('id','DESC')->first();


                                    if ($activity->name == 'TA Review Period') {
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.ta_review_period}', ($value["data"] ? htmlspecialchars($value["data"]) : ''));
                                    }
                                    if ($activity->name == 'EDD rating') {
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.edd_rating}', ($value["data"] ? htmlspecialchars($value["data"]) : ''));
                                    }
                                    if ($activity->name == 'Relationship') {
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.relationship}', ($value["data"] ? htmlspecialchars($value["data"]) : ''));
                                    }
                                    if ($activity->name == 'Expected Account Activity') {
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.expected_account_activity}', ($value["data"] ? htmlspecialchars($value["data"]) : ''));
                                    }
                                    if($activity->name == '# STRs'){
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.#_strs}', ($value["data"] ? htmlspecialchars($value["data"]) : ''));
                                    }

                                    if ($activity->grouping != null && $activity->grouping > 0) {
                                        $variable = 'activity.' . strtolower(str_replace(' ', '_', $activity->name));
                                        try {
                                            $templateProcessor->cloneRow($variable, $group_count);
                                        } catch (\Exception $e) {

                                        }
                                        $templateProcessor->setValue('${activity.' . strtolower(str_replace(' ', '_', $activity->name . '#' . $activity->grouping)).'}',($value["data"] != null ? htmlspecialchars($value["data"]) : 'N/A'));
                                    }


                                    break;

                                default:


                                    if ($activity->grouping != null && $activity->grouping > 0) {
                                        $variable = 'activity.' . strtolower(str_replace(' ', '_', $activity->name));
                                        try {
                                            $templateProcessor->cloneRow($variable, $group_count);
                                        } catch (\Exception $e) {

                                        }
                                        $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.' . strtolower(str_replace(' ', '_', $activity->name . '#' . $activity->grouping)).'}',($value->data != null ? $value->data : 'N/A'));
                                    }
                                    //array_push($value_array, htmlentities($value->data));
                                    break;
                            }
                        }
                    }
                } else {

                    switch ($activity['actionable_type']) {
                        case 'App\ActionableDropdown':

                            //array_push($value_array, '');
                            if ($activity->name == 'EDD rating') {
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.edd_rating}', '');
                            }

                            if ($activity->name == 'KYC status') {
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.kyc_status}', '');
                            }

                            if ($activity->name == 'New / Existing') {
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.new_/_existing}', '');
                            }

                            break;
                        default:
                            if($activity->name == 'KYC last verified'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.kyc_last_verified}', '');
                            }
                            if($activity->name == 'Existing ABSA client since'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.existing_absa_client_since}', '');
                            }

                            if($activity->name == 'Date of EDD report'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.date_of_edd_report}', '');
                            }

                            if($activity->name == 'Date opened'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.date_opened}', '');
                            }
                            if($activity->name == 'PEP'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.pep}', '');
                            }
                            elseif($activity->name == 'Sanctions'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.sanctions}', '');
                            }
                            elseif($activity->name == '# STRs'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.#_strs}', '');
                            }
                            elseif($activity->name == 'Litigation'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.litigation}', '');
                            }
                            elseif($activity->name == 'Adverse Media'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.adverse_media}', '');
                            }
                            if ($activity->name == 'TA Review Period') {
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.ta_review_period}', '');
                            }

                            if ($activity->name == 'Relationship') {
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.relationship}', '');
                            }

                            if ($activity->name == 'Background') {
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.background}', '');
                            }
                            if ($activity->name == 'Complete field') {
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.complete_field}', '');
                            }
                            if ($activity->name == 'Recommendation') {
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.recommendation}', '');
                            }
                            if ($activity->name == 'Reputational Risk Considerations') {
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.reputational_risk_considerations}', '');
                            }
                            if ($activity->name == 'Additional investigation notes') {
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.additional_investigation_notes}', '');
                            }
                            if ($activity->name == 'Expected Account Activity') {
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.expected_account_activity}', '');
                            }



                            if($activity->name == 'New / Existing'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.new_/_existing}', '');
                            }
                            if($activity->name == 'KYC status'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.kyc_status}', '');
                            }
                            if($activity->name == 'CASA'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.casa}', '');
                            }

                            if($activity->name == 'Product'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.product}', 'N/A');
                            }
                            if($activity->name == 'Turn Over (Current year)'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.turn_over_(current_year)}', 'N/A');
                            }
                            if($activity->name == 'Turn Over (Previous year)'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.turn_over_(previous_year)}', 'N/A');
                            }
                            if($activity->name == 'Balance'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.balance}', 'N/A');
                            }
                            if($activity->name == 'Date opened'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.date_opened}', 'N/A');
                            }
                            if($activity->name == 'Limit'){
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.limit}', 'N/A');
                            }

                            if ($activity->grouping != null && $activity->grouping > 0) {
                                $variable = 'activity.' . strtolower(str_replace(' ', '_', $activity->name));
                                try {
                                    $templateProcessor->cloneRow($variable, $group_count);
                                } catch (\Exception $e) {

                                }
                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.' . strtolower(str_replace(' ', '_', $activity->name . '#' . $activity->grouping)).'}', 'N/A');
                            }
                            //array_push($value_array, '');
                            break;
                    }
                }
            }

        }


        //Create directory to store processed templates, for future reference or to check what was sent to the client
        $processed_template_path = 'processedforms/'.($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.$client->last_name)).'/';
        if (!File::exists(storage_path('app/forms/' . $processed_template_path))) {
            Storage::makeDirectory('forms/' . $processed_template_path);
        }
        $filename = explode('.',$template_file);

        $processed_template = $processed_template_path . DIRECTORY_SEPARATOR . str_replace(' ','_',$template_file->name). "_" . ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.$client->last_name)) . "(1).docx";
        if(File::exists(storage_path('app/forms/' . $processed_template))){
            Storage::delete('forms/' . $processed_template);
        }

        $templateProcessor->saveAs(storage_path('app/forms/' . $processed_template));

        return $processed_template;
    }

    public function wordRelatedPartyExport($client_id,$process_id,$template_id,$reason,$title,$committee,$report_date){


        $total = 1;
        $client = Client::where('id',$client_id)->first();
        $client_cnt = Client::select('id')->where('id',$client_id)->first();

        $total = $total + count(collect($client_cnt)->toArray());

        $related_party_tree = RelatedPartiesTree::select('related_party_id')->get();

        $related_parties = RelatedParty::where('client_id', $client_id)->whereIn('id',collect($related_party_tree)->toArray())->get();
        $related_parties_cnt = RelatedParty::select('id')->where('client_id', $client_id)->whereIn('id',collect($related_party_tree)->toArray())->get();

        $total = $total + count(collect($related_parties_cnt)->toArray());

        $i = 2;
        $docx_array = array();

        foreach ($related_parties as $related_party) {
            $template_file = Template::withTrashed()->where('id',$template_id)->first();
            
            $related_party_id = $related_party->id;
            $processes = Process::select('id')->where('process_type_id',2)->get();
            $steps = Step::with(['activities.actionable.data' => function ($q) use ($client_id,$related_party_id) {
                $q->where('related_party_id',$related_party_id)->where('client_id',$client_id);
            }])->whereIn('process_id',collect($processes)->toArray())->get();


            $templateProcessor = new TemplateProcessor(storage_path('app/templates/' . $template_file->file2));
            $templateProcessor->setValue('${date}', date("Y/m/d"));
            $templateProcessor->setValue('${report_name}', (isset($reason) ? htmlentities($reason) : ''));
            $templateProcessor->setValue('${report_title}', (isset($title) ? htmlentities($title) : ''));
            $templateProcessor->setValue('${report_committee}', (isset($committee) ? htmlentities($committee) : ''));
            $templateProcessor->setValue('${report_date}', (isset($report_date) ? htmlentities($report_date) : ''));
            $templateProcessor->setValue('${client_name}', ($client->company != null ? htmlentities($client->company) : htmlentities($client->first_name . ' ' . $client->last_name)));
            $templateProcessor->setValue('${related_party_name}', ($related_party->company != null ? htmlentities($related_party->company) : htmlentities($related_party->first_name . ' ' . $related_party->last_name)));
            $templateProcessor->setValue('${related_party.cif_code}', ($related_party->cif_code != null ? '('.$related_party->cif_code.')' : ''));
            $templateProcessor->setValue('${related_party.id_number}', ($related_party->company != null ? $related_party->company_registration_number : ''));
            $templateProcessor->setValue('${number}', $i);
            $templateProcessor->setValue('${total}', $total);
            $templateProcessor->setValue('${page}', ($i+1));


            $var_array = array();
            $value_array = array();

            foreach (collect($related_party)->toArray() as $column_name => $value) {
                $exclude = ['referrer_id', 'introducer_id', 'deleted_at', 'created_at', 'updated_at', 'office_id', 'process_id', 'step_id', 'is_progressing', 'not_progressing_date', 'completed_at', 'needs_approval', 'id'];
                if (!in_array($column_name, $exclude)) {
                    array_push($var_array, 'related_party.' . $column_name);
                    array_push($value_array, $value);
                }
            }

            foreach ($steps as $step) {

                $group_count = 0;

                if ($step->group > 0) {
                    $group_count = $client->groupRelatedPartyCompletedActivities(Step::find($step->id), $related_party->id);
                    $group_count = (int)$group_count;
                    //dd((int)$group_count);
                }

                foreach ($step["activities"] as $activity) {
                    $var = '';
                    switch ($activity['actionable_type']){
                        case 'App\ActionableDropdown':
                            if($activity->grouping != null && $activity->grouping > 0) {
                                $variable = 'activity.'.strtolower(str_replace(' ', '_', $step->name)).'.' . strtolower(str_replace(' ', '_', $activity->name));
                                try {
                                    $templateProcessor->cloneRow($variable, $group_count);
                                } catch (\Exception $e) {

                                }
                                //$var = 'activity.' . strtolower(str_replace(' ', '_', $activity->name.'#'.$activity->grouping));
                            }
                            //array_push($var_array,$var);
                            break;

                        default:
                            if($activity->grouping != null && $activity->grouping > 0) {
                                $variable = 'activity.'.strtolower(str_replace(' ', '_', $step->name)).'.' . strtolower(str_replace(' ', '_', $activity->name));
                                try {
                                    $templateProcessor->cloneRow($variable, $group_count);
                                } catch (\Exception $e) {

                                }
                                //$var = 'activity.' . strtolower(str_replace(' ', '_', $activity->name.'#'.$activity->grouping));
                            }
                            //array_push($var_array, $var);
                            break;
                    }


                    if (isset($activity["actionable"]->data) && count($activity["actionable"]->data) > 0) {

                        foreach ($activity["actionable"]->data as $value) {
                            if($value->related_party_id == $related_party_id) {

                                switch ($activity['actionable_type']) {
                                    case 'App\RelatedPartyDropdown':
                                        $dropdown = RelatedPartyDropdownData::where('related_party_id',$related_party_id)->where('related_party_dropdown_id',$value->related_party_dropdown_id)->first();
                                        $data = RelatedPartyDropdownItem::where('id', $dropdown->related_party_dropdown_item_id)->first();

                                            if($activity->name == 'New / Existing'){
                                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.new_/_existing}', ($data ? $data["name"] : ''));
                                            }
                                            if($activity->name == 'KYC status'){
                                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.kyc_status}', ($data ? $data["name"] : ''));
                                            }
                                            if($activity->name == 'CASA'){
                                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.casa}', ($data ? $data["name"] : ''));
                                            }
                                        if($activity->name == 'EDD rating'){
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.edd_rating}', ($data ? $data["name"] : ''));
                                        }

                                        break;
                                    case 'App\RelatedPartyDate':

                                        $data = RelatedPartyDateData::where('client_id',$client_id)->where('related_party_id',$related_party_id)->where('related_party_date_id',$value->related_party_date_id)->orderBy('id','desc')->first();

                                        if($activity->name == 'KYC last verified'){
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.kyc_last_verified}', ($data && $data != null && $data["data"] != '' ? '('.$data["data"].')' : ''));
                                        }
                                        if($activity->name == 'Existing ABSA client since'){
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.existing_absa_client_since}', ($data && $data != null && $data["data"] != '' ? '('.$data["data"].')' : ''));
                                        }

                                        if($activity->name == 'Date of EDD report'){
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.date_of_edd_report}', ($data && $data != null && $data["data"] != '' ? $data["data"] : ''));
                                        }

                                        /*if($activity->name == 'Date opened'){
                                            $templateProcessor->setValue('${activity.date_opened}', ($data ? '('.$data["data"].')' : ''));
                                        }*/
                                        if ($activity->grouping != null && $activity->grouping > 0) {
                                            $variable = 'activity.' . strtolower(str_replace(' ', '_', $activity->name));
                                            try {
                                                $templateProcessor->cloneRow($variable, $group_count);
                                            } catch (\Exception $e) {

                                            }
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.' . strtolower(str_replace(' ', '_', $activity->name . '#' . $activity->grouping)).'}',($data["data"] != null ? $data["data"] : 'N/A'));
                                        }

                                        break;
                                    case 'App\RelatedPartyBoolean':
                                        $items = RelatedPartyBooleanData::where('client_id', $client_id)->where('related_party_id', $related_party_id)->where('related_party_boolean_id', $value->related_party_boolean_id)->first();


                                            if($activity->name == 'PEP'){
                                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.pep}', ($items && $items->data == '0' ? 'No' : 'Yes'));
                                            }
                                            elseif($activity->name == 'Sanctions'){
                                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.sanctions}', ($items && $items->data == '0' ? 'No' : 'Yes'));
                                            }
                                            elseif($activity->name == '# STRs'){
                                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.#_strs}', ($items && $items->data == '0' ? 'No' : 'Yes'));
                                            }
                                            elseif($activity->name == 'Litigation'){
                                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.litigation}', ($items && $items->data == '0' ? 'No' : 'Yes'));
                                            }
                                            elseif($activity->name == 'Adverse Media'){
                                                $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.adverse_media}', ($items && $items->data == '0' ? 'No' : 'Yes'));
                                            }


                                        break;
                                    case 'App\RelatedPartyTextarea':
                                        $parser = new HTMLtoOpenXML\Parser();

                                        $value = RelatedPartyTextareaData::where('client_id', $client_id)->where('related_party_id', $related_party_id)->where('related_party_textarea_id', $value->related_party_textarea_id)->orderBy('id','DESC')->first();

                                        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(false);
                                        $ooXml = $parser->fromHTML($value["data"]);
                                        if ($activity->name == 'Background') {
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.background}', ($ooXml ? $ooXml : ''));
                                        }
                                        if ($activity->name == 'Recommendation') {
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.recommendation}', ($ooXml ? $ooXml : ''));
                                        }
                                        if ($activity->name == 'Reputational Risk Considerations') {
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.reputational_risk_considerations}', ($ooXml ? $ooXml : ''));
                                        }
                                        if ($activity->name == 'Additional investigation notes') {
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.additional_investigation_notes}', ($ooXml ? $ooXml : ''));
                                        }
                                        if ($activity->name == 'Complete field') {
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.complete_field}', ($ooXml ? $ooXml : ''));
                                        }

                                        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);

                                        break;
                                    case 'App\RelatedPartyText':


                                        $value = RelatedPartyTextData::where('client_id', $client_id)->where('related_party_text_id', $value->related_party_text_id)->where('related_party_id', $related_party_id)->orderBy('id','DESC')->first();


                                        if ($activity->name == 'TA Review Period') {
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.ta_review_period}', ($value["data"] ? htmlspecialchars($value["data"]) : ''));
                                        }
                                        if ($activity->name == 'EDD rating') {
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.edd_rating}', ($value["data"] ? htmlspecialchars($value["data"]) : ''));
                                        }
                                        if ($activity->name == 'Relationship') {
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.relationship}', ($value["data"] ? htmlspecialchars($value["data"]) : ''));
                                        }
                                        if ($activity->name == 'Expected Account Activity') {
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.expected_account_activity}', ($value["data"] ? htmlspecialchars($value["data"]) : ''));
                                        }
                                        if($activity->name == '# STRs'){
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.#_strs}', ($value["data"] ? htmlspecialchars($value["data"]) : ''));
                                        }

                                        if ($activity->grouping != null && $activity->grouping > 0) {
                                            $variable = 'activity.' . strtolower(str_replace(' ', '_', $activity->name));
                                            try {
                                                $templateProcessor->cloneRow($variable, $group_count);
                                            } catch (\Exception $e) {

                                            }
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.' . strtolower(str_replace(' ', '_', $activity->name . '#' . $activity->grouping)).'}',($value["data"] != null ? htmlspecialchars($value["data"]) : 'N/A'));
                                        }

                                        break;

                                    default:

                                        if ($activity->grouping != null && $activity->grouping > 0) {
                                            $variable = 'activity.' . strtolower(str_replace(' ', '_', $activity->name));
                                            try {
                                                $templateProcessor->cloneRow($variable, $group_count);
                                            } catch (\Exception $e) {

                                            }
                                            $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.' . strtolower(str_replace(' ', '_', $activity->name . '#' . $activity->grouping)).'}',($value->data != null ? $value->data : 'N/A'));
                                        }
                                        //array_push($value_array, htmlentities($value->data));
                                        break;
                                }
                            }
                        }
                    } else {

                        switch ($activity['actionable_type']) {
                            case 'App\RelatedPartyDropdown':

                                if ($activity->name == 'EDD rating') {
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.edd_rating}', '');
                                }

                                if($activity->name == 'New / Existing'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.new_/_existing}', '');
                                }
                                if($activity->name == 'KYC status'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.kyc_status}', '');
                                }
                                if($activity->name == 'CASA'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.casa}', '');
                                }


                                break;
                            default:
                                if($activity->name == 'KYC last verified'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.kyc_last_verified}', '');
                                }
                                if($activity->name == 'Existing ABSA client since'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.existing_absa_client_since}', '');
                                }

                                if($activity->name == 'Date of EDD report'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.date_of_edd_report}', '');
                                }
                                if ($activity->name == 'TA Review Period') {
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.ta_review_period}', '');
                                }

                                if ($activity->name == 'Relationship') {
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.relationship}', '');
                                }
                                if ($activity->name == 'Expected Account Activity') {
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.expected_account_activity}', '');
                                }

                                if ($activity->name == 'Background') {
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.background}', '');
                                }
                                if ($activity->name == 'Complete field') {
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.complete_field}', '');
                                }
                                if ($activity->name == 'Recommendation') {
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.recommendation}', '');
                                }
                                if ($activity->name == 'Reputational Risk Considerations') {
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.reputational_risk_considerations}', '');
                                }
                                if ($activity->name == 'Additional investigation notes') {
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.additional_investigation_notes}', '');
                                }

                                if($activity->name == 'PEP'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.pep}', '');
                                }
                                elseif($activity->name == 'Sanctions'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.sanctions}', '');
                                }
                                elseif($activity->name == '# STRs'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.#_strs}', '');
                                }
                                elseif($activity->name == 'Litigation'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.litigation}', '');
                                }
                                elseif($activity->name == 'Adverse Media'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.adverse_media}', '');
                                }
                                elseif($activity->name == 'CASA'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.casa}', '');
                                }



                                if($activity->name == 'Product'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.product}', 'N/A');
                                }
                                if($activity->name == 'Turn Over (Current year)'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.turn_over_(current_year)}', 'N/A');
                                }
                                if($activity->name == 'Turn Over (Previous year)'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.turn_over_(previous_year)}', 'N/A');
                                }
                                if($activity->name == 'Balance'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.balance}', 'N/A');
                                }
                                if($activity->name == 'Date opened'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.date_opened}', 'N/A');
                                }
                                if($activity->name == 'Limit'){
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.limit}', 'N/A');
                                }

                                if ($activity->grouping != null && $activity->grouping > 0) {
                                    $variable = 'activity.' . strtolower(str_replace(' ', '_', $activity->name));
                                    try {
                                        $templateProcessor->cloneRow($variable, $group_count);
                                    } catch (\Exception $e) {

                                    }
                                    $templateProcessor->setValue('${activity.'.strtolower(str_replace(' ', '_', $step->name)).'.' . strtolower(str_replace(' ', '_', $activity->name . '#' . $activity->grouping)).'}', 'N/A');
                                }
                                //array_push($value_array, '');
                                break;
                        }
                    }
                }

            }

            /*$templateProcessor->setValue(
                $var_array, $value_array
            );*/

            //Create directory to store processed templates, for future reference or to check what was sent to the client
            $processed_template_path = 'processedforms/' . ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.$client->last_name)) . '/';

            $filename = explode('.', $template_file);

            $processed_template = $processed_template_path . DIRECTORY_SEPARATOR . str_replace(' ', '_', $template_file->name) . "_" . ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.$client->last_name)) . "(".$i.").docx";


            $templateProcessor->saveAs(storage_path('app/forms/' . $processed_template));

            array_push($docx_array,storage_path('app/forms/' . $processed_template));

            $i++;

        }
        if(isset($processed_template_path)) {
            $dm = new DocxMerge();
            $dm->addFiles($docx_array)->save(storage_path('app/forms/' .$processed_template_path . DIRECTORY_SEPARATOR . str_replace(' ', '_', $template_file->name) . "_" . ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.$client->last_name)) . '-rp2.docx'), true);


            return $processed_template_path . DIRECTORY_SEPARATOR . str_replace(' ', '_', $template_file->name) . "_" . ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.$client->last_name)). '-rp2.docx';
        } else {
            return;
        }

    }
    /**
     * Export Activity data to powerpoint
     *
     * @param Int client_id
     * @param Request $request
     *
     * @return void
     */
    public function generatePPTX($client_id,$process_id,$reason,$title,$committee,$date){
        $objPHPPowerPoint = new PhpPresentation();
        /*$objPHPPowerPoint->getProperties()->setCreator('Sketch Presentation')
            ->setLastModifiedBy('Sketch Team')
            ->setTitle('Sketch Presentation')
            ->setSubject('Sketch Presentation')
            ->setDescription('Sketch Presentation')
            ->setKeywords('office 2007 openxml libreoffice odt php')
            ->setCategory('Sample Category');*/
        $objPHPPowerPoint->removeSlideByIndex(0);

        // Grab the client
        $client = Client::with('related_parties')->where('id',$client_id);

        // Client details in an array
        $client = $client->first()->toArray();

        $total = count($client['related_parties']) + 2;

        $this->slide0($objPHPPowerPoint,$client_id,$process_id,$total,$reason,$title,$committee,$date);
        $this->slide1($objPHPPowerPoint,$client_id,$process_id,$total,$reason,$title,$committee,$date);

        if(count($client['related_parties']) > 0) {
            for ($i = 0; $i < count($client['related_parties']); $i++) {
                //dd($client['related_parties'][$i]['description']);

                // loop over steps to get the activity names, storing them in an assoc. array
                $related_party = $client['related_parties'][$i];
                $related_party_process = $client['related_parties'][$i]['process_id'];
                $related_party_id = $client['related_parties'][$i]['id'];
                $this->slide2($objPHPPowerPoint,$client_id,$related_party_process,$i,$related_party_id,$total,$reason,$title,$committee,$date);
            }
        }

        $oWriterPPTX = IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
        return $oWriterPPTX->save(storage_path("app/templates/sample.pptx"));
    }

    public function slide0(&$objPHPPowerPoint,$client_id,$process_id,$total,$reason,$title,$committee,$date){
        $random = Client::where('id',$client_id)->first();

        $string = ($random->company != null ? preg_replace('/[^a-zA-Z0-9_ -]/s','',$random->company) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$random->first_name.' '.$random->last_name));

        $name = strtolower(str_replace(' ','_',$string));

        if(File::exists(storage_path('app/forms/')."$name.jpg")) {
            $treeview = storage_path('app/forms/') . "$name.jpg";
        } else {
            $treeview = '';
        }

        // Create slide
        $currentSlide = $objPHPPowerPoint->createSlide();

        $oFill = new Fill();
        $oFill->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color(Color::COLOR_DARKRED));
        $shape = new Drawing\File();
        $shape->setName('PHPPresentation logo')
            ->setDescription('PHPPresentation logo')
            ->setPath(storage_path('app/avatars/Capture.png'))
            ->setHeight(720)
            ->setOffsetX(953)
            ->setOffsetY(0)
            ->setFill($oFill);
        $currentSlide->addShape($shape);

        $shape = new Drawing\File();
        $shape->setName('PHPPresentation logo')
            ->setDescription('PHPPresentation logo')
            ->setPath($treeview)
            ->setWidth(400)
            ->setOffsetX(250)
            ->setOffsetY(20);
        $currentSlide->addShape($shape);

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(600)
            ->setOffsetX(10)
            ->setOffsetY(20);
        $shape->getActiveParagraph()->getAlignment()->setMarginLeft(0)->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $textRun = $shape->createTextRun($reason);
        $textRun->getFont()->setBold(false)
            ->setSize(16)
            ->setName('Arial')
            ->setColor( new Color( 'FFc00000' ) );

        // Create a shape (drawing)
        $shape = $currentSlide->createTableShape(1);
        $shape->setWidth(915);
        $shape->setOffsetX(20);
        $shape->setOffsetY(660);

        $row = $shape->createRow();
        $row->setHeight(10);
        $cell = $row->nextCell();
        $cell->createTextRun('1  |  '.$title.'  |  '.$committee.'  |  '.$date)->getFont()->setBold(false)->setSize(8)->setColor(new Color('FF000000'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setMarginLeft(5)->setMarginTop(10);
        $cell->createBreak();
        $cell->createTextRun('SECRET CONFIDENTIAL INTERNAL ONLY')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(0)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FF000000'));
            $cell->getBorders()->getLeft()->setLineWidth(0)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(0)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
        }

        $oFill = new Fill();
        $oFill->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color(Color::COLOR_DARKRED));
        $shape = new Drawing\File();
        $shape->setName('PHPPresentation logo')
            ->setDescription('PHPPresentation logo')
            ->setPath(storage_path('app/avatars/absa-logo.png'))
            ->setHeight(30)
            ->setOffsetX(900)
            ->setOffsetY(670)
            ->setFill($oFill);
        $currentSlide->addShape($shape);

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(600)
            ->setOffsetX(10)
            ->setOffsetY(50);
        $shape->getActiveParagraph()->getAlignment()->setMarginLeft(0)->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $textRun = $shape->createTextRun('${client.first_name} ${client.last_name}');
        $textRun->getFont()->setBold(true)
            ->setSize(12)
            ->setName('Arial')
            ->setColor( new Color( 'FF000000' ) );

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(400)
            ->setOffsetX(600)
            ->setOffsetY(50);
        $shape->getActiveParagraph()->getAlignment()->setMarginLeft(0)->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $textRun = $shape->createTextRun('${activity.management_executive}');
        $textRun->getFont()->setBold(true)
            ->setSize(12)
            ->setName('Arial')
            ->setColor( new Color( 'FF000000' ) );

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(100)
            ->setOffsetX(840)
            ->setOffsetY(50);
        $shape->getActiveParagraph()->getAlignment()->setMarginLeft(0)->setHorizontal( Alignment::HORIZONTAL_RIGHT );
        $textRun = $shape->createTextRun('1/'.$total);
        $textRun->getFont()->setBold(true)
            ->setSize(12)
            ->setName('Arial')
            ->setColor( new Color( 'FF000000' ) );
    }

    public function slide1(&$objPHPPowerPoint,$client_id,$process_id,$total,$reason,$title,$committee,$date)
    {

        $client = Client::where('id', $client_id)->first();

        $steps = Step::with(['activities.actionable.data' => function ($q) use ($client_id) {
            $q->where('client_id', $client_id);
        }])->where('process_id', $process_id)->where('group', 1)->get();

        // Create slide
        $currentSlide = $objPHPPowerPoint->createSlide();

        $oFill = new Fill();
        $oFill->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color(Color::COLOR_DARKRED));
        $shape = new Drawing\File();
        $shape->setName('PHPPresentation logo')
            ->setDescription('PHPPresentation logo')
            ->setPath(storage_path('app/avatars/Capture.png'))
            ->setHeight(720)
            ->setOffsetX(953)
            ->setOffsetY(0)
            ->setFill($oFill);
        $currentSlide->addShape($shape);

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(600)
            ->setOffsetX(10)
            ->setOffsetY(20);
        $shape->getActiveParagraph()->getAlignment()->setMarginLeft(0)->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $textRun = $shape->createTextRun($reason);
        $textRun->getFont()->setBold(false)
            ->setSize(16)
            ->setName('Arial')
            ->setColor(new Color('FFc00000'));

        // Create a shape (drawing)
        $shape = $currentSlide->createTableShape(1);
        $shape->setWidth(915);
        $shape->setOffsetX(20);
        $shape->setOffsetY(660);

        $row = $shape->createRow();
        $row->setHeight(10);
        $cell = $row->nextCell();
        $cell->createTextRun('2  |  ' . $title . '  |  ' . $committee . '  |  ' . $date)->getFont()->setBold(false)->setSize(8)->setColor(new Color('FF000000'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setMarginLeft(5)->setMarginTop(10);
        $cell->createBreak();
        $cell->createTextRun('SECRET CONFIDENTIAL INTERNAL ONLY')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(0)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FF000000'));
            $cell->getBorders()->getLeft()->setLineWidth(0)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(0)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        $oFill = new Fill();
        $oFill->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color(Color::COLOR_DARKRED));
        $shape = new Drawing\File();
        $shape->setName('PHPPresentation logo')
            ->setDescription('PHPPresentation logo')
            ->setPath(storage_path('app/avatars/absa-logo.png'))
            ->setHeight(30)
            ->setOffsetX(900)
            ->setOffsetY(670)
            ->setFill($oFill);
        $currentSlide->addShape($shape);

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(600)
            ->setOffsetX(10)
            ->setOffsetY(50);
        $shape->getActiveParagraph()->getAlignment()->setMarginLeft(0)->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $textRun = $shape->createTextRun('${client.first_name} ${client.last_name}');
        $textRun->getFont()->setBold(true)
            ->setSize(12)
            ->setName('Arial')
            ->setColor(new Color('FF000000'));

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(400)
            ->setOffsetX(600)
            ->setOffsetY(50);
        $shape->getActiveParagraph()->getAlignment()->setMarginLeft(0)->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $textRun = $shape->createTextRun('${activity.management_executive}');
        $textRun->getFont()->setBold(true)
            ->setSize(12)
            ->setName('Arial')
            ->setColor(new Color('FF000000'));

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(100)
            ->setOffsetX(840)
            ->setOffsetY(50);
        $shape->getActiveParagraph()->getAlignment()->setMarginLeft(0)->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $textRun = $shape->createTextRun('2/' . $total);
        $textRun->getFont()->setBold(true)
            ->setSize(12)
            ->setName('Arial')
            ->setColor(new Color('FF000000'));

        // Create a shape (drawing)
        $shape = $currentSlide->createTableShape(11);
        $shape->setWidth(800);
        $shape->setOffsetX(20);
        $shape->setOffsetY(80);

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell->setWidth(30);
        $cell->createTextRun(' ')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        $cell->setWidth(180);
        $cell->createTextRun(' ')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        $cell->setWidth(90);
        $cell->createTextRun(' ')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        $cell->setWidth(90);
        $cell->createTextRun(' ')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        $cell->setWidth(110);
        $cell->createTextRun('Overview')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        $cell->setWidth(50);
        $cell->createTextRun(' ')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        $cell->setColSpan(5);
        $cell->createTextRun(' ')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        foreach ($row->getCells() as $cell) {
            $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        }

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFb30d50'))
            ->setEndColor(new Color('FFb30d50'));

        $oCell = $row->nextCell();
        $oCell->setWidth(20);
        $oCell->createTextRun('No.')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell = $row->nextCell();
        $oCell->createTextRun('Customer Name')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell = $row->nextCell();
        $oCell->createTextRun('Relationship')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell = $row->nextCell();
        $oCell->createTextRun('KYC Status')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell = $row->nextCell();
        $oCell->createTextRun('New / Existing')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell = $row->nextCell();
        $oCell->createTextRun('CASA')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell = $row->nextCell();
        $oCell->createTextRun('PEP')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell = $row->nextCell();
        $oCell->createTextRun('Sanctions')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell = $row->nextCell();
        $oCell->createTextRun('# STRS')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell = $row->nextCell();
        $oCell->createTextRun('Litigation')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $oCell = $row->nextCell();
        $oCell->createTextRun('Adv. Media')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }


        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));

        $oCellcol1 = $row2->nextCell();
        $oCellcol1->setRowSpan(30);
        $oCellcol1->createTextRun('1.1')->getFont()->setBold(false)->setSize(9)->setColor(new Color('FF000000'));
        $oCellcol1->getActiveParagraph()->getAlignment()->setMarginLeft(5);

        $oCell = $row2->nextCell();
        $oCell->createTextRun('${client.first_name} ${client.last_name}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell->createBreak();
        $oCell->createTextRun('( ${client.cif_code} )')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell->createBreak();
        $oCell->createTextRun('${client.id_number}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${activity.relationship}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${activity.kyc_status}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell->createBreak();
        $oCell->createTextRun('${activity.kyc_last_verified}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${activity.new_/_existing}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell->createBreak();
        $oCell->createTextRun('${activity.existing_absa_client_since}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${activity.casa}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${activity.pep}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${activity.sanctions}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${activity.#_strs}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${activity.litigation}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${activity.adverse_media}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row = $shape->createRow();
        $row->setHeight(11);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell = $row->nextCell();
        $cell->setColSpan(10);
        $cell->createTextRun('Background')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));

        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(10);
        $oCell->createTextRun('${activity.background}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell = $row->nextCell();
        $cell->setColSpan(10);
        $cell->createTextRun('Product Exposure')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFb30d50'))
            ->setEndColor(new Color('FFb30d50'));

        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(2);
        $oCell->createTextRun('Product')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(2);
        $oCell->createTextRun('Turnover ( Current Year )')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(3);
        $oCell->createTextRun('Turnover ( Previous Year )')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->createTextRun('Date Opened')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('Balance')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('Limit')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        $prod = 0;
        foreach ($steps as $step) {

            if ($step->group > 0) {
                $group_count = $client->groupCompletedActivities(Step::find($step->id), $client->id);
                $group_count = (int)$group_count;
            }

            foreach ($step["activities"] as $activity) {
                if ($activity->grouping != null && $activity->grouping > 0 && strpos($activity->name, 'Product') !== false) {
                    $group_array = array();
                    //for($i = 0;$i <= $group_count;$i++){

                    if (!in_array($activity->grouping, $group_array, true) && isset($activity["actionable"]["data"][0]["data"])) {
                        array_push($group_array, $activity->grouping);

                        $row2 = $shape->createRow();
                        $row2->setHeight(10);
                        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                            ->setRotation(90)
                            ->setStartColor(new Color('FFede6e7'))
                            ->setEndColor(new Color('FFede6e7'));

                        $oCell = $row2->nextCell();
                        $oCell = $row2->nextCell();
                        $oCell->setColSpan(2);
                        $oCell->createTextRun('${activity.product#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                        $oCell = $row2->nextCell();
                        $oCell = $row2->nextCell();
                        $oCell->setColSpan(2);
                        $oCell->createTextRun('${activity.turn_over_(current_year)#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                        $oCell = $row2->nextCell();
                        $oCell = $row2->nextCell();
                        $oCell->setColSpan(3);
                        $oCell->createTextRun('${activity.turn_over_(previous_year)#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                        $oCell = $row2->nextCell();
                        $oCell = $row2->nextCell();
                        $oCell = $row2->nextCell();
                        $oCell->createTextRun('${activity.date_opened#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                        $oCell = $row2->nextCell();
                        $oCell->createTextRun('${activity.balance#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                        $oCell = $row2->nextCell();
                        $oCell->createTextRun('${activity.limit#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                        foreach ($row2->getCells() as $cell) {
                            $cell->getBorders()->getBottom()->setLineWidth(1)
                                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                            $cell->getBorders()->getTop()->setLineWidth(1)
                                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                            $cell->getBorders()->getLeft()->setLineWidth(1)
                                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                            $cell->getBorders()->getRight()->setLineWidth(1)
                                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                            $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        }
                        $prod++;
                    }
                    //}

                }
            }

        }


        if($prod == 0){
            $row2 = $shape->createRow();
            $row2->setHeight(10);
            $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setRotation(90)
                ->setStartColor(new Color('FFede6e7'))
                ->setEndColor(new Color('FFede6e7'));

            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->setColSpan(2);
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->setColSpan(2);
            $oCell->createTextRun('N//A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->setColSpan(3);
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            foreach ($row2->getCells() as $cell) {
                $cell->getBorders()->getBottom()->setLineWidth(1)
                    ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                $cell->getBorders()->getTop()->setLineWidth(1)
                    ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                $cell->getBorders()->getLeft()->setLineWidth(1)
                    ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                $cell->getBorders()->getRight()->setLineWidth(1)
                    ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell = $row->nextCell();
        $cell->setColSpan(10);
        $cell->createTextRun('Suspicious Transactional Report (STR)')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFb30d50'))
            ->setEndColor(new Color('FFb30d50'));

        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(2);
        $oCell->createTextRun('Date')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(4);
        $oCell->createTextRun('High Level Reason')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(4);
        $oCell->createTextRun('Investigation Outcome')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        }
        $str = 0;
        foreach($steps as $step) {

            if ($step->group > 0) {
                $group_count = $client->groupCompletedActivities(Step::find($step->id), $client->id);
                $group_count = (int)$group_count;
            }

            foreach ($step["activities"] as $activity) {
                if ($activity->grouping != null && $activity->grouping > 0 && strpos($activity->name, 'Investigation') !== false) {
                    $group_array = array();
                    for($i = 0;$i <= $group_count;$i++){

                        if(!in_array($activity->grouping,$group_array,true) && isset($activity["actionable"]["data"][0]["data"])) {
                            array_push($group_array,$activity->grouping);
                            $row2 = $shape->createRow();
                            $row2->setHeight(20);
                            $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                                ->setRotation(90)
                                ->setStartColor(new Color('FFede6e7'))
                                ->setEndColor(new Color('FFede6e7'));

                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            $oCell->setColSpan(2);
                            $oCell->createTextRun('${activity.date#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            $oCell->setColSpan(4);
                            $oCell->createTextRun('${activity.high_level_reason#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            $oCell->setColSpan(4);
                            $oCell->createTextRun('${activity.investigation_outcome#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            foreach ($row2->getCells() as $cell) {
                                $cell->getBorders()->getBottom()->setLineWidth(1)
                                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                                $cell->getBorders()->getTop()->setLineWidth(0)
                                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                                $cell->getBorders()->getLeft()->setLineWidth(0)
                                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                                $cell->getBorders()->getRight()->setLineWidth(0)
                                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            }
                            $str++;
                        }
                    }
                }
            }

        }
        if($str == 0){
            $row2 = $shape->createRow();
            $row2->setHeight(10);
            $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setRotation(90)
                ->setStartColor(new Color('FFede6e7'))
                ->setEndColor(new Color('FFede6e7'));

            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->setColSpan(2);
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->setColSpan(4);
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->setColSpan(4);
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            foreach ($row2->getCells() as $cell) {
                $cell->getBorders()->getBottom()->setLineWidth(1)
                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                $cell->getBorders()->getTop()->setLineWidth(0)
                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                $cell->getBorders()->getLeft()->setLineWidth(0)
                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                $cell->getBorders()->getRight()->setLineWidth(0)
                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }
        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell = $row->nextCell();
        $cell->setColSpan(10);
        $cell->createTextRun('Transactional Analysis (${activity.ta_review_period})')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));

        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $oCell->setColSpan(2);
        $oCell->createTextRun('Expected Account Activity')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(8);
        $oCell->createTextRun('${activity.expected_account_activity}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();

        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(10);
        $oCell->createTextRun('${activity.complete_field}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);

        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell = $row->nextCell();
        $cell->setColSpan(10);
        $cell->createTextRun('Adverse Media')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));

        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $oCell->setColSpan(2);
        $oCell->createTextRun('Date of EDD')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(3);
        $oCell->createTextRun('${activity.date_of_edd_report}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $oCell->setColSpan(2);
        $oCell->createTextRun('EDD Rating')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(3);
        $oCell->createTextRun('${activity.edd_rating}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();

        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(10);
        $oCell->createTextRun('${activity.additional_investigation_notes}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);

        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell = $row->nextCell();
        $cell->setColSpan(10);
        $cell->createTextRun('Conclusion')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));

        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $oCell->setColSpan(2);
        $oCell->createTextRun('Reputational Risk Considerations')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5)->setMarginRight(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(3);
        $oCell->createTextRun('${activity.reputational_risk_considerations}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $oCell->setColSpan(2);
        $oCell->createTextRun('Recommendation')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(3);
        $oCell->createTextRun('${activity.recommendation}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();

        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }
    }

    public function slide2(&$objPHPPowerPoint,$client_id,$process_id,$count,$related_party_id,$total,$reason,$title,$committee,$date){

        $totalcnt = $count+3;

        $client = Client::where('id',$client_id)->first();

        $related_party = RelatedParty::where('id',$related_party_id)->first();

        $steps = Step::with(['activities.actionable.data'=>function ($q) use ($related_party_id){
            $q->where('related_party_id',$related_party_id);
        }])->where('process_id',$process_id)->where('group',1)->get();
        //relatedparty'.$count.'
        // Create slide
        $currentSlide = $objPHPPowerPoint->createSlide();

        $oFill = new Fill();
        $oFill->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color(Color::COLOR_DARKRED));
        $shape = new Drawing\File();
        $shape->setName('PHPPresentation logo')
            ->setDescription('PHPPresentation logo')
            ->setPath(storage_path('app/avatars/Capture.png'))
            ->setHeight(720)
            ->setOffsetX(953)
            ->setOffsetY(0)
            ->setFill($oFill);
        $currentSlide->addShape($shape);

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(600)
            ->setOffsetX(10)
            ->setOffsetY(20);
        $shape->getActiveParagraph()->getAlignment()->setMarginLeft(0)->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $textRun = $shape->createTextRun($reason);
        $textRun->getFont()->setBold(false)
            ->setSize(16)
            ->setName('Arial')
            ->setColor( new Color( 'FFc00000' ) );

        // Create a shape (drawing)
        $shape = $currentSlide->createTableShape(1);
        $shape->setWidth(915);
        $shape->setOffsetX(20);
        $shape->setOffsetY(660);

        $row = $shape->createRow();
        $row->setHeight(10);
        $cell = $row->nextCell();
        $cnt = $count+3;
        $cell->createTextRun($cnt.' |  '.$title.'  |  '.$committee.'  |  '.$date)->getFont()->setBold(false)->setSize(8)->setColor(new Color('FF000000'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setMarginLeft(5)->setMarginTop(10);
        $cell->createBreak();
        $cell->createTextRun('SECRET CONFIDENTIAL INTERNAL ONLY')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(0)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FF000000'));
            $cell->getBorders()->getLeft()->setLineWidth(0)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(0)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_LEFT );
        }

        $oFill = new Fill();
        $oFill->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color(Color::COLOR_DARKRED));
        $shape = new Drawing\File();
        $shape->setName('PHPPresentation logo')
            ->setDescription('PHPPresentation logo')
            ->setPath(storage_path('app/avatars/absa-logo.png'))
            ->setHeight(30)
            ->setOffsetX(900)
            ->setOffsetY(670)
            ->setFill($oFill);
        $currentSlide->addShape($shape);

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(600)
            ->setOffsetX(10)
            ->setOffsetY(50);
        $shape->getActiveParagraph()->getAlignment()->setMarginLeft(0)->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $textRun = $shape->createTextRun('${client.first_name} ${client.last_name}');
        $textRun->getFont()->setBold(true)
            ->setSize(12)
            ->setName('Arial')
            ->setColor( new Color( 'FF000000' ) );

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(400)
            ->setOffsetX(600)
            ->setOffsetY(50);
        $shape->getActiveParagraph()->getAlignment()->setMarginLeft(0)->setHorizontal( Alignment::HORIZONTAL_LEFT );
        $textRun = $shape->createTextRun('${activity.management_executive}');
        $textRun->getFont()->setBold(true)
            ->setSize(12)
            ->setName('Arial')
            ->setColor( new Color( 'FF000000' ) );

        $shape = $currentSlide->createRichTextShape()
            ->setHeight(20)
            ->setWidth(100)
            ->setOffsetX(840)
            ->setOffsetY(50);
        $shape->getActiveParagraph()->getAlignment()->setMarginLeft(0)->setHorizontal( Alignment::HORIZONTAL_RIGHT );
        $textRun = $shape->createTextRun($totalcnt.'/'.$total);
        $textRun->getFont()->setBold(true)
            ->setSize(12)
            ->setName('Arial')
            ->setColor( new Color( 'FF000000' ) );

        // Create a shape (drawing)
        $shape = $currentSlide->createTableShape(11);
        $shape->setWidth(800);
        $shape->setOffsetX(20);
        $shape->setOffsetY(80);

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell->setWidth(30);
        $cell->createTextRun(' ')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        $cell->setWidth(180);
        $cell->createTextRun(' ')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        $cell->setWidth(90);
        $cell->createTextRun(' ')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        $cell->setWidth(90);
        $cell->createTextRun(' ')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        $cell->setWidth(110);
        $cell->createTextRun('Overview')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        $cell->setWidth(50);
        $cell->createTextRun(' ')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell = $row->nextCell();
        $cell->setColSpan(5);
        $cell->createTextRun(' ')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getBorders()->getBottom()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getTop()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getLeft()->setLineWidth(0)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        $cell->getBorders()->getRight()->setLineWidth(1)
            ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        foreach ($row->getCells() as $cell) {
            $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_RIGHT );
        }

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFb30d50'))
            ->setEndColor(new Color('FFb30d50'));

        $oCell = $row->nextCell();
        $oCell->setWidth(20);
        $oCell->createTextRun('No.')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $oCell = $row->nextCell();
        $oCell->createTextRun('Customer Name')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $oCell = $row->nextCell();
        $oCell->createTextRun('Relationship')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $oCell = $row->nextCell();
        $oCell->createTextRun('KYC Status')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $oCell = $row->nextCell();
        $oCell->createTextRun('New / Existing')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $oCell = $row->nextCell();
        $oCell->createTextRun('CASA')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $oCell = $row->nextCell();
        $oCell->createTextRun('PEP')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $oCell = $row->nextCell();
        $oCell->createTextRun('Sanctions')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $oCell = $row->nextCell();
        $oCell->createTextRun('# STRS')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $oCell = $row->nextCell();
        $oCell->createTextRun('Litigation')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        $oCell = $row->nextCell();
        $oCell->createTextRun('Adv. Media')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }


        $row2 = $shape->createRow();
        $row2->setHeight(20);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));

        $oCellcol1 = $row2->nextCell();
        $oCellcol1->setRowSpan(30);
        $cnt = $count+2;
        $oCellcol1->createTextRun('1.'.$cnt)->getFont()->setBold(false)->setSize(9)->setColor(new Color('FF000000'))->setName('Arial');
        $oCellcol1->getActiveParagraph()->getAlignment()->setMarginLeft(5);

        $oCell = $row2->nextCell();
        $oCell->createTextRun('${relatedparty'.$count.'.first_name} ${relatedparty'.$count.'.last_name}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell->createBreak();
        $oCell->createTextRun('( ${relatedparty'.$count.'.cif_code} )')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell->createBreak();
        $oCell->createTextRun('${relatedparty'.$count.'.id_number}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${relatedparty'.$count.'.activity.relationship}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${relatedparty'.$count.'.activity.kyc_status}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell->createBreak();
        $oCell->createTextRun('${relatedparty'.$count.'.activity.kyc_last_verified}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${relatedparty'.$count.'.activity.new_/_existing}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell->createBreak();
        $oCell->createTextRun('${relatedparty'.$count.'.activity.existing_absa_client_since}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${relatedparty'.$count.'.activity.casa}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${relatedparty'.$count.'.activity.pep}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${relatedparty'.$count.'.activity.sanctions}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${relatedparty'.$count.'.activity.#_strs}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${relatedparty'.$count.'.activity.litigation}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('${relatedparty'.$count.'.activity.adverse_media}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row = $shape->createRow();
        $row->setHeight(11);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell = $row->nextCell();
        $cell->setColSpan(10);
        $cell->createTextRun('Background')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        }

        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));

        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(10);
        $oCell->createTextRun('${relatedparty'.$count.'.activity.background}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell = $row->nextCell();
        $cell->setColSpan(10);
        $cell->createTextRun('Product Exposure')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFb30d50'))
            ->setEndColor(new Color('FFb30d50'));

        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(2);
        $oCell->createTextRun('Product')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(2);
        $oCell->createTextRun('Turnover ( Current Year )')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(3);
        $oCell->createTextRun('Turnover ( Previous Year )')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->createTextRun('Date Opened')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('Balance')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell->createTextRun('Limit')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        }
        $prod = 0;
        foreach($steps as $step) {

            if ($step->group > 0) {
                $group_count = $client->groupRelatedPartyCompletedActivities(Step::find($step->id), $client->id);
                $group_count = (int)$group_count;
            }

            foreach ($step["activities"] as $activity) {
                if ($activity->grouping != null && $activity->grouping > 0 && strpos($activity->name, 'Product') !== false) {
                    $group_array = array();
                    //for($i = 0;$i <= $group_count;$i++){

                    if(!in_array($activity->grouping,$group_array,true) && isset($activity["actionable"]["data"][0]["data"])) {
                        array_push($group_array,$activity->grouping);

                        $row2 = $shape->createRow();
                        $row2->setHeight(10);
                        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                            ->setRotation(90)
                            ->setStartColor(new Color('FFede6e7'))
                            ->setEndColor(new Color('FFede6e7'));

                        $oCell = $row2->nextCell();
                        $oCell = $row2->nextCell();
                        $oCell->setColSpan(2);
                        $oCell->createTextRun('${relatedparty'.$count.'.activity.product#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                        $oCell = $row2->nextCell();
                        $oCell = $row2->nextCell();
                        $oCell->setColSpan(2);
                        $oCell->createTextRun('${relatedparty'.$count.'.activity.turn_over_(current_year)#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                        $oCell = $row2->nextCell();
                        $oCell = $row2->nextCell();
                        $oCell->setColSpan(3);
                        $oCell->createTextRun('${relatedparty'.$count.'.activity.turn_over_(previous_year)#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                        $oCell = $row2->nextCell();
                        $oCell = $row2->nextCell();
                        $oCell = $row2->nextCell();
                        $oCell->createTextRun('${relatedparty'.$count.'.activity.date_opened#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                        $oCell = $row2->nextCell();
                        $oCell->createTextRun('${relatedparty'.$count.'.activity.balance#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                        $oCell = $row2->nextCell();
                        $oCell->createTextRun('${relatedparty'.$count.'.activity.limit#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                        foreach ($row2->getCells() as $cell) {
                            $cell->getBorders()->getBottom()->setLineWidth(1)
                                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                            $cell->getBorders()->getTop()->setLineWidth(1)
                                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                            $cell->getBorders()->getLeft()->setLineWidth(1)
                                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                            $cell->getBorders()->getRight()->setLineWidth(1)
                                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                            $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        }
                        $prod++;
                    }
                    //}
                }
            }

        }
        if($prod == 0){
            $row2 = $shape->createRow();
            $row2->setHeight(10);
            $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setRotation(90)
                ->setStartColor(new Color('FFede6e7'))
                ->setEndColor(new Color('FFede6e7'));

            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->setColSpan(2);
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->setColSpan(2);
            $oCell->createTextRun('N//A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->setColSpan(3);
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            foreach ($row2->getCells() as $cell) {
                $cell->getBorders()->getBottom()->setLineWidth(1)
                    ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                $cell->getBorders()->getTop()->setLineWidth(1)
                    ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                $cell->getBorders()->getLeft()->setLineWidth(1)
                    ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                $cell->getBorders()->getRight()->setLineWidth(1)
                    ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell = $row->nextCell();
        $cell->setColSpan(10);
        $cell->createTextRun('Suspicious Transactional Report (STR)')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFb30d50'))
            ->setEndColor(new Color('FFb30d50'));

        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(2);
        $oCell->createTextRun('Date')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(4);
        $oCell->createTextRun('High Level Reason')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(4);
        $oCell->createTextRun('Investigation Outcome')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        }
        $str = 0;
        foreach($steps as $step) {

            if ($step->group > 0) {
                $group_count = $client->groupRelatedPartyCompletedActivities(Step::find($step->id), $client->id);
                $group_count = (int)$group_count;
            }


            foreach ($step["activities"] as $activity) {
                if ($activity->grouping != null && $activity->grouping > 0 && strpos($activity->name, 'Investigation') !== false) {
                    $group_array = array();

                    for($i = 0;$i <= $group_count;$i++){

                        if(!in_array($activity->grouping,$group_array,true) && isset($activity["actionable"]["data"][0]["data"])) {
                            array_push($group_array,$activity->grouping);
                            $row2 = $shape->createRow();
                            $row2->setHeight(10);
                            $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                                ->setRotation(90)
                                ->setStartColor(new Color('FFede6e7'))
                                ->setEndColor(new Color('FFede6e7'));

                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            $oCell->setColSpan(2);
                            $oCell->createTextRun('${relatedparty'.$count.'.activity.date#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            $oCell->setColSpan(4);
                            $oCell->createTextRun('${relatedparty'.$count.'.activity.high_level_reason#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            $oCell->setColSpan(4);
                            $oCell->createTextRun('${relatedparty'.$count.'.activity.investigation_outcome#' . $activity->grouping . '}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
                            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            $oCell = $row2->nextCell();
                            foreach ($row2->getCells() as $cell) {
                                $cell->getBorders()->getBottom()->setLineWidth(1)
                                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                                $cell->getBorders()->getTop()->setLineWidth(0)
                                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                                $cell->getBorders()->getLeft()->setLineWidth(0)
                                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                                $cell->getBorders()->getRight()->setLineWidth(0)
                                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            }
                            $str++;
                        }
                    }


                }
            }

        }

        if($str == 0){
            $row2 = $shape->createRow();
            $row2->setHeight(10);
            $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
                ->setRotation(90)
                ->setStartColor(new Color('FFede6e7'))
                ->setEndColor(new Color('FFede6e7'));

            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->setColSpan(2);
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->setColSpan(4);
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell->setColSpan(4);
            $oCell->createTextRun('N/A')->getFont()->setBold(false)->setSize(9)->setName('Arial');
            $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            $oCell = $row2->nextCell();
            foreach ($row2->getCells() as $cell) {
                $cell->getBorders()->getBottom()->setLineWidth(1)
                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                $cell->getBorders()->getTop()->setLineWidth(0)
                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                $cell->getBorders()->getLeft()->setLineWidth(0)
                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                $cell->getBorders()->getRight()->setLineWidth(0)
                    ->setLineStyle(Border::LINE_SINGLE)->getColor()->setARGB('FFFFFFFF');
                $cell->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        }

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell = $row->nextCell();
        $cell->setColSpan(10);
        $cell->createTextRun('Transactional Analysis (${relatedparty'.$count.'.activity.ta_review_period})')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(10);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));

        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $oCell->setColSpan(2);
        $oCell->createTextRun('Expected Account Activity')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(8);
        $oCell->createTextRun('${relatedparty'.$count.'.activity.expected_account_activity}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();

        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(20);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(10);
        $oCell->createTextRun('${relatedparty'.$count.'.activity.complete_field}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);

        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell = $row->nextCell();
        $cell->setColSpan(10);
        $cell->createTextRun('Adverse Media')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(20);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));

        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $oCell->setColSpan(2);
        $oCell->createTextRun('Date of EDD')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(3);
        $oCell->createTextRun('${relatedparty'.$count.'.activity.date_of_edd_report}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $oCell->setColSpan(2);
        $oCell->createTextRun('EDD Rating')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(3);
        $oCell->createTextRun('${relatedparty'.$count.'.activity.edd_rating}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();

        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(20);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(10);
        $oCell->createTextRun('${relatedparty'.$count.'.activity.additional_investigation_notes}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);

        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row = $shape->createRow();
        $row->setHeight(10);
        $row->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $cell = $row->nextCell();
        $cell = $row->nextCell();
        $cell->setColSpan(10);
        $cell->createTextRun('Conclusion')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $cell->getActiveParagraph()->getAlignment()->setHorizontal( Alignment::HORIZONTAL_CENTER );
        foreach ($row->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }

        $row2 = $shape->createRow();
        $row2->setHeight(20);
        $row2->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FFede6e7'))
            ->setEndColor(new Color('FFede6e7'));

        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $oCell->setColSpan(2);
        $oCell->createTextRun('Reputational Risk Considerations')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(3);
        $oCell->createTextRun('${relatedparty'.$count.'.activity.reputational_risk_considerations}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5)->setMarginRight(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->getFill()->setFillType(Fill::FILL_GRADIENT_LINEAR)
            ->setRotation(90)
            ->setStartColor(new Color('FF870a3c'))
            ->setEndColor(new Color('FF870a3c'));
        $oCell->setColSpan(2);
        $oCell->createTextRun('Recommendation')->getFont()->setBold(true)->setSize(9)->setColor(new Color('FFFFFF'))->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();
        $oCell->setColSpan(3);
        $oCell->createTextRun('${relatedparty'.$count.'.activity.recommendation}')->getFont()->setBold(false)->setSize(9)->setName('Arial');
        $oCell->getActiveParagraph()->getAlignment()->setMarginLeft(5);
        $oCell = $row2->nextCell();
        $oCell = $row2->nextCell();

        foreach ($row2->getCells() as $cell) {
            $cell->getBorders()->getBottom()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getTop()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getLeft()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
            $cell->getBorders()->getRight()->setLineWidth(1)
                ->setLineStyle(Border::LINE_SINGLE)->setColor(new Color('FFFFFFFF'));
        }
    }


    public function powerpointExport($client_id,$process_id,$template_id,$reason,$title,$committee,$date)
    {
        // Grab the client
        $client = Client::with('related_parties')->where('id',$client_id);

        // Client details in an array
        $client = $client->first()->toArray();

        //dd($client);

        // What will eventually be sent to the report
        $output = [];
        $processData = [];
        $rpprocessData = [];

        // We have a client


        // We have a client
        if($client) {
            // loop over steps to get the activity names, storing them in an assoc. array
            $steps = Step::orderBy('id')->get();

            foreach($steps as $step) {

                $activities = Activity::with(['actionable.data'=>function($query) use ($client){
                    $query->where('client_id',$client["id"])->orderBy('created_at','desc');
                }])->get();

                foreach($activities as $activity) {

                    if (strpos($activity['actionable_type'], 'Actionable') !== false) {
                        $completed_activity_clients_data = null;
                        switch ($activity['actionable_type']) {
                            case 'App\ActionableBoolean':
                                $filter_data = ['' => 'All', 0 => 'No', 1 => 'Yes', 2 => 'Not Completed'];
                                $completed_activity_clients_data = ActionableBooleanData::where('actionable_boolean_id', $activity['actionable_id'])
                                    ->select('client_id', 'data')
                                    ->distinct()
                                    ->pluck('data', 'client_id');
                                break;
                            case 'App\ActionableDate':
                                $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                $completed_activity_clients_data = ActionableDateData::where('actionable_date_id', $activity['actionable_id'])
                                    ->select('client_id', 'data')
                                    ->distinct()
                                    ->pluck('data', 'client_id');
                                break;
                            case 'App\ActionableText':
                                $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                $completed_activity_clients_data = ActionableTextData::where('actionable_text_id', $activity['actionable_id'])
                                    ->select('client_id', 'data')
                                    ->distinct()
                                    ->pluck('data', 'client_id');
                                break;
                            case 'App\ActionableTextarea':
                                $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                $completed_activity_clients_data = ActionableTextareaData::where('actionable_textarea_id', $activity['actionable_id'])
                                    ->select('client_id', 'data')
                                    ->distinct()
                                    ->pluck('data', 'client_id');
                                break;
                            case 'App\ActionableDropdown':
                                $filter_data = ActionableDropdownItem::where('actionable_dropdown_id', $activity['actionable_id'])
                                    ->select('name', 'id')
                                    ->distinct()
                                    ->pluck('name', 'id')
                                    ->prepend('All', 0);

                                /*if ($request->has('activity') && $request->input('activity') != '') {*/
                                $completed_activity_clients_data = ActionableDropdownData::where('actionable_dropdown_id', $activity['actionable_id'])

                                    ->select('client_id', 'actionable_dropdown_item_id')
                                    ->distinct()
                                    //->get()->toArray();
                                    ->pluck('actionable_dropdown_item_id', 'client_id');
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
                            case 'App\ActionableDocument':
                                $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                $completed_activity_clients_data = ActionableDocumentData::where('actionable_document_id', $activity['actionable_id'])
                                    ->select('client_id', 'actionable_document_id')
                                    ->distinct()
                                    ->pluck('actionable_document_id', 'client_id');
                                break;
                            case 'App\ActionableTemplateEmail':
                                $filter_data = Template::orderBy('name')
                                    ->select('name', 'id')
                                    ->distinct()
                                    ->pluck('name', 'id')
                                    ->prepend('All', 0);
                                $completed_activity_clients_data = ActionableTemplateEmailData::where('actionable_template_email_id', $activity['actionable_id'])
                                    ->select('client_id', 'template_id')
                                    ->distinct()
                                    ->pluck('template_id', 'client_id');
                                break;
                            case 'App\ActionableNotification':
                                $filter_data = ['' => 'All', 1 => 'Sent', 0 => 'Not Sent'];
                                $completed_activity_clients_data = ActionableNotificationData::where('actionable_notification_id', $activity['actionable_id'])
                                    ->select('client_id', 'actionable_notification_id')
                                    ->distinct()
                                    ->pluck('actionable_notification_id', 'client_id');
                                break;
                            case 'App\ActionableMultipleAttachment':
                                $filter_data = Template::orderBy('name')
                                    ->select('name', 'id')
                                    ->distinct()
                                    ->pluck('name', 'id')
                                    ->prepend('All', 0);
                                $completed_activity_clients_data = ActionableMultipleAttachmentData::where('actionable_ma_id', $activity['actionable_id'])
                                    ->select('client_id', 'template_id')
                                    ->distinct()
                                    ->pluck('template_id', 'client_id');
                                break;
                            default:
                                //todo capture defaults
                                break;
                        }

                        $data_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client['id']] : ' ';
                        $completed_value = '';
                        $selected_drop_down_names = '';

                        $data = '';
                        $yn_value = '';
                        switch ($activity['actionable_type']) {
                            case 'App\ActionableBoolean':
                                //$completed_value = isset($completed_activity_clients_data[$client['id']]) ? 'Yes' : 'No';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 2;
                                if (isset($completed_activity_clients_data[$client['id']]) && $completed_activity_clients_data[$client['id']] == '1') {
                                    $completed_value = "Yes";
                                } elseif (isset($completed_activity_clients_data[$client['id']]) && $completed_activity_clients_data[$client['id']] == '0') {
                                    $completed_value = "No";
                                } else {
                                    $completed_value = "";
                                }
                                break;
                            case 'App\ActionableDate':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client['id']] : ' ';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                break;
                            case 'App\ActionableText':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client["id"]] : ' ';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                break;
                            case 'App\ActionableTextarea':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client["id"]] : ' ';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                break;
                            case 'App\ActionableDropdown':
                                $data = ActionableDropdownData::with('item')->where('client_id', $client['id'])->where('actionable_dropdown_id', $activity['actionable_id'])->first();
                                //dd($data->item->name);
                                $activity_data_value = isset($completed_activity_clients_data[$client['id']]) ? (isset($data->item->name) && $data->item->name != null ? $data->item->name : '') : ' ';
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) ? (isset($data->item->name) && $data->item->name != null ? $data->item->name : '') : ' ';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 0;
                                break;
                            case 'App\ActionableDocument':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 'Completed' : 'Not Completed';

                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 0;
                                break;
                            case 'App\ActionableTemplateEmail':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 'Completed' : 'Not Completed';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 0;
                                break;
                            case 'App\ActionableNotification':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 'Completed' : 'Not Completed';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                break;
                            case 'App\ActionableMultipleAttachment':
                                $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 'Completed' : 'Not Completed';
                                $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 0;
                                break;
                            default:
                                //todo capture defaults
                                break;

                        }


                        if($activity['grouping'] != null && $activity['grouping'] > 0){
                            $processData[1] [] = ['name' => strtolower(str_replace(' ', '_', $activity['name'].'#'.$activity['grouping'])),
                                'data' => strip_tags($completed_value)];
                        } else {
                            $processData[1] [] = ['name' => strtolower(str_replace(' ', '_', $activity['name'])),
                                'data' => strip_tags($completed_value)];
                        }

                    }
                }
            }

            //Has related party
            if(count($client['related_parties']) > 0){
                for($i = 0;$i<count($client['related_parties']);$i++){
                    //dd($client['related_parties'][$i]['description']);

                    // loop over steps to get the activity names, storing them in an assoc. array
                    $related_party = $client['related_parties'][$i];
                    $related_party_process = $client['related_parties'][$i]['process_id'];
                    $related_party_id = $client['related_parties'][$i]['id'];
                    $steps = Step::orderBy('id')->get();

                    //dd($steps);
                    $rpprocessData[$i][1] [] = ['info'=>$related_party];
                    foreach($steps as $step) {
                        $activities = Activity::where('step_id',$step['id'])->get();

                        foreach($activities as $activity) {
                            if (strpos($activity['actionable_type'], 'RelatedParty') !== false) {
                                $completed_activity_clients_data = null;
                                switch ($activity['actionable_type']) {
                                    case 'App\RelatedPartyBoolean':
                                        $filter_data = ['' => 'All', 0 => 'No', 1 => 'Yes', 2 => 'Not Completed'];
                                        $completed_activity_clients_data = RelatedPartyBooleanData::where('related_party_boolean_id', $activity['actionable_id'])->where('related_party_id',$related_party_id)
                                            ->select('client_id', 'data')
                                            ->distinct()
                                            ->pluck('data', 'client_id');
                                        break;
                                    case 'App\RelatedPartyDate':
                                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                        $completed_activity_clients_data = RelatedPartyDateData::where('related_party_date_id', $activity['actionable_id'])->where('related_party_id',$related_party_id)
                                            ->select('client_id', 'data')
                                            ->orderBy('id','desc')
                                            ->pluck('data', 'client_id');
                                        break;
                                    case 'App\RelatedPartyText':
                                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                        $completed_activity_clients_data = RelatedPartyTextData::where('related_party_text_id', $activity['actionable_id'])->where('related_party_id',$related_party_id)
                                            ->select('client_id', 'data')
                                            ->distinct()
                                            ->pluck('data', 'client_id');
                                        break;
                                    case 'App\RelatedPartyTextarea':
                                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                        $completed_activity_clients_data = RelatedPartyTextareaData::where('related_party_textarea_id', $activity['actionable_id'])->where('related_party_id',$related_party_id)
                                            ->select('client_id', 'data')
                                            ->distinct()
                                            ->pluck('data', 'client_id');
                                        break;
                                    case 'App\RelatedPartyDropdown':
                                        $filter_data = RelatedPartyDropdownItem::where('related_party_dropdown_id', $activity['actionable_id'])
                                            ->select('name', 'id')
                                            ->distinct()
                                            ->pluck('name', 'id')
                                            ->prepend('All', 0);

                                        $completed_activity_clients_data = RelatedPartyDropdownData::where('related_party_dropdown_id', $activity['actionable_id'])->where('related_party_id',$related_party_id)
                                            ->select('client_id', 'related_party_dropdown_item_id')
                                            ->distinct()
                                            ->pluck('related_party_dropdown_item_id', 'client_id');
                                        break;
                                    case 'App\RelatedPartyDocument':
                                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                                        $completed_activity_clients_data = RelatedPartyDocumentData::where('related_party_document_id', $activity['actionable_id'])->where('related_party_id',$related_party_id)
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
                                        $completed_activity_clients_data = RelatedPartyTemplateEmailData::where('related_party_template_email_id', $activity['actionable_id'])->where('related_party_id',$related_party_id)
                                            ->select('client_id', 'template_id')
                                            ->distinct()
                                            ->pluck('template_id', 'client_id');
                                        break;
                                    case 'App\ActionableNotification':
                                        $filter_data = ['' => 'All', 1 => 'Sent', 0 => 'Not Sent'];
                                        $completed_activity_clients_data = RelatedPartyNotificationData::where('related_party_notification_id', $activity['actionable_id'])->where('related_party_id',$related_party_id)
                                            ->select('client_id', 'related_party_notification_id')
                                            ->distinct()
                                            ->pluck('related_party_notification_id', 'client_id');
                                        break;
                                    case 'App\RelatedPartyMultipleAttachment':
                                        $filter_data = Template::orderBy('name')
                                            ->select('name', 'id')
                                            ->distinct()
                                            ->pluck('name', 'id')
                                            ->prepend('All', 0);
                                        $completed_activity_clients_data = RelatedPartyMultipleAttachmentData::where('related_party_ma_id', $activity['actionable_id'])->where('related_party_id',$related_party_id)
                                            ->select('client_id', 'template_id')
                                            ->distinct()
                                            ->pluck('template_id', 'client_id');
                                        break;
                                    default:
                                        //todo capture defaults
                                        break;
                                }

                                $data_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client['id']] : ' ';
                                $completed_value = '';
                                $selected_drop_down_names = '';

                                $data = '';
                                $yn_value = '';
                                switch ($activity['actionable_type']) {
                                    case 'App\RelatedPartyBoolean':
                                        //$completed_value = isset($completed_activity_clients_data[$client['id']]) ? 'Yes' : 'No';
                                        $activity_value = isset($completed_activity_clients_data[$client['id']]) ? $completed_activity_clients_data[$client['id']] : 2;
                                        if (isset($completed_activity_clients_data[$client['id']]) && $completed_activity_clients_data[$client['id']] == '1') {
                                            $completed_value = "Yes";
                                        } elseif (isset($completed_activity_clients_data[$client['id']]) && $completed_activity_clients_data[$client['id']] == '0') {
                                            $completed_value = "No";
                                        } else {
                                            $completed_value = "";
                                        }
                                        break;
                                    case 'App\RelatedPartyDate':
                                        if(in_array($activity['name'],['KYC last verified','Existing ABSa client since'])) {
                                            $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? '('.$completed_activity_clients_data[$client['id']].')' : ' ';
                                        } else {
                                            $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client['id']] : ' ';
                                        }
                                        $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                        break;
                                    case 'App\RelatedPartyText':
                                        $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client["id"]] : ' ';
                                        $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                        break;
                                    case 'App\RelatedPartyTextarea':
                                        $completed_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? $completed_activity_clients_data[$client["id"]] : ' ';
                                        $activity_value = isset($completed_activity_clients_data[$client['id']]) && trim($completed_activity_clients_data[$client['id']]) != '' ? 1 : 0;
                                        break;
                                    case 'App\RelatedPartyDropdown':
                                        $data_value = '';
                                        $data = RelatedPartyDropdownData::with('item')->where('client_id', $client['id'])->where('related_party_dropdown_id', $activity['actionable_id'])->where('related_party_id',$related_party_id)->first();
                                        $activity_data_value = isset($completed_activity_clients_data[$client['id']]) ? (isset($data->item->name) && $data->item->name != null ? $data->item->name : ' ') : ' ';
                                        $completed_value = isset($completed_activity_clients_data[$client['id']]) ? (isset($data->item->name) && $data->item->name != null ? $data->item->name : ' ') : ' ';
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

                                if($activity['grouping'] != null && $activity['grouping'] > 0){
                                    array_push($rpprocessData[$i][1] ,['name' => strtolower(str_replace(' ', '_', $activity['name']).'#'.$activity['grouping']),'data' => strip_tags($completed_value)]);
                                } else {
                                    array_push($rpprocessData[$i][1], ['name' => strtolower(str_replace(' ', '_', $activity['name'])), 'data' => strip_tags($completed_value)]);
                                }


                            }
                        }
                    }
                }
            }


        }

        //dd($rpprocessData);
        $this->generatePPTX($client_id,$process_id,$reason,$title,$committee,$date);

        // Get the pptx template
        $template = Template::where('id', $template_id)->first();

        /*if(count($client['related_parties']) > 0){
            $file = explode(".", $template->file);

            $templatefile = $file[0].'_rp'.count($client['related_parties']).'.'.$file[1];
            //dd($templatefile);
        } else {*/
        $templatefile = "sample.pptx";
        //$templatefile = $template->file;
        //}

        $presentation = new Presentation($templatefile, ['client' => $client, 'activities' => $processData[1],'related_parties'=>$rpprocessData]);

        // do whatevs
        $presentation->run();
        $downloadFile = $presentation->getDownloadPath();

        $headers = array(
            'Content-Type: application/vnd.ms-powerpoint',
        );

        return \Response::download($downloadFile, 'report_'.date('Y_m_d_H_i_s').'.pptx', $headers);
    }

    public function assignedactivities(Request $request) {

        $offices = OfficeUser::select('office_id')->where('user_id',Auth::id())->get();
        $office_array = [];

        foreach ($offices as $office){
            array_push($office_array,$office->office_id);
        }

        /* Get the raw data for assigned activities */
        $result = ActionsAssigned::with('client')->whereHas('client', function ($q) use ($office_array) {
            $q->whereIn('office_id', $office_array);
        });

        if($request->input('assigned_user') && $request->input('assigned_user') != '') {
            $result = $result->whereHas('activity', function ($q) use ($request) {
                $q->where('user_id', $request->input('assigned_user'));
            });
        }

        if($request->input('activities_search') && $request->input('activities_search') != '') {
            $a = $request->input('activities_search');
            $result = $result->with(['activity' => function ($q) use ($a) {
                $q->where('activity_id', $a);
            }])->whereHas('activity', function ($q) use ($a) {
                $q->where('activity_id', $a);
            });
        }

        if($request->input('client_search') && $request->input('client_search') != 0) {
            $result = $result->whereHas('activity', function ($q) use ($request) {
                $q->where('client_id', $request->input('client_search'));
            });
        }

        if($request->input('f') && $request->input('f') != '') {
            $result = $result->where('due_date', '>=', $request->input('f'));
        }

        if($request->input('t') && $request->input('t') != '') {
            $result = $result->where('due_date', '<=', $request->input('t'));
        }

        $result = $result->whereHas('activity', function($q){
            $q->where('status','0');
        })->orderBy('due_date','desc')->get();

        $configs = Config::first();
//dd($result);
        /*  Separate out the collection into an array we can manipulate better in the template */
        $activities = [];
        $ud = [];

        foreach($result as $activity) {

            // User IDs are comma-separated in the database
            $split_users = explode(',', $activity->users);
            $auser_array = array();
            if ($activity->client) {
                // List of Activities
                foreach ($activity->activity as $activity_id) {
                    foreach ($split_users as $user_id) {

                        // User Name
                        $user = User::where('id', trim($user_id))->first();

                        $user_name = $user["first_name"] . ' ' . $user["last_name"];

                        $ud[$user["first_name"] . ' ' . $user["last_name"]][] = ['due_date'=>$activity->due_date];

                        if(!in_array($user_name,$auser_array)) {
                            array_push($auser_array, $user_name);
                        }
                    }

                    if ($activity_id != null && $activity_id->status != 1) {
                        $clientid = $activity->client["id"];

                        if(isset($activities[($activity->client["company"] != null && $activity->client["company"] != '' ? $activity->client["company"] : $activity->client["first_name"] . ' ' . $activity->client["last_name"])][$activity_id->activity_id]["due_date"]) && $activities[($activity->client["company"] != null && $activity->client["company"] != '' ? $activity->client["company"] : $activity->client["first_name"] . ' ' . $activity->client["last_name"])][$activity_id->activity_id]["due_date"] > $activity->due_date){
                            $due_date = $activities[($activity->client["company"] != null && $activity->client["company"] != '' ? $activity->client["company"] : $activity->client["first_name"] . ' ' . $activity->client["last_name"])][$activity_id->activity_id]["due_date"];
                        } else {
                            $due_date = $activity->due_date;
                        }

                        $client_name = ($activity->client["company"] != null && $activity->client["company"] != '' ? $activity->client["company"] : $activity->client["first_name"] . ' ' . $activity->client["last_name"]);

                        //Get the current timestamp.
                        $now = strtotime(now());

                        //Calculate the difference.
                        $difference = $now - strtotime($due_date);

                        //Convert seconds into days.
                        $days = floor($difference / (60 * 60 * 24));

                        if ($days < -$configs->action_threshold) {
                            $class = $activity->client->process->getStageHex(2);
                        } elseif ($days <= $configs->action_threshold) {
                            if (Carbon::parse(now()) > Carbon::parse($due_date)) {
                                $class = $activity->client->process->getStageHex(0);
                            } elseif (Carbon::parse(now()) >= Carbon::parse($due_date)->subDay($configs->action_threshold)) {
                                $class = $activity->client->process->getStageHex(1);
                            }
                        } elseif ($days > $configs->action_threshold) {
                            $class = $activity->client->process->getStageHex(0);
                        } else {
                            $class = $activity->client->process->getStageHex(0);
                        }

                        if (Auth::check() && Auth::user()->isNot("admin") && Auth::id() == $user_id) {
                            $activities[$client_name] [$activity_id->activity_id] = [
                                'client_id' => $activity->client["id"],
                                'client_name' => $client_name,
                                'process_id' => Step::where('id',$activity->step_id)->first()->process_id,
                                'step_id' => $activity->step_id,
                                'action_id' => $activity->id,
                                'user' => (isset($activities[($activity->client["company"] != null && $activity->client["company"] != '' ? $activity->client["company"] : $activity->client["first_name"] . ' ' . $activity->client["last_name"])][$activity_id->activity_id]["user"]) ? array_unique(array_merge($activities[($activity->client["company"] != null && $activity->client["company"] != '' ? $activity->client["company"] : $activity->client["first_name"] . ' ' . $activity->client["last_name"])][$activity_id->activity_id]["user"],$auser_array)) : $auser_array),
                                'activity_id' => trim($activity_id->activity_id),
                                'activity_name' => Activity::withTrashed()->where('id', trim($activity_id->activity_id))->first()->name,
                                'due_date' => $due_date,
                                'class' => $class];
                        } elseif (Auth::check() && Auth::user()->is("admin")) {
                            $activities[$client_name] [$activity_id->activity_id] = [
                                'client_id' => $activity->client["id"],
                                'client_name' => $client_name,
                                'process_id' => Step::where('id',$activity->step_id)->first()->process_id,
                                'step_id' => $activity->step_id,
                                'action_id' => $activity->id,
                                'user' => (isset($activities[($activity->client["company"] != null && $activity->client["company"] != '' ? $activity->client["company"] : $activity->client["first_name"] . ' ' . $activity->client["last_name"])][$activity_id->activity_id]["user"]) ? array_unique(array_merge($activities[($activity->client["company"] != null && $activity->client["company"] != '' ? $activity->client["company"] : $activity->client["first_name"] . ' ' . $activity->client["last_name"])][$activity_id->activity_id]["user"],$auser_array)) : $auser_array),
                                'activity_id' => trim($activity_id->activity_id),
                                'activity_name' => Activity::withTrashed()->where('id', trim($activity_id->activity_id))->first()->name,
                                'due_date' => $due_date,
                                'created_date' => '',
                                'updated_date' => '',
                                'class' => $class];
                        }
                    }

                }

            }

        }

        ksort($activities);

        //dd($activities);

        $client_array = [];

        $clients = Client::whereIn('office_id',$office_array)->get();

        $client_array[0] = 'All';

        foreach ($clients as $ca){
            $client_array[$ca->id] = ($ca->company != null && $ca->company != '' ? $ca->company : $ca->first_name.' '.$ca->last_name);
        }

        //sort($client_array);

        $parameters = [
            'activities_dropdown' => Activity::select('id','name')->distinct('name')->get(),
            'clients_dropdown' => $client_array,
            'assigned_users' => User::select('id', DB::raw("CONCAT(first_name,' ',last_name) as full_name"))->get(),
            'activities' => $activities
        ];

        return view('reports.assignedactivities')->with($parameters);
    }

    public function show(Activity $activity, Request $request)
    {

        $total = 0;
        $request->session()->forget('path_route');

        $clients = Client::with(['referrer', 'process.steps.activities.actionable.data', 'introducer', 'consultant', 'committee', 'trigger'])->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'),
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

        if ($request->has('committee') && $request->input('committee') != null) {
            $clients = $clients->filter(function ($client) use ($request) {
                return $client->committee_id == $request->input('committee');
            });
        }

        if ($request->has('trigger') && $request->input('trigger') != '') {
            $p = $request->input('trigger');
            $clients = $clients->filter(function ($client) use ($p) {
                return $client->trigger_type_id == $p;
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

        //Actitivy type report filter
        $completed_activity_clients_data = null;
        $filter_data = [];
        $tmp_filter_data = [];


        //loop clients
        $client_data = array();
        $activity_value = '';
        $completed_values = [];
        foreach ($clients as $client) {
            if ($client) {
                switch ($activity->actionable_type) {
                    case 'App\ActionableBoolean':
                        $filter_data = ['' => 'All', 0 => 'No', 1 => 'Yes', 2 => 'Not Completed'];
                        $completed_activity_clients_data = ActionableBooleanData::where('actionable_boolean_id', $activity->actionable_id)
                            ->select('client_id', 'data')
                            ->distinct()
                            ->pluck('data', 'client_id');
                        break;
                    case 'App\ActionableDate':
                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                        $completed_activity_clients_data = ActionableDateData::where('actionable_date_id', $activity->actionable_id)
                            ->select('client_id', 'data')
                            ->distinct()
                            ->pluck('data', 'client_id');
                        break;
                    case 'App\ActionableText':
                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                        $completed_activity_clients_data = ActionableTextData::where('actionable_text_id', $activity->actionable_id)
                            ->select('client_id', 'data')
                            ->distinct()
                            ->pluck('data', 'client_id');
                        break;
                    case 'App\ActionableDropdown':
                        $filter_data = ActionableDropdownItem::where('actionable_dropdown_id', $activity->actionable_id)
                            ->select('name', 'id')
                            ->distinct()
                            ->pluck('name', 'id')
                            ->prepend('All', 0);

                        if ($request->has('activity') && $request->input('activity') != '') {
                            $completed_activity_clients_data = ActionableDropdownData::where('actionable_dropdown_id', $activity->actionable_id)
                                ->where('actionable_dropdown_item_id', $request->input('activity'))
                                ->select('client_id', 'actionable_dropdown_item_id')
                                ->distinct()
                                //->get()->toArray();
                                ->pluck('actionable_dropdown_item_id', 'client_id');
                        } else {
                            $completed_activity_clients_data = ActionableDropdownData::where('actionable_dropdown_id', $activity->actionable_id)
                                ->select('client_id', 'actionable_dropdown_item_id')
                                ->distinct()
                                //->get()->toArray();
                                ->pluck('actionable_dropdown_item_id', 'client_id');
                        }

                        $tmp_filter_data2 = $filter_data->toArray();
                        foreach ($tmp_filter_data2 as $key => $value):
                            array_push($tmp_filter_data, $key);
                        endforeach;
                        break;
                    case 'App\ActionableDocument':
                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                        $completed_activity_clients_data = ActionableDocumentData::where('actionable_document_id', $activity->actionable_id)
                            ->select('client_id', 'actionable_document_id')
                            ->distinct()
                            ->pluck('actionable_document_id', 'client_id');
                        break;
                    case 'App\ActionableTemplateEmail':
                        $filter_data = Template::orderBy('name')
                            ->select('name', 'id')
                            ->distinct()
                            ->pluck('name', 'id')
                            ->prepend('All', 0);
                        $completed_activity_clients_data = ActionableTemplateEmailData::where('actionable_template_email_id', $activity->actionable_id)
                            ->select('client_id', 'template_id')
                            ->distinct()
                            ->pluck('template_id', 'client_id');
                        break;
                    case 'App\ActionableNotification':
                        $filter_data = ['' => 'All', 1 => 'Sent', 0 => 'Not Sent'];
                        $completed_activity_clients_data = ActionableNotificationData::where('actionable_notification_id', $activity->actionable_id)
                            ->select('client_id', 'actionable_notification_id')
                            ->distinct()
                            ->pluck('actionable_notification_id', 'client_id');
                        break;
                    case 'App\ActionableMultipleAttachment':
                        $filter_data = Template::orderBy('name')
                            ->select('name', 'id')
                            ->distinct()
                            ->pluck('name', 'id')
                            ->prepend('All', 0);
                        $completed_activity_clients_data = ActionableMultipleAttachmentData::where('actionable_ma_id', $activity->actionable_id)
                            ->select('client_id', 'template_id')
                            ->distinct()
                            ->pluck('template_id', 'client_id');
                        break;
                    default:
                        //todo capture defaults
                        break;
                }

                $data_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? $completed_activity_clients_data[$client->id] : '';
                $completed_value = '';
                $selected_drop_down_names = '';

                $data = '';
                $yn_value = '';
                switch ($activity->actionable_type) {
                    case 'App\ActionableBoolean':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) ? 'Yes' : 'No';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 2;
                        if (isset($completed_activity_clients_data[$client->id]) && $completed_activity_clients_data[$client->id] == '1') {
                            $yn_value = "Yes";
                        }
                        if (isset($completed_activity_clients_data[$client->id]) && $completed_activity_clients_data[$client->id] == '0') {
                            $yn_value = "No";
                        }
                        break;
                    case 'App\ActionableDate':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 1 : 0;
                        break;
                    case 'App\ActionableText':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 1 : 0;
                        break;
                    case 'App\ActionableDropdown':
                        $data_value = '';
                        if ($request->has('s') && $request->input('s') != '') {
                            $selected_drop_down_items = ActionableDropdownData::with('item')->where('actionable_dropdown_id', $activity->actionable_id)
                                ->where('client_id', $client->id)
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
                        }
                        $data = ActionableDropdownData::with('item')->where('client_id', $client->id)->where('actionable_dropdown_id', $activity->actionable_id)->first();
                        $activity_data_value = isset($completed_activity_clients_data[$client->id]) ? (isset($data->item->name) && $data->item->name != null ? $data->item->name : '') : '';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 0;
                        break;
                    case 'App\ActionableDocument':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Yes' : 'No';

                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 0;
                        break;
                    case 'App\ActionableTemplateEmail':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 0;
                        break;
                    case 'App\ActionableNotification':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 1 : 0;
                        break;
                    case 'App\ActionableMultipleAttachment':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 0;
                        break;
                    default:
                        //todo capture defaults
                        break;
                }

                //dd($completed_value);
                //add to array
                $client_data[$client->id] = [
                    'type' => 'P',
                    'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                    'case_nr' => $client->case_number,
                    'cif_code' => $client->cif_code,
                    'committee' => ($client->committee_id > 0 ? $client->committee->name : ''),
                    'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                    'activity_data' => (isset($activity_data_value) && $activity_data_value != null ? $activity_data_value : ''),
                    'id' => $client->id,
                    'instruction_date' => ($client->instruction_date != null ? $client->instruction_date : ''),
                    'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                    'activity_value' => $activity_value,
                    'data_value' => $data_value,
                    'completed_yn' => ($activity->actionable_type == "App\ActionableBoolean" ? $yn_value : $completed_value),
                    'selected_drop_down_names' => $selected_drop_down_names,
                    'introducer' => $client->introducer,
                    'avatar' => $client->avatar
                ];

                $total++;
            }
        }


        $deleted_rps = RelatedPartiesTree::select('related_party_id')->get();

        $related_parties = RelatedParty::with(['client', 'referrer', 'process.steps.activities.actionable.data', 'introducer', 'committee', 'trigger'])->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'),
            DB::raw('ROUND(DATEDIFF(completed_at, created_at), 0) as completed_days'),
            DB::raw('CAST(AES_DECRYPT(`hash_company`, "Qwfe345dgfdg") AS CHAR(50)) hash_company'),
            DB::raw('CAST(AES_DECRYPT(`hash_first_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_first_name'),
            DB::raw('CAST(AES_DECRYPT(`hash_last_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_last_name'),
            DB::raw('CAST(AES_DECRYPT(`hash_cif_code`, "Qwfe345dgfdg") AS CHAR(50)) hash_cif_code'),
            DB::raw('CAST(`case_number` AS CHAR(50)) AS case_number')
        )->whereIn('id', collect($deleted_rps)->toArray());

        if ($request->has('q') && $request->input('q') != '') {

            $related_parties = $related_parties->having('hash_company', 'like', "%" . $request->input('q') . "%")
                ->orHaving('hash_first_name', 'like', "%" . $request->input('q') . "%")
                ->orHaving('hash_last_name', 'like', "%" . $request->input('q') . "%")
                ->orHaving('hash_cif_code', 'like', "%" . $request->input('q') . "%")
                ->orHaving('case_number', 'like', "%" . $request->input('q') . "%");
        }

        $related_parties = $related_parties->get();

        if ($request->has('user') && $request->input('user') != null) {
            $related_parties = $related_parties->filter(function ($related_party) use ($request) {
                return $related_party->client->consultant_id == $request->input('user');
            });
        }

        if ($request->has('committee') && $request->input('committee') != null) {
            $related_parties = $related_parties->filter(function ($related_party) use ($request) {
                return $related_party->committee_id == $request->input('committee');
            });
        }

        if ($request->has('trigger') && $request->input('trigger') != '') {
            $p = $request->input('trigger');
            $related_parties = $related_parties->filter(function ($related_party) use ($p) {
                return $related_party->trigger_type_id == $p;
            });
        }

        if ($request->has('f') && $request->input('f') != '') {
            $p = $request->input('f');
            $related_parties = $related_parties->filter(function ($related_party) use ($p) {
                return $related_party->instruction_date >= $p;
            });
        }

        if ($request->has('t') && $request->input('t') != '') {
            $p = $request->input('t');
            $related_parties = $related_parties->filter(function ($related_party) use ($p) {
                return Carbon::parse($related_party->instruction_date)->format("Y-m-d") <= $p;
            });
        }


        foreach ($related_parties as $related_party) {
            $rp_data = [];
            if ($related_party){
                $rpl = ActivityRelatedPartyLink::where('primary_activity', $activity->id)->first();
            $rp_activity = Activity::where('id', $rpl->related_activity)->first();

            switch ($rp_activity["actionable_type"]) {
                case 'App\RelatedPartyBoolean':
                    $yn_value = '';

                    $data2 = RelatedPartyBooleanData::where('related_party_id', $related_party->id)->where('related_party_boolean_id', $rp_activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                    if (isset($data2["data"]) && $data2["data"] == '1') {
                        $yn_value = "Yes";
                    }
                    if (isset($data2["data"]) && $data2["data"] == '0') {
                        $yn_value = "No";
                    }
                    array_push($rp_data, $yn_value);
                    break;
                case 'App\RelatedPartyDate':
                    //dd($activity);
                    $data_value = '';

                    $data2 = RelatedPartyDateData::where('related_party_id', $related_party->id)->where('related_party_date_id', $rp_activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                    $data_value = $data2["data"];

                    array_push($rp_data, $data_value);
                    break;
                case 'App\RelatedPartyText':
                    $data_value = '';

                    $data2 = RelatedPartyTextData::where('related_party_id', $related_party->id)->where('related_party_text_id', $rp_activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                    $data_value = $data2["data"];

                    array_push($rp_data, $data_value);
                    break;
                case 'App\RelatedPartyTextarea':
                    $data_value = '';

                    $data2 = RelatedPartyTextareaData::where('related_party_id', $related_party->id)->where('related_party_textarea_id', $rp_activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                    $data_value = $data2["data"];

                    array_push($rp_data, $data_value);
                    break;
                case 'App\RelatedPartyDropdown':
                    $data_value = '';

                    $data2 = RelatedPartyDropdownData::with('item')->where('related_party_id', $related_party->id)->where('related_party_dropdown_id', $rp_activity->actionable_id)->get();

                    foreach ($data2 as $key => $value):
                        if (count($data2) > 1) {
                            $data_value .= $value["item"]["name"] . ', ';
                        } else {
                            $data_value .= $value["item"]["name"];
                        }
                    endforeach;
                    array_push($rp_data, $data_value);
                    break;
                case 'App\RelatedPartyDocument':
                    $yn_value = '';

                    $data2 = RelatedPartyDocumentData::where('related_party_id', $related_party->id)->where('related_party_document_id', $activity->actionable_id)->take(1)->first();

                    if (isset($data2["document_id"])) {
                        $yn_value = "Yes";
                    } else {
                        $yn_value = "No";
                    }
                    array_push($rp_data, $yn_value);
                    break;
                case 'App\RelatedPartyTemplateEmail':
                    $yn_value = '';

                    $data2 = RelatedPartyTemplateEmailData::where('related_party_id', $related_party->id)->where('related_party_template_email_id', $activity->actionable_id)->orderBy('created_at','desc')->take(1)->first();

                    if (isset($data2["data"]) && $data2["data"] == '1') {
                        $yn_value = "Yes";
                    }
                    if (isset($data2["data"]) && $data2["data"] == '0') {
                        $yn_value = "No";
                    }
                    array_push($rp_data, $yn_value);
                    break;
                case 'App\RelatedPartyNotification':
                    $yn_value = '';

                    $data2 = RelatedPartyNotificationData::where('related_party_id', $related_party->id)->where('related_party_notification_id', $activity->actionable_id)->orderBy('created_at','desc')->take(1)->first();

                    if (isset($data2["data"]) && $data2["data"] == '1') {
                        $yn_value = "Yes";
                    }
                    if (isset($data2["data"]) && $data2["data"] == '0') {
                        $yn_value = "No";
                    }
                    array_push($rp_data, $yn_value);
                    break;
                case 'App\RelatedPartyMultipleAttachment':
                    $yn_value = '';

                    $data2 = RelatedPartyMultipleAttachmentData::where('related_party_id', $related_party->id)->where('related_party_ma_id', $activity->actionable_id)->orderBy('created_at','desc')->take(1)->first();

                    if (isset($data2["data"]) && $data2["data"] == '1') {
                        $yn_value = "Yes";
                    }
                    if (isset($data2["data"]) && $data2["data"] == '0') {
                        $yn_value = "No";
                    }
                    array_push($rp_data, $yn_value);
                    break;
                default:
                    //todo capture defaults
                    break;
            }


            $client_data[$related_party->client_id]['rp'][$related_party->id] = [
                'type' => 'R',
                'company' => ($related_party->company != null ? $related_party->company : $related_party->first_name . ' ' . $related_party->last_name),
                'id' => $related_party->id,
                'client_id' => $related_party->client_id,
                'case_nr' => $related_party->case_number,
                'cif_code' => $related_party->cif_code,
                'committee' => isset($related_party->committee) ? $related_party->committee->name : null,
                'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                'instruction_date' => $related_party->instruction_date,
                'completed_at' => ($related_party->completed_at != null ? $related_party->completed_at : ''),
                'date_submitted_qa' => $related_party->qa_start_date,
                'assigned' => ($related_party->consultant_id != null ? 1 : 0),
                'consultant' => Client::with('consultant')->where('id', $related_party->client_id)->first(),
                'data' => $rp_data,
                'process' => $related_party->process_id,
                'step' => $related_party->step_id
            ];

            $total++;
        }
    }
        $parameters = [
            'clients' => $client_data,
            'filter_data' => $filter_data,
            'processes' => Process::orderBy('name')->pluck('name', 'id')->prepend('All processes', 'all'),
            'steps' => Step::orderBy('process_id')->orderBy('order')->pluck('name', 'id')->prepend('All steps', ''),
            'activity' => $activity,
            'committee' => Committee::orderBy('name')->pluck('name', 'id')->prepend('All committees', 'all'),
            'trigger' => TriggerType::orderBy('name')->pluck('name', 'id')->prepend('All trigger types', 'all'),
            'assigned_user' => Client::all()->keyBy('consultant_id')->map(function ($consultant){
                return isset($consultant->consultant)?$consultant->consultant->first_name.' '.$consultant->consultant->last_name:null;
            })->sort(),
            'total' => $total
        ];

        return view('reports.show')->with($parameters);
    }

    public function export(Activity $activity, Excel $excel, Request $request)
    {
        $total = 0;
        $request->session()->forget('path_route');

        $clients = Client::with(['referrer', 'process.steps.activities.actionable.data', 'introducer', 'consultant', 'committee', 'trigger'])->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'),
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

        if ($request->has('committee') && $request->input('committee') != null) {
            $clients = $clients->filter(function ($client) use ($request) {
                return $client->committee_id == $request->input('committee');
            });
        }

        if ($request->has('trigger') && $request->input('trigger') != '') {
            $p = $request->input('trigger');
            $clients = $clients->filter(function ($client) use ($p) {
                return $client->trigger_type_id == $p;
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

        //Actitivy type report filter
        $completed_activity_clients_data = null;
        $filter_data = [];
        $tmp_filter_data = [];


        //loop clients
        $client_data = array();
        $activity_value = '';
        $completed_values = [];
        foreach ($clients as $client) {
            if ($client) {
                switch ($activity->actionable_type) {
                    case 'App\ActionableBoolean':
                        $filter_data = ['' => 'All', 0 => 'No', 1 => 'Yes', 2 => 'Not Completed'];
                        $completed_activity_clients_data = ActionableBooleanData::where('actionable_boolean_id', $activity->actionable_id)
                            ->select('client_id', 'data')
                            ->distinct()
                            ->pluck('data', 'client_id');
                        break;
                    case 'App\ActionableDate':
                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                        $completed_activity_clients_data = ActionableDateData::where('actionable_date_id', $activity->actionable_id)
                            ->select('client_id', 'data')
                            ->distinct()
                            ->pluck('data', 'client_id');
                        break;
                    case 'App\ActionableText':
                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                        $completed_activity_clients_data = ActionableTextData::where('actionable_text_id', $activity->actionable_id)
                            ->select('client_id', 'data')
                            ->distinct()
                            ->pluck('data', 'client_id');
                        break;
                    case 'App\ActionableDropdown':
                        $filter_data = ActionableDropdownItem::where('actionable_dropdown_id', $activity->actionable_id)
                            ->select('name', 'id')
                            ->distinct()
                            ->pluck('name', 'id')
                            ->prepend('All', 0);

                        if ($request->has('activity') && $request->input('activity') != '') {
                            $completed_activity_clients_data = ActionableDropdownData::where('actionable_dropdown_id', $activity->actionable_id)
                                ->where('actionable_dropdown_item_id', $request->input('activity'))
                                ->select('client_id', 'actionable_dropdown_item_id')
                                ->distinct()
                                //->get()->toArray();
                                ->pluck('actionable_dropdown_item_id', 'client_id');
                        } else {
                            $completed_activity_clients_data = ActionableDropdownData::where('actionable_dropdown_id', $activity->actionable_id)
                                ->select('client_id', 'actionable_dropdown_item_id')
                                ->distinct()
                                //->get()->toArray();
                                ->pluck('actionable_dropdown_item_id', 'client_id');
                        }

                        $tmp_filter_data2 = $filter_data->toArray();
                        foreach ($tmp_filter_data2 as $key => $value):
                            array_push($tmp_filter_data, $key);
                        endforeach;
                        break;
                    case 'App\ActionableDocument':
                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                        $completed_activity_clients_data = ActionableDocumentData::where('actionable_document_id', $activity->actionable_id)
                            ->select('client_id', 'actionable_document_id')
                            ->distinct()
                            ->pluck('actionable_document_id', 'client_id');
                        break;
                    case 'App\ActionableTemplateEmail':
                        $filter_data = Template::orderBy('name')
                            ->select('name', 'id')
                            ->distinct()
                            ->pluck('name', 'id')
                            ->prepend('All', 0);
                        $completed_activity_clients_data = ActionableTemplateEmailData::where('actionable_template_email_id', $activity->actionable_id)
                            ->select('client_id', 'template_id')
                            ->distinct()
                            ->pluck('template_id', 'client_id');
                        break;
                    case 'App\ActionableNotification':
                        $filter_data = ['' => 'All', 1 => 'Sent', 0 => 'Not Sent'];
                        $completed_activity_clients_data = ActionableNotificationData::where('actionable_notification_id', $activity->actionable_id)
                            ->select('client_id', 'actionable_notification_id')
                            ->distinct()
                            ->pluck('actionable_notification_id', 'client_id');
                        break;
                    case 'App\ActionableMultipleAttachment':
                        $filter_data = Template::orderBy('name')
                            ->select('name', 'id')
                            ->distinct()
                            ->pluck('name', 'id')
                            ->prepend('All', 0);
                        $completed_activity_clients_data = ActionableMultipleAttachmentData::where('actionable_ma_id', $activity->actionable_id)
                            ->select('client_id', 'template_id')
                            ->distinct()
                            ->pluck('template_id', 'client_id');
                        break;
                    default:
                        //todo capture defaults
                        break;
                }

                $data_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? $completed_activity_clients_data[$client->id] : '';
                $completed_value = '';
                $selected_drop_down_names = '';

                $data = '';
                $yn_value = '';
                switch ($activity->actionable_type) {
                    case 'App\ActionableBoolean':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) ? 'Yes' : 'No';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 2;
                        if (isset($completed_activity_clients_data[$client->id]) && $completed_activity_clients_data[$client->id] == '1') {
                            $yn_value = "Yes";
                        }
                        if (isset($completed_activity_clients_data[$client->id]) && $completed_activity_clients_data[$client->id] == '0') {
                            $yn_value = "No";
                        }
                        break;
                    case 'App\ActionableDate':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 1 : 0;
                        break;
                    case 'App\ActionableText':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 1 : 0;
                        break;
                    case 'App\ActionableDropdown':
                        $data_value = '';
                        if ($request->has('s') && $request->input('s') != '') {
                            $selected_drop_down_items = ActionableDropdownData::with('item')->where('actionable_dropdown_id', $activity->actionable_id)
                                ->where('client_id', $client->id)
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
                        }
                        $data = ActionableDropdownData::with('item')->where('client_id', $client->id)->where('actionable_dropdown_id', $activity->actionable_id)->first();
                        $activity_data_value = isset($completed_activity_clients_data[$client->id]) ? (isset($data->item->name) && $data->item->name != null ? $data->item->name : '') : '';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 0;
                        break;
                    case 'App\ActionableDocument':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Yes' : 'No';

                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 0;
                        break;
                    case 'App\ActionableTemplateEmail':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 0;
                        break;
                    case 'App\ActionableNotification':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 1 : 0;
                        break;
                    case 'App\ActionableMultipleAttachment':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 0;
                        break;
                    default:
                        //todo capture defaults
                        break;
                }

                //dd($completed_value);
                //add to array
                $client_data[$client->id] = [
                    'type' => 'P',
                    'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                    'case_nr' => $client->case_number,
                    'cif_code' => $client->cif_code,
                    'committee' => ($client->committee_id > 0 ? $client->committee->name : ''),
                    'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                    'activity_data' => (isset($activity_data_value) && $activity_data_value != null ? $activity_data_value : ''),
                    'id' => $client->id,
                    'instruction_date' => ($client->instruction_date != null ? $client->instruction_date : ''),
                    'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                    'activity_value' => $activity_value,
                    'data_value' => $data_value,
                    'completed_yn' => ($activity->actionable_type == "App\ActionableBoolean" ? $yn_value : $completed_value),
                    'selected_drop_down_names' => $selected_drop_down_names,
                    'introducer' => ''
                ];

                $total++;
            }
        }


        $deleted_rps = RelatedPartiesTree::select('related_party_id')->get();

        $related_parties = RelatedParty::with(['client', 'referrer', 'process.steps.activities.actionable.data', 'introducer', 'committee', 'trigger'])->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'),
            DB::raw('ROUND(DATEDIFF(completed_at, created_at), 0) as completed_days'),
            DB::raw('CAST(AES_DECRYPT(`hash_company`, "Qwfe345dgfdg") AS CHAR(50)) hash_company'),
            DB::raw('CAST(AES_DECRYPT(`hash_first_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_first_name'),
            DB::raw('CAST(AES_DECRYPT(`hash_last_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_last_name'),
            DB::raw('CAST(AES_DECRYPT(`hash_cif_code`, "Qwfe345dgfdg") AS CHAR(50)) hash_cif_code'),
            DB::raw('CAST(`case_number` AS CHAR(50)) AS case_number')
        )->whereIn('id', collect($deleted_rps)->toArray());

        if ($request->has('q') && $request->input('q') != '') {

            $related_parties = $related_parties->having('hash_company', 'like', "%" . $request->input('q') . "%")
                ->orHaving('hash_first_name', 'like', "%" . $request->input('q') . "%")
                ->orHaving('hash_last_name', 'like', "%" . $request->input('q') . "%")
                ->orHaving('hash_cif_code', 'like', "%" . $request->input('q') . "%")
                ->orHaving('case_number', 'like', "%" . $request->input('q') . "%");
        }

        $related_parties = $related_parties->get();

        if ($request->has('user') && $request->input('user') != null) {
            $related_parties = $related_parties->filter(function ($related_party) use ($request) {
                return $related_party->client->consultant_id == $request->input('user');
            });
        }

        if ($request->has('committee') && $request->input('committee') != null) {
            $related_parties = $related_parties->filter(function ($related_party) use ($request) {
                return $related_party->committee_id == $request->input('committee');
            });
        }

        if ($request->has('trigger') && $request->input('trigger') != '') {
            $p = $request->input('trigger');
            $related_parties = $related_parties->filter(function ($related_party) use ($p) {
                return $related_party->trigger_type_id == $p;
            });
        }

        if ($request->has('f') && $request->input('f') != '') {
            $p = $request->input('f');
            $related_parties = $related_parties->filter(function ($related_party) use ($p) {
                return $related_party->instruction_date >= $p;
            });
        }

        if ($request->has('t') && $request->input('t') != '') {
            $p = $request->input('t');
            $related_parties = $related_parties->filter(function ($related_party) use ($p) {
                return Carbon::parse($related_party->instruction_date)->format("Y-m-d") <= $p;
            });
        }


        foreach ($related_parties as $related_party) {
            $rp_data = [];
            if ($related_party){
                $rpl = ActivityRelatedPartyLink::where('primary_activity', $activity->id)->first();
                $rp_activity = Activity::where('id', $rpl->related_activity)->first();

                switch ($rp_activity["actionable_type"]) {
                    case 'App\RelatedPartyBoolean':
                        $yn_value = '';

                        $data2 = RelatedPartyBooleanData::where('related_party_id', $related_party->id)->where('related_party_boolean_id', $rp_activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                        if (isset($data2["data"]) && $data2["data"] == '1') {
                            $yn_value = "Yes";
                        }
                        if (isset($data2["data"]) && $data2["data"] == '0') {
                            $yn_value = "No";
                        }
                        array_push($rp_data, $yn_value);
                        break;
                    case 'App\RelatedPartyDate':
                        //dd($activity);
                        $data_value = '';

                        $data2 = RelatedPartyDateData::where('related_party_id', $related_party->id)->where('related_party_date_id', $rp_activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                        $data_value = $data2["data"];

                        array_push($rp_data, $data_value);
                        break;
                    case 'App\RelatedPartyText':
                        $data_value = '';

                        $data2 = RelatedPartyTextData::where('related_party_id', $related_party->id)->where('related_party_text_id', $rp_activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                        $data_value = $data2["data"];

                        array_push($rp_data, $data_value);
                        break;
                    case 'App\RelatedPartyTextarea':
                        $data_value = '';

                        $data2 = RelatedPartyTextareaData::where('related_party_id', $related_party->id)->where('related_party_textarea_id', $rp_activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                        $data_value = $data2["data"];

                        array_push($rp_data, $data_value);
                        break;
                    case 'App\RelatedPartyDropdown':
                        $data_value = '';

                        $data2 = RelatedPartyDropdownData::with('item')->where('related_party_id', $related_party->id)->where('related_party_dropdown_id', $rp_activity->actionable_id)->get();

                        foreach ($data2 as $key => $value):
                            if (count($data2) > 1) {
                                $data_value .= $value["item"]["name"] . ', ';
                            } else {
                                $data_value .= $value["item"]["name"];
                            }
                        endforeach;
                        array_push($rp_data, $data_value);
                        break;
                    case 'App\RelatedPartyDocument':
                        $yn_value = '';

                        $data2 = RelatedPartyDocumentData::where('related_party_id', $related_party->id)->where('related_party_document_id', $activity->actionable_id)->take(1)->first();

                        if (isset($data2["document_id"])) {
                            $yn_value = "Yes";
                        } else {
                            $yn_value = "No";
                        }
                        array_push($rp_data, $yn_value);
                        break;
                    case 'App\RelatedPartyTemplateEmail':
                        $yn_value = '';

                        $data2 = RelatedPartyTemplateEmailData::where('related_party_id', $related_party->id)->where('related_party_template_email_id', $activity->actionable_id)->orderBy('created_at','desc')->take(1)->first();

                        if (isset($data2["data"]) && $data2["data"] == '1') {
                            $yn_value = "Yes";
                        }
                        if (isset($data2["data"]) && $data2["data"] == '0') {
                            $yn_value = "No";
                        }
                        array_push($rp_data, $yn_value);
                        break;
                    case 'App\RelatedPartyNotification':
                        $yn_value = '';

                        $data2 = RelatedPartyNotificationData::where('related_party_id', $related_party->id)->where('related_party_notification_id', $activity->actionable_id)->orderBy('created_at','desc')->take(1)->first();

                        if (isset($data2["data"]) && $data2["data"] == '1') {
                            $yn_value = "Yes";
                        }
                        if (isset($data2["data"]) && $data2["data"] == '0') {
                            $yn_value = "No";
                        }
                        array_push($rp_data, $yn_value);
                        break;
                    case 'App\RelatedPartyMultipleAttachment':
                        $yn_value = '';

                        $data2 = RelatedPartyMultipleAttachmentData::where('related_party_id', $related_party->id)->where('related_party_ma_id', $activity->actionable_id)->orderBy('created_at','desc')->take(1)->first();

                        if (isset($data2["data"]) && $data2["data"] == '1') {
                            $yn_value = "Yes";
                        }
                        if (isset($data2["data"]) && $data2["data"] == '0') {
                            $yn_value = "No";
                        }
                        array_push($rp_data, $yn_value);
                        break;
                    default:
                        //todo capture defaults
                        break;
                }


                $client_data[$related_party->client_id]['rp'][$related_party->id] = [
                    'type' => 'R',
                    'company' => ($related_party->company != null ? $related_party->company : $related_party->first_name . ' ' . $related_party->last_name),
                    'id' => $related_party->id,
                    'client_id' => $related_party->client_id,
                    'case_nr' => $related_party->case_number,
                    'cif_code' => $related_party->cif_code,
                    'committee' => isset($related_party->committee) ? $related_party->committee->name : null,
                    'trigger' => ($related_party->trigger_type_id > 0 ? $related_party->trigger->name : ''),
                    'instruction_date' => $related_party->instruction_date,
                    'completed_at' => ($related_party->completed_at != null ? $related_party->completed_at : ''),
                    'date_submitted_qa' => $related_party->qa_start_date,
                    'assigned' => ($related_party->consultant_id != null ? 1 : 0),
                    'consultant' => Client::with('consultant')->where('id', $related_party->client_id)->first(),
                    'data' => $rp_data,
                    'process' => $related_party->process_id,
                    'step' => $related_party->step_id
                ];

                $total++;
            }
        }

        return $excel->download(new DynamicReportExport($client_data,$activity), 'clients_'.date('Y_m_d_H_i_s').'.xlsx');
    }

    public function pdfexport(Activity $activity, Request $request)
    {
        $total = 0;
        $request->session()->forget('path_route');

        $clients = Client::with(['referrer', 'process.steps.activities.actionable.data', 'introducer', 'consultant', 'committee', 'trigger'])->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'),
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

        if ($request->has('committee') && $request->input('committee') != null) {
            $clients = $clients->filter(function ($client) use ($request) {
                return $client->committee_id == $request->input('committee');
            });
        }

        if ($request->has('trigger') && $request->input('trigger') != '') {
            $p = $request->input('trigger');
            $clients = $clients->filter(function ($client) use ($p) {
                return $client->trigger_type_id == $p;
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

        //Actitivy type report filter
        $completed_activity_clients_data = null;
        $filter_data = [];
        $tmp_filter_data = [];


        //loop clients
        $client_data = array();
        $activity_value = '';
        $completed_values = [];
        foreach ($clients as $client) {
            if ($client) {
                switch ($activity->actionable_type) {
                    case 'App\ActionableBoolean':
                        $filter_data = ['' => 'All', 0 => 'No', 1 => 'Yes', 2 => 'Not Completed'];
                        $completed_activity_clients_data = ActionableBooleanData::where('actionable_boolean_id', $activity->actionable_id)
                            ->select('client_id', 'data')
                            ->distinct()
                            ->pluck('data', 'client_id');
                        break;
                    case 'App\ActionableDate':
                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                        $completed_activity_clients_data = ActionableDateData::where('actionable_date_id', $activity->actionable_id)
                            ->select('client_id', 'data')
                            ->distinct()
                            ->pluck('data', 'client_id');
                        break;
                    case 'App\ActionableText':
                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                        $completed_activity_clients_data = ActionableTextData::where('actionable_text_id', $activity->actionable_id)
                            ->select('client_id', 'data')
                            ->distinct()
                            ->pluck('data', 'client_id');
                        break;
                    case 'App\ActionableDropdown':
                        $filter_data = ActionableDropdownItem::where('actionable_dropdown_id', $activity->actionable_id)
                            ->select('name', 'id')
                            ->distinct()
                            ->pluck('name', 'id')
                            ->prepend('All', 0);

                        if ($request->has('activity') && $request->input('activity') != '') {
                            $completed_activity_clients_data = ActionableDropdownData::where('actionable_dropdown_id', $activity->actionable_id)
                                ->where('actionable_dropdown_item_id', $request->input('activity'))
                                ->select('client_id', 'actionable_dropdown_item_id')
                                ->distinct()
                                //->get()->toArray();
                                ->pluck('actionable_dropdown_item_id', 'client_id');
                        } else {
                            $completed_activity_clients_data = ActionableDropdownData::where('actionable_dropdown_id', $activity->actionable_id)
                                ->select('client_id', 'actionable_dropdown_item_id')
                                ->distinct()
                                //->get()->toArray();
                                ->pluck('actionable_dropdown_item_id', 'client_id');
                        }

                        $tmp_filter_data2 = $filter_data->toArray();
                        foreach ($tmp_filter_data2 as $key => $value):
                            array_push($tmp_filter_data, $key);
                        endforeach;
                        break;
                    case 'App\ActionableDocument':
                        $filter_data = ['' => 'All', 1 => 'Completed', 0 => 'Not Completed'];
                        $completed_activity_clients_data = ActionableDocumentData::where('actionable_document_id', $activity->actionable_id)
                            ->select('client_id', 'actionable_document_id')
                            ->distinct()
                            ->pluck('actionable_document_id', 'client_id');
                        break;
                    case 'App\ActionableTemplateEmail':
                        $filter_data = Template::orderBy('name')
                            ->select('name', 'id')
                            ->distinct()
                            ->pluck('name', 'id')
                            ->prepend('All', 0);
                        $completed_activity_clients_data = ActionableTemplateEmailData::where('actionable_template_email_id', $activity->actionable_id)
                            ->select('client_id', 'template_id')
                            ->distinct()
                            ->pluck('template_id', 'client_id');
                        break;
                    case 'App\ActionableNotification':
                        $filter_data = ['' => 'All', 1 => 'Sent', 0 => 'Not Sent'];
                        $completed_activity_clients_data = ActionableNotificationData::where('actionable_notification_id', $activity->actionable_id)
                            ->select('client_id', 'actionable_notification_id')
                            ->distinct()
                            ->pluck('actionable_notification_id', 'client_id');
                        break;
                    case 'App\ActionableMultipleAttachment':
                        $filter_data = Template::orderBy('name')
                            ->select('name', 'id')
                            ->distinct()
                            ->pluck('name', 'id')
                            ->prepend('All', 0);
                        $completed_activity_clients_data = ActionableMultipleAttachmentData::where('actionable_ma_id', $activity->actionable_id)
                            ->select('client_id', 'template_id')
                            ->distinct()
                            ->pluck('template_id', 'client_id');
                        break;
                    default:
                        //todo capture defaults
                        break;
                }

                $data_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? $completed_activity_clients_data[$client->id] : '';
                $completed_value = '';
                $selected_drop_down_names = '';

                $data = '';
                $yn_value = '';
                switch ($activity->actionable_type) {
                    case 'App\ActionableBoolean':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) ? 'Yes' : 'No';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 2;
                        if (isset($completed_activity_clients_data[$client->id]) && $completed_activity_clients_data[$client->id] == '1') {
                            $yn_value = "Yes";
                        }
                        if (isset($completed_activity_clients_data[$client->id]) && $completed_activity_clients_data[$client->id] == '0') {
                            $yn_value = "No";
                        }
                        break;
                    case 'App\ActionableDate':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 1 : 0;
                        break;
                    case 'App\ActionableText':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 1 : 0;
                        break;
                    case 'App\ActionableDropdown':
                        $data_value = '';
                        if ($request->has('s') && $request->input('s') != '') {
                            $selected_drop_down_items = ActionableDropdownData::with('item')->where('actionable_dropdown_id', $activity->actionable_id)
                                ->where('client_id', $client->id)
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
                        }
                        $data = ActionableDropdownData::with('item')->where('client_id', $client->id)->where('actionable_dropdown_id', $activity->actionable_id)->first();
                        $activity_data_value = isset($completed_activity_clients_data[$client->id]) ? (isset($data->item->name) && $data->item->name != null ? $data->item->name : '') : '';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 0;
                        break;
                    case 'App\ActionableDocument':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Yes' : 'No';

                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 0;
                        break;
                    case 'App\ActionableTemplateEmail':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 0;
                        break;
                    case 'App\ActionableNotification':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 1 : 0;
                        break;
                    case 'App\ActionableMultipleAttachment':
                        $completed_value = isset($completed_activity_clients_data[$client->id]) && trim($completed_activity_clients_data[$client->id]) != '' ? 'Completed' : 'Not Completed';
                        $activity_value = isset($completed_activity_clients_data[$client->id]) ? $completed_activity_clients_data[$client->id] : 0;
                        break;
                    default:
                        //todo capture defaults
                        break;
                }

                //dd($completed_value);
                //add to array
                $client_data[$client->id] = [
                    'type' => 'P',
                    'company' => ($client->company != null ? $client->company : $client->first_name . ' ' . $client->last_name),
                    'case_nr' => $client->case_number,
                    'cif_code' => $client->cif_code,
                    'committee' => ($client->committee_id > 0 ? $client->committee->name : ''),
                    'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                    'activity_data' => (isset($activity_data_value) && $activity_data_value != null ? $activity_data_value : ''),
                    'id' => $client->id,
                    'instruction_date' => ($client->instruction_date != null ? $client->instruction_date : ''),
                    'consultant' => isset($client->consultant_id) ? $client->consultant->first_name . ' ' . $client->consultant->last_name : null,
                    'activity_value' => $activity_value,
                    'data_value' => $data_value,
                    'completed_yn' => ($activity->actionable_type == "App\ActionableBoolean" ? $yn_value : $completed_value),
                    'selected_drop_down_names' => $selected_drop_down_names,
                    'introducer' => $client->introducer,
                    'avatar' => $client->avatar
                ];

                $total++;
            }
        }


        $deleted_rps = RelatedPartiesTree::select('related_party_id')->get();

        $related_parties = RelatedParty::with(['client', 'referrer', 'process.steps.activities.actionable.data', 'introducer', 'committee', 'trigger'])->select('*', DB::raw('CONCAT(first_name," ",COALESCE(`last_name`,"")) as full_name'),
            DB::raw('ROUND(DATEDIFF(completed_at, created_at), 0) as completed_days'),
            DB::raw('CAST(AES_DECRYPT(`hash_company`, "Qwfe345dgfdg") AS CHAR(50)) hash_company'),
            DB::raw('CAST(AES_DECRYPT(`hash_first_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_first_name'),
            DB::raw('CAST(AES_DECRYPT(`hash_last_name`, "Qwfe345dgfdg") AS CHAR(50)) hash_last_name'),
            DB::raw('CAST(AES_DECRYPT(`hash_cif_code`, "Qwfe345dgfdg") AS CHAR(50)) hash_cif_code'),
            DB::raw('CAST(`case_number` AS CHAR(50)) AS case_number')
        )->whereIn('id', collect($deleted_rps)->toArray());

        if ($request->has('q') && $request->input('q') != '') {

            $related_parties = $related_parties->having('hash_company', 'like', "%" . $request->input('q') . "%")
                ->orHaving('hash_first_name', 'like', "%" . $request->input('q') . "%")
                ->orHaving('hash_last_name', 'like', "%" . $request->input('q') . "%")
                ->orHaving('hash_cif_code', 'like', "%" . $request->input('q') . "%")
                ->orHaving('case_number', 'like', "%" . $request->input('q') . "%");
        }

        $related_parties = $related_parties->get();

        if ($request->has('user') && $request->input('user') != null) {
            $related_parties = $related_parties->filter(function ($related_party) use ($request) {
                return $related_party->client->consultant_id == $request->input('user');
            });
        }

        if ($request->has('committee') && $request->input('committee') != null) {
            $related_parties = $related_parties->filter(function ($related_party) use ($request) {
                return $related_party->committee_id == $request->input('committee');
            });
        }

        if ($request->has('trigger') && $request->input('trigger') != '') {
            $p = $request->input('trigger');
            $related_parties = $related_parties->filter(function ($related_party) use ($p) {
                return $related_party->trigger_type_id == $p;
            });
        }

        if ($request->has('f') && $request->input('f') != '') {
            $p = $request->input('f');
            $related_parties = $related_parties->filter(function ($related_party) use ($p) {
                return $related_party->instruction_date >= $p;
            });
        }

        if ($request->has('t') && $request->input('t') != '') {
            $p = $request->input('t');
            $related_parties = $related_parties->filter(function ($related_party) use ($p) {
                return Carbon::parse($related_party->instruction_date)->format("Y-m-d") <= $p;
            });
        }


        foreach ($related_parties as $related_party) {
            $rp_data = [];
            if ($related_party){
                $rpl = ActivityRelatedPartyLink::where('primary_activity', $activity->id)->first();
                $rp_activity = Activity::where('id', $rpl->related_activity)->first();

                switch ($rp_activity["actionable_type"]) {
                    case 'App\RelatedPartyBoolean':
                        $yn_value = '';

                        $data2 = RelatedPartyBooleanData::where('related_party_id', $related_party->id)->where('related_party_boolean_id', $rp_activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                        if (isset($data2["data"]) && $data2["data"] == '1') {
                            $yn_value = "Yes";
                        }
                        if (isset($data2["data"]) && $data2["data"] == '0') {
                            $yn_value = "No";
                        }
                        array_push($rp_data, $yn_value);
                        break;
                    case 'App\RelatedPartyDate':
                        //dd($activity);
                        $data_value = '';

                        $data2 = RelatedPartyDateData::where('related_party_id', $related_party->id)->where('related_party_date_id', $rp_activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                        $data_value = $data2["data"];

                        array_push($rp_data, $data_value);
                        break;
                    case 'App\RelatedPartyText':
                        $data_value = '';

                        $data2 = RelatedPartyTextData::where('related_party_id', $related_party->id)->where('related_party_text_id', $rp_activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                        $data_value = $data2["data"];

                        array_push($rp_data, $data_value);
                        break;
                    case 'App\RelatedPartyTextarea':
                        $data_value = '';

                        $data2 = RelatedPartyTextareaData::where('related_party_id', $related_party->id)->where('related_party_textarea_id', $rp_activity->actionable_id)->orderBy('created_at', 'desc')->take(1)->first();

                        $data_value = $data2["data"];

                        array_push($rp_data, $data_value);
                        break;
                    case 'App\RelatedPartyDropdown':
                        $data_value = '';

                        $data2 = RelatedPartyDropdownData::with('item')->where('related_party_id', $related_party->id)->where('related_party_dropdown_id', $rp_activity->actionable_id)->get();

                        foreach ($data2 as $key => $value):
                            if (count($data2) > 1) {
                                $data_value .= $value["item"]["name"] . ', ';
                            } else {
                                $data_value .= $value["item"]["name"];
                            }
                        endforeach;
                        array_push($rp_data, $data_value);
                        break;
                    case 'App\RelatedPartyDocument':
                        $yn_value = '';

                        $data2 = RelatedPartyDocumentData::where('related_party_id', $related_party->id)->where('related_party_document_id', $activity->actionable_id)->take(1)->first();

                        if (isset($data2["document_id"])) {
                            $yn_value = "Yes";
                        } else {
                            $yn_value = "No";
                        }
                        array_push($rp_data, $yn_value);
                        break;
                    case 'App\RelatedPartyTemplateEmail':
                        $yn_value = '';

                        $data2 = RelatedPartyTemplateEmailData::where('related_party_id', $related_party->id)->where('related_party_template_email_id', $activity->actionable_id)->orderBy('created_at','desc')->take(1)->first();

                        if (isset($data2["data"]) && $data2["data"] == '1') {
                            $yn_value = "Yes";
                        }
                        if (isset($data2["data"]) && $data2["data"] == '0') {
                            $yn_value = "No";
                        }
                        array_push($rp_data, $yn_value);
                        break;
                    case 'App\RelatedPartyNotification':
                        $yn_value = '';

                        $data2 = RelatedPartyNotificationData::where('related_party_id', $related_party->id)->where('related_party_notification_id', $activity->actionable_id)->orderBy('created_at','desc')->take(1)->first();

                        if (isset($data2["data"]) && $data2["data"] == '1') {
                            $yn_value = "Yes";
                        }
                        if (isset($data2["data"]) && $data2["data"] == '0') {
                            $yn_value = "No";
                        }
                        array_push($rp_data, $yn_value);
                        break;
                    case 'App\RelatedPartyMultipleAttachment':
                        $yn_value = '';

                        $data2 = RelatedPartyMultipleAttachmentData::where('related_party_id', $related_party->id)->where('related_party_ma_id', $activity->actionable_id)->orderBy('created_at','desc')->take(1)->first();

                        if (isset($data2["data"]) && $data2["data"] == '1') {
                            $yn_value = "Yes";
                        }
                        if (isset($data2["data"]) && $data2["data"] == '0') {
                            $yn_value = "No";
                        }
                        array_push($rp_data, $yn_value);
                        break;
                    default:
                        //todo capture defaults
                        break;
                }


                $client_data[$related_party->client_id]['rp'][$related_party->id] = [
                    'type' => 'R',
                    'company' => ($related_party->company != null ? $related_party->company : $related_party->first_name . ' ' . $related_party->last_name),
                    'id' => $related_party->id,
                    'client_id' => $related_party->client_id,
                    'case_nr' => $related_party->case_number,
                    'cif_code' => $related_party->cif_code,
                    'committee' => isset($related_party->committee) ? $related_party->committee->name : null,
                    'trigger' => ($client->trigger_type_id > 0 ? $client->trigger->name : ''),
                    'instruction_date' => $related_party->instruction_date,
                    'completed_at' => ($related_party->completed_at != null ? $related_party->completed_at : ''),
                    'date_submitted_qa' => $related_party->qa_start_date,
                    'assigned' => ($related_party->consultant_id != null ? 1 : 0),
                    'consultant' => Client::with('consultant')->where('id', $related_party->client_id)->first(),
                    'data' => $rp_data,
                    'process' => $related_party->process_id,
                    'step' => $related_party->step_id
                ];

                $total++;
            }
        }
        $parameters = [
            'clients' => $client_data,
            'filter_data' => $filter_data,
            'processes' => Process::orderBy('name')->pluck('name', 'id')->prepend('All processes', 'all'),
            'steps' => Step::orderBy('process_id')->orderBy('order')->pluck('name', 'id')->prepend('All steps', ''),
            'activity' => $activity,
            'committee' => Committee::orderBy('name')->pluck('name', 'id')->prepend('All committees', 'all'),
            'trigger' => TriggerType::orderBy('name')->pluck('name', 'id')->prepend('All trigger types', 'all'),
            'assigned_user' => Client::all()->keyBy('consultant_id')->map(function ($consultant){
                return isset($consultant->consultant)?$consultant->consultant->first_name.' '.$consultant->consultant->last_name:null;
            })->sort(),
            'total' => $total
        ];

        $pdf = PDF::loadView('pdf.customreport', $parameters)->setPaper('a4')->setOrientation('landscape');
        return $pdf->download('clients_'.date('Y_m_d_H_i_s').'.pdf');
    }

    public function converted(Request $request)
    {
        $end_date = Carbon::now();

        if ($request->has('t')) {
            $end_date = Carbon::parse($request->input('t'));
        }

        $start_year = $end_date->copy()->subYear();
        $start_quarter = $end_date->copy()->subMonths(3);
        $start_month = $end_date->copy()->subMonth();

        $config = Config::first();

        $last_year = Client::where('completed_at', '>=', $start_year)->where('completed_at', '<=', $end_date)->count();
        $last_quarter = Client::where('completed_at', '>=', $start_quarter)->where('completed_at', '<=', $end_date)->count();
        $last_month = Client::where('completed_at', '>=', $start_month)->where('completed_at', '<=', $end_date)->count();

        $parameters = [
            'end_date' => $end_date->toDateString(),
            'clients' => [
                'last_year' => [
                    'actual' => $last_year,
                    'target' => $config->onboards_per_day * $end_date->diffInDays($start_year),
                    'difference' => $last_year - ($config->onboards_per_day * $end_date->diffInDays($start_year)),
                ],
                'last_quarter' => [
                    'actual' => $last_quarter,
                    'target' => $config->onboards_per_day * $end_date->diffInDays($start_quarter),
                    'difference' => $last_quarter - ($config->onboards_per_day * $end_date->diffInDays($start_quarter)),
                ],
                'last_month' => [
                    'actual' => $last_month,
                    'target' => $config->onboards_per_day * $end_date->diffInDays($start_month),
                    'difference' => $last_month - ($config->onboards_per_day * $end_date->diffInDays($start_month)),
                ]
            ],
            'processes' => Process::orderBy('name')->pluck('name', 'id')->prepend('All processes', 'all')
        ];

        return view('reports.converted')->with($parameters);
    }

    public function fees()
    {
        $parameters = [
            'processes' => Process::orderBy('name')->pluck('name', 'id')->prepend('All processes', 'all')
        ];

        return view('reports.fees')->with($parameters);
    }

    public function referrer(Request $request)
    {
        $referrers = Referrer::select(DB::raw("CONCAT(first_name,' ',COALESCE(`last_name`,'')) AS full_name"), 'id')->orderBy('first_name')->orderBy('last_name')->pluck('full_name', 'id')->prepend('All Referrers', 'all');
        $clients = Client::with('referrer')->orderBy('company')->get();

        if ($request->has('referrer') && $request->input('referrer') != 'all') {
            $clients = Client::with('referrer')->where('referrer_id', $request->input('referrer'))->orderBy('company')->get();
        }

        $parameters = [
            'referrer_options' => $referrers,
            'clients' => $clients
        ];
        return view('reports.referrer')->with($parameters);
    }

    public function conversion(Request $request)
    {
        $types = [
            1 => 'Lead to Prospective',
            2 => 'Prospective to Service Agreed',
            3 => 'Service Agreed to Converted',
            4 => 'First Contact to Converted',
        ];

        switch ($request->input('type')) {
            case 1:
                $target = Activity::whereIn('step_id', [1, 2, 3])->sum('threshold');
                break;
            case 2:
                $target = Activity::whereIn('step_id', [3, 4])->sum('threshold');
                break;
            case 3:
                $target = Activity::whereIn('step_id', [4, 5])->sum('threshold');
                break;
            case 4:
            default:
                $target = Activity::sum('threshold');
                break;
        }

        $end_date = Carbon::now();

        if ($request->has('t')) {
            $end_date = Carbon::parse($request->input('t'));
        }

        $start_year = $end_date->copy()->subYear();
        $start_quarter = $end_date->copy()->subMonths(3);
        $start_month = $end_date->copy()->subMonth();

        $config = Config::first();

        $last_year = Client::where('completed_at', '>=', $start_year)->where('completed_at', '<=', $end_date)->count();
        $last_quarter = Client::where('completed_at', '>=', $start_quarter)->where('completed_at', '<=', $end_date)->count();
        $last_month = Client::where('completed_at', '>=', $start_month)->where('completed_at', '<=', $end_date)->count();

        $parameters = [
            'end_date' => $end_date->toDateString(),
            'types' => $types,
            'clients' => [
                'last_year' => [
                    'actual' => $last_year,
                    'target' => $config->onboards_per_day * $end_date->diffInDays($start_year),
                    'difference' => $last_year - ($config->onboards_per_day * $end_date->diffInDays($start_year)),
                ],
                'last_quarter' => [
                    'actual' => $last_quarter,
                    'target' => $config->onboards_per_day * $end_date->diffInDays($start_quarter),
                    'difference' => $last_quarter - ($config->onboards_per_day * $end_date->diffInDays($start_quarter)),
                ],
                'last_month' => [
                    'actual' => $last_month,
                    'target' => $config->onboards_per_day * $end_date->diffInDays($start_month),
                    'difference' => $last_month - ($config->onboards_per_day * $end_date->diffInDays($start_month)),
                ]
            ],
            'processes' => Process::orderBy('name')->pluck('name', 'id')->prepend('All processes', 'all')
        ];

        return view('reports.conversion')->with($parameters);
    }

    public function feeProposalSent(Request $request)
    {
        $actionable_id = Activity::where('name', 'Fee proposal sent?')->first()->actionable_id;

        $actionable_template_email_data = ActionableTemplateEmailData::with('client.process')->where('actionable_template_email_id', $actionable_id)->select('client_id', DB::raw('MIN(created_at) as min_created_at, MAX(created_at) as max_created_at'))->groupBy('client_id')->orderBy('created_at')->get();

        $parameters = [
            'actionable_template_email_data' => $actionable_template_email_data,
            'processes' => Process::orderBy('name')->pluck('name', 'id')->prepend('All processes', 'all')
        ];

        return view('reports.feeproposalsent')->with($parameters);
    }

    public function loa()
    {
        $loa_actionable_id = Activity::where('name', 'LOA (Letter of Authority)')->first()->actionable_id;

        $actionable_template_email_data = ActionableTemplateEmailData::with('client.process')
            ->where('actionable_template_email_id', $loa_actionable_id)
            ->select('client_id')
            ->distinct()
            ->pluck('client_id');

        $clients = Client::with('process')
            ->whereIn('id', $actionable_template_email_data)
            ->get();

        $parameters = [
            'clients' => $clients,
            'processes' => Process::orderBy('name')->pluck('name', 'id')->prepend('All processes', 'all')
        ];

        return view('reports.loa')->with($parameters);

    }

    public function loe()
    {
        $loe_actionable_id = Activity::where('name', 'LOE (Letter of Engagement)')->first()->actionable_id;

        $actionable_template_email_data = ActionableTemplateEmailData::with('client.process')
            ->where('actionable_template_email_id', $loe_actionable_id)
            ->select('client_id')
            ->distinct()
            ->pluck('client_id');

        $clients = Client::with('process')
            ->whereIn('id', $actionable_template_email_data)
            ->get();

        $parameters = [
            'clients' => $clients,
            'processes' => Process::orderBy('name')->pluck('name', 'id')->prepend('All processes', 'all')
        ];

        return view('reports.loe')->with($parameters);
    }

    public function aml()
    {
        $aml_actionable_id = Activity::where('name', 'AML forms upload')->first()->actionable_id;

        $actionable_document_data = ActionableDocumentData::with('client.process')
            ->where('actionable_document_id', $aml_actionable_id)
            ->distinct()
            ->pluck('client_id');

        $clients = Client::with('process')
            ->whereIn('id', $actionable_document_data)
            ->get();

        $parameters = [
            'clients' => $clients,
            'processes' => Process::orderBy('name')->pluck('name', 'id')->prepend('All processes', 'all')
        ];

        return view('reports.aml')->with($parameters);
    }

    public function crf()
    {
        $crf_actionable_id = Activity::where('name', 'Client Registration Form completed (CRF)')->first()->actionable_id;

        //dd($crf_actionable_id);
        $actionable_dropdown_data = ActionableDropdownData::with('client.process')
            ->where('actionable_dropdown_id', $crf_actionable_id)
            ->select('client_id')
            ->distinct()
            ->pluck('client_id');

        $clients = Client::with('process')
            ->whereIn('id', $actionable_dropdown_data)
            ->get();

        $parameters = [
            'clients' => $clients,
            'processes' => Process::orderBy('name')->pluck('name', 'id')->prepend('All processes', 'all')
        ];

        return view('reports.crf')->with($parameters);
    }

    public function clientReports(Request $request)
    {
        /*
         * Stages for actions are
         * 1. No
         * 2. In progress
         * 3. Yes
         * 4. N/A
         */

        $actionables = [
            'sloa' => Activity::where('name', 'LOA (Letter of Authority)')->first(),
            'loe' => Activity::where('name', 'LOE (Letter of Engagement)')->first(),
            'crf' => Activity::where('name', 'CRF (Client Registration Form) completed?')->first(),
            'aml' => Activity::where('name', 'AML Forms received?')->first()
        ];

        $dependants = [
            'sloa' => [],
            'loe' => [],
            'crf' => [],
            'aml' => []
        ];

        //find the actionable data for each activity dependency
        foreach ($actionables as $actionable_key => $actionable) {
            if (!is_null($actionable->dependant_activity_id)) {
                array_push($dependants[$actionable_key], $actionable->dependant->actionable->data);
            }

            //todo optimise so that if they all have the same dependendant it should reuse the dependency chain
            $current_dependant = ($actionable->dependant_activity_id) ? $actionable->dependant : null;
            while (!is_null($current_dependant->dependant_activity_id)) {
                array_push($dependants[$actionable_key], $current_dependant->dependant->actionable->data);
                $current_dependant = $current_dependant->dependant;
            }
        }

        //get all data for completed rows of the fields
        $completed = [
            'sloa' => ActionableDocumentData::where('actionable_document_id', $actionables['sloa']->actionable_id)
                ->select('client_id')
                ->distinct()
                ->pluck('client_id', 'client_id'),
            'loe' => ActionableTemplateEmailData::where('actionable_template_email_id', $actionables['loe']->actionable_id)
                ->select('client_id')
                ->distinct()
                ->pluck('client_id', 'client_id'),
            'crf' => ActionableBooleanData::where('actionable_boolean_id', $actionables['crf']->actionable_id)
                ->select('client_id', 'data')
                ->distinct()
                ->pluck('data', 'client_id'),
            'aml' => ActionableTemplateEmailData::where('actionable_template_email_id', $actionables['aml']->actionable_id)
                ->select('client_id')
                ->distinct()
                ->pluck('client_id', 'client_id'),
        ];


        $client_data = new Collection();

        $clients = Client::select('company', 'id','process_id', 'is_progressing')->with('process.steps.activities.actionable.data');

        //if company name query filter on that
        if($request->has('q') && $request->input('q') != ''){
            $clients->where('company','LIKE','%'.$request->input('q').'%');
        }

        $clients = $clients->get();

        //loop clients
        foreach ($clients as $client) {

            //if there is a completed data row set it to complete else set to not complete
            $sloa = (isset($completed['sloa'][$client->id])) ? 1 : 2;
            $loe = (isset($completed['loe'][$client->id])) ? 1 : 2;
            //additionally test if data is true
            $crf = (isset($completed['crf'][$client->id]) && $completed['crf'][$client->id] == true) ? 1 : 2;
            $aml = (isset($completed['aml'][$client->id])) ? 1 : 2;

            //if incomplete test if in progress
            if ($sloa == 2) {
                foreach ($dependants['sloa'] as $dependant) {
                    if (count($dependant->where('client_id', $client->id))) {
                        $sloa = 3;
                        break;
                    }
                }
            }

            if ($loe == 2) {
                foreach ($dependants['loe'] as $dependant) {
                    if (count($dependant->where('client_id', $client->id))) {
                        $loe = 3;
                        break;
                    }
                }
            }

            if ($crf == 2) {
                foreach ($dependants['crf'] as $dependant) {
                    if (count($dependant->where('client_id', $client->id))) {
                        $crf = 3;
                        break;
                    }
                }
            }

            if ($aml == 2) {
                foreach ($dependants['aml'] as $dependant) {
                    if (count($dependant->where('client_id', $client->id))) {
                        $aml = 3;
                        break;
                    }
                }
            }


            $current_step = $client->getCurrentStep();

            //add to array
            $client_data->push([
                'name' => $client->company,
                'id' => $client->id,
                'is_progressing' => $client->is_progressing,
                'step' => $current_step['name'],
                'step_id' => $current_step['id'],
                'sloa' => $sloa,
                'loe' => $loe,
                'crf' => $crf,
                'aml' => $aml,
                'reports' => $request->has('reports')?$request->input('reports'):1
            ]);
        }

        //if step filter is provided, filter on the provided step
        if ($request->has('s') && $request->input('s') != 'all' && $request->input('s') != '') {
            $selected_step = $request->input('s');
            $client_data = $client_data->filter(function ($client) use ($selected_step) {
                return $client['step_id'] == $selected_step;
            });
        }

        //if filter is provided, filter on the provided stage
        if($request->has('sloa') && $request->input('sloa') !=0 ){
            $sloa_input = $request->input('sloa');

            $client_data = $client_data->filter(function ($client) use ($sloa_input) {
                return $client['sloa'] == $sloa_input;
            });
        }

        if($request->has('loe') && $request->input('loe') !=0 ){
            $loe_input = $request->input('loe');

            $client_data = $client_data->filter(function ($client) use ($loe_input) {
                return $client['loe'] == $loe_input;
            });
        }

        if($request->has('crf') && $request->input('crf') !=0 ){
            $crf_input = $request->input('crf');

            $client_data = $client_data->filter(function ($client) use ($crf_input) {
                return $client['crf'] == $crf_input;
            });
        }

        if($request->has('aml') && $request->input('aml') !=0 ){
            $aml_input = $request->input('aml');

            $client_data = $client_data->filter(function ($client) use ($aml_input) {
                return $client['aml'] == $aml_input;
            });
        }

        if($request->has('reports') && $request->input('reports') !=-1 ){
            $reports_input = $request->input('reports');

            $client_data = $client_data->filter(function ($client) use ($reports_input) {
                return $client['is_progressing'] == $reports_input;
            });
        }
        else
        {
            $reports_input = 1;
            $client_data = $client_data->filter(function ($client) use ($reports_input) {
                return $client['is_progressing'] == $reports_input;
            });
        }

        $parameters = [
            'clients' => $client_data,
            'options' => [0 => 'Select', 1 => 'Yes', 2 => 'No', 3 => 'In Progress'],
            'steps' => Step::orderBy('process_id')->orderBy('order')->pluck('name', 'id')->prepend('All steps', 'all')
        ];

        return view('reports.clientreports')->with($parameters);
    }

    public function create()
    {
        $parameters = [
            'activities' => Activity::where('actionable_type','like','%Actionable%')->where('grouping','<=',0)->orderBy('name')->pluck('name', 'id')
        ];
        return view('reports.create')->with($parameters);
    }

    public function store(Request $request)
    {
        $report = new Report;
        $report->name = $request->input('name');
        $report->activity_id = $request->input('activity');
        $report->user_id = auth()->id();
        $report->save();

        return redirect(route('reports.index'))->with('flash_success', 'Report created successfully');
    }

    public function edit($reportid)
    {
        $parameters = [
            'reports' => Report::where('id','=',$reportid)->get(),
            'activities' => Activity::where('actionable_type','like','%Actionable%')->where('grouping','<=',0)->orderBy('name')->pluck('name', 'id')
        ];

        return view('reports.edit')->with($parameters);
    }

    public function update(UpdateReportRequest $request, $reportid){

        $report = Report::find($reportid);
        $report->name = $request->input('name');
        $report->activity_id = $request->input('activity');
        $report->user_id = auth()->id();
        $report->save();

        return redirect(route('reports.index'))->with('flash_success', 'Report saved successfully');
    }

    public function destroy($id){
        Report::destroy($id);

        return redirect(route('reports.index'))->with('flash_success', 'Report deleted successfully');
    }

    public function saveJpg(Request $request){
        //just a random name for the image file
        $random = Client::where('id',$request->input('client'))->first();

        $string = ($random->company != null ? preg_replace('/[^a-zA-Z0-9_ -]/s','',$random->company) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$random->first_name.' '.$random->last_name));

        $name = strtolower(str_replace(' ','_',$string));

        if(File::exists(storage_path('app/forms/')."$name.jpg")){

            Storage::delete(storage_path('app/forms/')."$name.jpg");
        }

        $savefile = @file_put_contents(storage_path('app/forms/')."$name.jpg", base64_decode(explode(",", $request->input('data'))[1]));

        if($savefile){
            return response()->json(['urlpath'=>storage_path('app/forms/')."$name.jpg"]);
        }
    }

}