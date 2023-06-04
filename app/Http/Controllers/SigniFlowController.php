<?php

namespace App\Http\Controllers;

use App\ActionableBooleanData;
use App\ActionableDateData;
use App\ActionableDropdownItem;
use App\ActionableTextareaData;
use App\FormInputBoolean;
use App\FormInputBooleanData;
use App\FormInputDate;
use App\FormInputDateData;
use App\FormInputTextareaData;
use App\FormSection;
use App\Process;
use App\SigniFlow;
use App\Step;
use Illuminate\Http\Request;

use App\ActionableTextData;
use App\Activity;
use App\Client;
use App\Document;
use App\FormInputDropdown;
use App\FormInputDropdownData;
use App\FormInputDropdownItem;
use App\FormInputText;
use App\FormInputTextData;
use App\FormSectionInputs;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class SigniFlowController extends Controller
{
    /**
     * Login to CPB and get token.
     *
     * @return $token
     */
    public function login()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://dr.docfusion-paas.com:44331/core/connect/token",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "client_id=AttoohClient&client_secret=@tT0o%23357&grant_type=client_credentials&scope=DocFusion&resource=DocFusion",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $decoded = json_decode($response, true);

        return $decoded['access_token'];

    }

    public function getSigniflowDocument($client_id, $user_id, $process_id)
    {
        $process = Process::find($process_id);

        if ($process->document == '' || $process->document == null) {
            $client = Client::find($client_id);
            $inputData['last_name'] = $client->last_name;
            $inputData['first_name'] = $client->first_name;
            $inputData['initials'] = $client->initials;
            $inputData['cell'] = $client->contact;
            $inputData['email'] = $client->email;
            $inputData['id_number'] = $client->id_number;

            $step_ids = Step::where('process_id', $process_id)->pluck('id')->toArray();

            $activities = Activity::with(['step'])->whereIn('step_id', $step_ids)->with(['actionable.data' => function ($q) use ($client_id) {
                $q->where('client_id', $client_id);
            }])->orderBy('order', 'asc')->get();

            $counter = 1;
            $signature = [];
            foreach ($activities as $activity) {
                $inputDatakey = '';
                $inputQuestionkey = '';
                if (isset($activity->step->signature) && ($activity->step->signature == 1)) {
                    $inputDatakey = 'signature_f' . $activity->id;
                    $inputQuestionkey = 'signature_q' . $activity->id;
                    $inputData['signature_q' . $activity->id] = $activity->name;
                } else {
                    $inputDatakey = 'f' . $activity->id;
                    $inputData['q' . $activity->id] = $activity->name;
                }

                switch ($activity->actionable_type) {
                    // Get the text data type
                    case 'App\ActionableText':
                        if (isset($activity->actionable->data) && count($activity->actionable->data) > 0) {
                            $inputData[$inputDatakey] = isset($activity->actionable->data[0]) ? $activity->actionable->data[0]->data : '';
                        } else {
                            $inputData[$inputDatakey] = '';
                        }

                        // Dynamically set signatures
                        if (isset($activity->step->signature) && ($activity->step->signature == 1)) {
                            $signature['Fieldname'] = 'S00' . $counter;
                            $signature['FieldType'] = 'Signature';

                            if (strpos($inputData[$inputQuestionkey], 'Name') !== false) {
                                $signature['Firstname'] = $inputData[$inputDatakey];
                            }

                            if (strpos($inputData[$inputQuestionkey], 'Surname') !== false) {
                                $signature['Lastname'] = $inputData[$inputDatakey];
                            }

                            if (strpos($inputData[$inputQuestionkey], 'Email') !== false) {
                                $signature['Email'] = $inputData[$inputDatakey];
                            }

                            $signature['Date'] = date('Y-m-d');

                            if (strpos($inputData[$inputQuestionkey], 'Cellphone') !== false) {
                                $signature['Mobile'] = $inputData[$inputDatakey];
                                $inputData['signatories']['Sig' . $counter] = $signature;
                                $signature = [];
                                $counter++;
                            }

                            $signature['OutField'] = '';
                            $signature['Data'] = [];
                        }
                        break;

                    // Get the drop down data type
                    case 'App\ActionableDropdown':
                        if (isset($activity['actionable']->data) && count($activity["actionable"]->data) > 0) {
                            $data = ActionableDropdownItem::where('id', $activity->actionable->data[0]->actionable_dropdown_item_id)->first();
                            $inputData[$inputDatakey] = isset($data->name) ? $data->name : '';
                        } else {
                            $inputData[$inputDatakey] = '';
                        }
                        break;

                    // Get the text area data type
                    case 'App\ActionableTextarea':
                        // If field is text area and has a table, break the table up and extract values from the table,
                        // else just take the field as it is and strip tags as well as remove line breaks, so it does not break the docFusion signature
                        if (isset($activity['actionable']->data) && count($activity["actionable"]->data) > 0) {
                            if (strpos($activity['actionable']->data[0]->data, '</table>') !== false) {
                                $domLine = new \DOMDocument();
                                $domLine->loadHTML(trim($activity['actionable']->data[0]->data));
                                $detail = $domLine->getElementsByTagName('td');

                                $tableArray = [];
                                $count = 0;
                                foreach ($detail as $nodeDetail) {
                                    $inputData[$inputDatakey . "_" . $count] = trim($nodeDetail->textContent);
                                    $count++;
                                }
                            } else {
                                $inputData[$inputDatakey] = preg_replace("/\r|\n/", "", $activity['actionable']->data[0]->data);
                            }
                        } else {
                            $inputData[$inputDatakey] = '';
                        }
                        break;

                    // Get the boolean data type
                    case 'App\ActionableBoolean':
                        $booleanData = ActionableBooleanData::where('client_id', $client_id)->where('actionable_boolean_id', $activity['actionable']->id)->first();

                        if (isset($booleanData)) {
                            $inputData[$inputDatakey] = trim($booleanData->data) == 0 ? 'No' : 'Yes';
                        }
                        break;

                    // Get the date data type
                    case 'App\ActionableDate':
                        // $inputData[$inputDatakey] = date('Ymd', strtotime($activity['actionable']->data));
                        if (isset($activity['actionable']->id) && ($activity["actionable"]->id > 0)) {
                            $data = ActionableDateData::orderBy('id', 'desc')->where('actionable_date_id', $activity['actionable']->id)->first();
                            $inputData[$inputDatakey] = isset($data->data) ? $data->data : '';
                        } else {
                            $inputData[$inputDatakey] = '';
                        }
                        break;

                    // Get any other data type not handled by the case blocks above
                    default:
                        if (isset($activity['actionable']->data) && count($activity["actionable"]->data) > 0) {
                            $inputData[$inputDatakey] = $activity['actionable']->data;
                        } else {
                            $inputData[$inputDatakey] = '';
                        }
                        break;
                }

                // Remove signature fields, since they are added in signatories array already
                if (isset($activity->step->signature) && ($activity->step->signature == 1)) {
                    // Remove this signature field, as it is added to the signatories
                    unset($inputData[$inputQuestionkey]);
                    unset($inputData[$inputDatakey]);
                }
            }

            if ($process->id == 34) {
                $form_section_inputs = FormSectionInputs::orderBy('id')->get();

                foreach ($form_section_inputs as $form_section_input) {
                    switch ($form_section_input->input_type) {
                        case 'App\FormInputText':
                            $form_inputs_text_data = FormInputTextData::select('id', 'data', 'form_input_text_id')->where('form_input_text_id', $form_section_input['input_id'])->where('client_id', $client->id)->orderBy('id', 'desc')->first();
                            if (isset($form_inputs_text_data)) {
                                $inputData['s' . $form_section_input->id] = trim($form_inputs_text_data->data);
                            }
                            break;

                        case 'App\FormInputBoolean':
                            $form_inputs_boolean_data = FormInputBooleanData::select('id', 'data', 'form_input_boolean_id')->where('form_input_boolean_id', $form_section_input['input_id'])->where('client_id', $client->id)->orderBy('id', 'desc')->first();
                            if (isset($form_inputs_boolean_data)) {
                                $inputData['s' . $form_section_input->id] = trim($form_inputs_boolean_data->data) == 1 ? 'Yes' : 'No';
                            }
                            break;

                        case 'App\FormInputDate':
                            $form_inputs_date_data = FormInputDateData::select('id', 'data', 'form_input_date_id')->where('form_input_date_id', $form_section_input['input_id'])->where('client_id', $client->id)->orderBy('id', 'desc')->first();
                            if (isset($form_inputs_date_data)) {
                                $inputData['s' . $form_section_input->id] = trim($form_inputs_date_data->data);
                            }
                            break;

                        case 'App\FormInputDropdown':
                            $form_inputs_dropdown_data_ids = FormInputDropdownData::select('id', 'form_input_dropdown_item_id', 'form_input_dropdown_id')->where('form_input_dropdown_id', $form_section_input['input_id'])->where('client_id', $client->id)->pluck('form_input_dropdown_item_id')->toArray();

                            if (isset($form_inputs_dropdown_data_ids) && !empty($form_inputs_dropdown_data_ids)) {
                                $values = FormInputDropdownItem::orderBy('name')->whereIn('id', $form_inputs_dropdown_data_ids)->pluck('name')->toArray();
                                $inputData['s' . $form_section_input->id] = implode(',', $values);
                            }
                            break;
                    }
                }
            }

            $token = $this->login();

            $inputJson = json_encode($inputData);

            $csv_string = '';;
            foreach ($inputData as $key => $value) {
                if (is_array($key) && is_array($value)) {
                    $csv_string .= implode('|', $key) . '|' . implode('|', $value) . PHP_EOL;
                } else if (is_array($key)) {
                    $csv_string .= implode('|', $key) . '|' . $value . PHP_EOL;
                } else if (is_array($value)) {
                    // Todo: This are the signatures, so they fail
                    // $csv_string = $key.'|'.implode('|', $value).PHP_EOL;
                } else {
                    $csv_string .= $key . '|' . $value . PHP_EOL;
                }
            }

            file_put_contents('goshen15.csv', $csv_string);
            file_put_contents('goshen15.json', $inputJson);

            try {
                $data = base64_encode($inputJson);

                $postFields = "{
                \r\n    \"Source\": \"Atooh Test\",
                \r\n    \"JobReference\": \"Atooh Hello World\",
                \r\n    \"Timeout\": \"00:01:00\",
                \r\n    \"Process\": {
                \r\n        \"ProcessType\": \"Process\",
                \r\n        \"BusinessUnitGuid\" : \"5ba96b19-5402-4ff9-8dd6-2990365eca81\",
                \r\n        \"ChainGuid\": \"72f1976c-d7de-4b0b-9e0f-576eab7753e1\",
                \r\n        \"ChainVersion\": null,
                \r\n        \"WorkflowProcessGuid\": \"" . $process->docfusion_process_id . "\",
                \r\n        \"WorkflowProcessVersion\": null,
                \r\n        \"TemplateGuid\" : \"" . $process->docfusion_template_id . "\",
                \r\n        \"ProcessAsync\": false,
                \r\n        \"ReturnData\": \"JSON\",
                \r\n    \t\t   },
                \r\n    \"Data\": {
                \r\n        \"DataType\": \"JSON\",
                \r\n        \"Data\": \"$data\",
                \r\n        \"DataProcessor\": null,
                \r\n        \"ProcessorProfile\": null
                \r\n    },
                \r\n    \"Parameters\": {}\r\n
            }";

                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "https://dr.docfusion-paas.com/api/DocFusionV2/GenerateDocumentFull",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => $postFields,
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: Bearer " . $token,
                        "Content-Type: application/json"
                    ),
                ));

                $curlOutput = curl_exec($curl);

                curl_close($curl);

                $returnedData = json_decode($curlOutput);

                // dd($returnedData);

                // Have to go through all this process because docfusion does not know how to return errors
                file_put_contents('docfusion_error_log.txt', $returnedData->GenerationLog);
                file_put_contents('signiflow_error_log.txt', $returnedData->ReturnData);

                /*$file = fopen("docfusion_error_log.txt","r");
                $docfusion_error_log = '{}';
                while(! feof($file))
                {
                    $line =  fgets($file);
                    if($line[0] == '{'){
                        $docfusion_error_log = rtrim($line);
                    }
                }
                fclose($file);

                $docfusion_error_log = json_decode(str_replace('True', '"True"', $docfusion_error_log));*/

                // if($returnedData->GenerationLog['Success'] == true) // Does not work becuase this is inside a long string, how inconvenient
                $document_file = '';
                $docfusion_message = '';
                $signiflow_message = '';
                $success = 1;
                $error_message = '';
                if ($returnedData->Success == true) {
                    $folder = '';
                    $file_name = Carbon::now()->format('Y-m-d') . "-" . strtotime(Carbon::now()) . ".pdf";

                    $base64data = base64_decode($returnedData->DocumentData, true);
                    $file_path = "{$folder}/{$file_name}";

                    $result = file_put_contents(storage_path('app/documents') . $file_path, $base64data);

                    $document = new Document();
                    $document->name = 'Signiflow - ' . $process->name;
                    $document->file = $file_path;
                    $document->user_id = $user_id;
                    $document->client_id = $client->id;
                    $document->save();

                    $document_file = $document->file;

                    $docfusion_message = $process->name . " document generated successfully. <br>Click <a href='/storage/document?q={$file_path}' target='_blank' style='color: red !important;'>here</a> to view the document.";

                    // Signiflow part
                    $returnedData->ReturnData = str_replace('True', '"True"', $returnedData->ReturnData);
                    $returnedData->ReturnData = str_replace('False', '"False"', $returnedData->ReturnData);
                    $returnedData->ReturnData = json_decode($returnedData->ReturnData);

                    // dd($returnedData->ReturnData);

                    if (isset($returnedData->ReturnData->Success) && ($returnedData->ReturnData->Success) == 'True') {
                        $signiflow_message = "Document was emailed for signatures";
                    } else {
                        $success = 0;
                        $error_array = (array)$returnedData->ReturnData;
                        // dd($error_array);
                        $error_message = $process->name . ' signature failed: Error: ' . $error_array['Error '];
                    }
                } else {
                    $success = 0;
                    // $docfusion_message = $returnedData->GenerationLog->Error; // Does not work becuase this is inside a long string, how inconvenient
                    $error_message = $process->name . " document could not be generated. Error log: " . env('APP_URL') . "/docfusion_error_log.txt";
                }

                return response()->json([
                    'http_code' => 200,
                    'success' => $success,
                    'message' => $docfusion_message . ".<br/><br/>" . $signiflow_message,
                    'error_message' => $error_message,
                    'file_path' => $document_file,
                    'docfusion_message' => $docfusion_message,
                    'signiflow_message' => $signiflow_message,
                    'docfusion_data' => $returnedData->GenerationLog,
                    'signiflow_data' => $returnedData->ReturnData
                ]);

            } catch (\GuzzleHttp\Exception\BadResponseException $e) {
                if ($e->getCode() === 400) {
                    return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.', 'server_message' => $e->getMessage()]);
                } else if ($e->getCode() === 401) {
                    return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.', 'server_message' => $e->getMessage()]);
                }

                return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.']);
            }
        } else {
            $filename = $process->name;
            $ext = pathinfo($process->document, PATHINFO_EXTENSION);

            $client = Client::find($client_id);
            $client->load('referrer', 'introducer', 'business_unit');

            $templateProcessor = new TemplateProcessor(storage_path('app/templates/' . $process->document));
            $templateProcessor->setValue('date', date("Y/m/d"));
            $templateProcessor->setValue('client_name', ($client->company != null ? htmlentities($client->company) : htmlentities($client->first_name.' '.$client->last_name)));
            /*$templateProcessor->setValue(
                ['client.first_name', 'client.last_name', 'client.email', 'client.contact', 'client.company', 'client.email', 'client.id_number', 'client.company_registration_number', 'client.cif_code', 'client.business_unit','client.committee','client.project','client.trigger_type','client.case_number','client.qa_start_date','client.qa_end_date','client.instruction_date','client.assigned_date','client.completed_date','client.out_of_scope'],
                [$client->first_name, $client->last_name, $client->email, $client->contact, $client->company, $client->email, $client->id_number, $client->company_registration_number, $client->cif_code, ($client->business_unit_id > 0 ? $client->business_unit->name : ''), ($client->committee_id ? $client->committee->name : ''), ($client->project_id > 0 ? $client->project->name : ''), ($client->trigger_type_id > 0 ? $client->trigger->name : ''),$client->case_number,$client->qa_start_date,$client->qa_end_date,$client->instruction_date,$client->assigned_date,$client->completed_date,($client->out_of_scope == '1' ? 'Yes' : 'No')]
            );

            $templateProcessor->setValue(
                ['client.name'],
                [($client->company == '' ? $client->first_name.' '.$client->last_name : $client->company)]
            );

            $templateProcessor->setValue(
                ['introducer.first_name', 'introducer.last_name', 'introducer.email', 'introducer.contact'],
                isset($client->introducer) ?
                    [
                        $client->introducer->first_name,
                        $client->introducer->last_name,
                        $client->introducer->email,
                        $client->introducer->contact
                    ] :
                    ['', '', '', '']
            );*/

            $client_id = $client->id;
            $var_array = array();
            $value_array = array();

            $sections = FormSection::with(['form_section_inputs.input.data'=>function ($q) use ($client_id){
                $q->where('client_id',$client_id);
            }])->where('form_id',$client->crm_id)->get();

            foreach($sections as $section) {

                foreach ($section["form_section_inputs"] as $activity) {
                    $var = '';
                    switch ($activity['input_type']){
                        case 'App\FormInputDropdown':
                            $var = 'c'.$activity->id;
                            array_push($var_array,$var);
                            break;

                        default:
                            $var = 'c'.$activity->id;
                            array_push($var_array, $var);
                            break;
                    }

                    if (isset($activity["input"]->data) && count($activity["input"]->data) > 0) {
                        foreach ($activity["input"]->data as $value) {

                            switch ($activity['input_type']){
                                case 'App\FormInputDropdown':

                                    $data = FormInputDropdownItem::where('id',$value->form_input_dropdown_item_id)->first();
                                    if($data){
                                        array_push($value_array, $data["name"]);
                                    } else {
                                        array_push($value_array, '');
                                    }
                                    break;
                                case 'App\FormInputBoolean':
                                    $items = FormInputBooleanData::where('client_id',$client_id)->where('form_input_boolean_id',$value->form_input_boolean_id)->first();

                                    if($items){
                                        array_push($value_array, ($items->data == '0' ? 'No' : 'Yes'));
                                    } else {
                                        array_push($value_array, '');
                                    }

                                    break;
                                case 'App\FormInputText':
                                    $items = FormInputTextData::where('client_id',$client_id)->where('form_input_text_id',$value->form_input_text_id)->first();

                                    if($items){
                                        array_push($value_array, $items["data"]);
                                    } else {
                                        array_push($value_array, '');
                                    }

                                    break;
                                case 'App\FormInputTextarea':
                                    $items = FormInputTextareaData::where('client_id',$client_id)->where('form_input_textarea_id',$value->form_input_textarea_id)->first();

                                    if($items){
                                        array_push($value_array, $items["data"]);
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

                        switch ($activity['input_type']){
                            default:

                                array_push($value_array, '');
                                break;
                        }
                    }
                }
            }

            $steps = Step::with(['activities.actionable.data'=>function ($q) use ($client_id){
                $q->where('client_id',$client_id);
            }])->where('process_id',$process_id)->get();

            foreach($steps as $step) {

                foreach ($step["activities"] as $activity) {
                    $var = '';
                    switch ($activity['actionable_type']){
                        case 'App\ActionableDropdown':
                            $var = 'f'.$activity->id;
                            array_push($var_array,$var);
                            break;

                        default:
                            $var = 'f'.$activity->id;
                            array_push($var_array, $var);
                            break;
                    }

                    if (isset($activity["actionable"]->data) && count($activity["actionable"]->data) > 0) {
                        foreach ($activity["actionable"]->data as $value) {

                            switch ($activity['actionable_type']){
                                case 'App\ActionableDropdown':

                                    $data = ActionableDropdownItem::where('id',$value->actionable_dropdown_item_id)->first();
                                    if($data){
                                        array_push($value_array, $data["name"]);
                                    } else {
                                        array_push($value_array, '');
                                    }
                                    break;
                                case 'App\ActionableBoolean':
                                    $items = ActionableBooleanData::where('client_id',$client_id)->where('actionable_boolean_id',$value->actionable_boolean_id)->first();

                                    if($items){
                                        array_push($value_array, ($items->data == '0' ? 'No' : 'Yes'));
                                    } else {
                                        array_push($value_array, '');
                                    }

                                    break;
                                default:

                                    array_push($value_array, str_replace('<p>','',str_replace('</p>','',$value->data)));
                                    break;
                            }
                        }
                    } else {

                        switch ($activity['actionable_type']){
                            default:

                                array_push($value_array, '');
                                break;
                        }
                    }
                }
            }
//dd($var_array);
            $templateProcessor->setValue(
                $var_array,$value_array
            );

            //Create directory to store processed templates, for future reference or to check what was sent to the client
            $processed_template_path = 'processed_applications';
            if (!File::exists(public_path('storage/documents/' . $processed_template_path))) {
                Storage::makeDirectory(public_path('storage/documents/' . $processed_template_path));
            }

            $filename = $client->id . "_" . date("Y_m_d_H_i_s") . "_" . uniqid();

            $newfilename = $filename . ".docx";
            $newfilename2 = $filename . ".pdf";

            $processed_template = $processed_template_path . DIRECTORY_SEPARATOR . $newfilename;

            $processed_template_pdf = $processed_template_path . DIRECTORY_SEPARATOR . $newfilename2;

            if(File::exists(public_path('storage/documents/' . $processed_template_pdf))){
                Storage::delete(public_path('storage/documents/' . $processed_template_pdf));
            }
            $templateProcessor->saveAs(public_path('storage/documents/' . $processed_template));
            //dd(storage_path('app/forms/' .$processed_template));
            shell_exec('sudo libreoffice --headless --convert-to pdf '.public_path('storage/documents/' .$processed_template).' --outdir '.public_path('storage/documents/' . $processed_template_path));

            $document = new Document();
            $document->name = $process->name;
            $document->file = '/'.$newfilename2;
            $document->user_id = $user_id;
            $document->client_id = $client->id;
            $document->save();

            $success = 1;

            return response()->json([
                'http_code' => 200,
                'success' => $success
            ]);
        }
    }
}
