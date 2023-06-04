<?php

namespace App\Http\Controllers;

use App\ClientProcess;
use App\Document;
use App\FormInputBooleanData;
use App\FormInputDropdownItem;
use App\HelperFunction;
use App\OfficeUser;
use App\Process;
use App\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Client;
use App\Step;
use App\ActionableBooleanData;
use App\ActionableDateData;
use App\ActionableDropdownData;
use App\ActionableDropdownItem;
use App\ActionableMultipleAttachment;
use App\ActionableMultipleAttachmentData;
use App\ActionableNotificationData;
use App\ActionableDocumentData;
use App\ActionableTextData;
use App\ActionableTextareaData;
use App\ActionableTemplateEmail;
use App\ActionableTemplateEmailData;
use App\ActionableDocumentEmailData;
use App\ActionActivities;
use App\Actions;
use App\ActionsAssigned;
use App\Activity;
use App\ActivityComment;
use App\ActivityInClientBasket;
use App\ActivityLog;
use App\Forms;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class APIController extends Controller
{
    /**
     * Authenticates via OAUTH to the API, sends a JSON string to it, then parses the result and
     * returns a PDF download.
     *
     * @param Request $request
     * @return Illuminate\Http\Response
     */
    public function index(Request $request, $clientId,$process_id) {

        // OAuth2 Token exchange
        $auth = $this->authorise();

        // Get the Client details from the DB, in the format expected by the API endpoint
        $input = $this->getSourceData($clientId,$process_id);

        // Get the document
        // $input = json_decode(file_get_contents('goshen16.json'));
        $apiReturn = json_decode($this->getDocument($auth,$process_id, json_encode($input)));
        // $apiReturn = json_decode($this->getDocument($auth,$process_id, $input));

        // Temporarily spit the JSON to disk
        file_put_contents('goshen15.json', json_encode($input));

        $base = $apiReturn;

        $process = Process::where('id',$process_id)->first();

        $dateTime = date('m-d Hi');
        $filename = preg_replace("/[^a-zA-Z]/", "", $process->name).'-'.$dateTime.'.pdf';

        if($base->DocumentData != null){
            ClientProcess::where('client_id',$clientId)->where('process_id',$process_id)->update([ 'completed_at' => now() ]);
        }

        if (!File::exists(public_path('storage/documents/processed_applications/' . $clientId))) {
            Storage::disk('public')->makeDirectory('documents/processed_applications/' . $clientId);
        }

        $file = public_path('storage/documents/processed_applications/' . $clientId."/".$filename);
        $processed_template = 'documents/processed_applications/' . $clientId."/".$filename;
        if(File::exists(public_path('storage/' . $processed_template))){
            Storage::delete('storage/' . $processed_template);
        }

        // Write the processed file away
        $file2 = file_put_contents($file, base64_decode($base->DocumentData));

        $document = new Document();
        $document->name = str_replace('.pdf', '', $filename);
        $document->file = explode('processed_applications', $file)[1];
        $document->user_id = auth()->id();
        $document->client_id = $clientId;
        $document->save();

        $headers = array(
            'Content-Type: application/pdf',
        );

        if($base->DocumentData != null){
            ClientProcess::where('client_id',$clientId)->where('process_id',$process_id)->update([ 'completed_at' => now() ]);
        }

        //return response()->json(json_encode($apiReturn->ReturnData));
         return response()->json($filename);

    }

    /**
     * Do the needed database queries to retrieve the data to be passed on to the API
     *
     * @return array
     */
    private function getSourceData( int $clientId,int $process_id ) {
        // Load the client
        $client = Client::find($clientId);

        $step = Step::withTrashed()->find($client->step_id);

        $client->load('referrer', 'office.area.region.division', 'users', 'comments.user','consultant');

        $steps = Step::where('process_id', $process_id)->with(['activities.actionable.data'=>function ($q) use ($clientId){
            $q->where('client_id',$clientId);
        }])->orderBy('order', 'asc')->get();

        $csvOutput = [];

        // Load the client details names
        $form = Forms::find(2);
        $forms = $form->getClientDetailsInputValues($client->id, $form->id);

        $client_array = [];
        foreach($forms as $formId => $form) {

            foreach($form as $sectionName => $input){
                foreach($input as $inputArr) {
                    foreach($inputArr["inputs"] as $values_)  {
                        if(isset($values_["value"])){
                            switch ($values_['type']) {
                                case 'dropdown':
                                    $data = FormInputDropdownItem::where('id', $values_["value"])->first();

                                    if ($data) {
                                        $client_array [] = [$client->id, $sectionName, $values_['id'], $values_['name'], $data["name"]];
                                    }
                                    break;
                                case 'boolean':
                                    $client_array [] = [$client->id, $sectionName, $values_['id'], $values_['name'], ($values_['value'] == '0' ? 'No' : 'Yes')];
                                    break;
                                case 'date':
                                    $client_array [] = [$client->id, $sectionName, $values_['id'], $values_['name'], date('Ymd', strtotime($values_['value']))];
                                    break;
                                case 'textarea':
                                    // If field is text area and has a table, break the table up and extract values from the table,
                                    // else just take the field as it is and strip tags as well as remove line breaks, so it does break the docFusion signature
                                    if (strpos($values_['value'], '</table>') !== false) {
                                        $domLine = new \DOMDocument();
                                        $domLine->loadHTML(trim($values_['value']));
                                        $detail = $domLine->getElementsByTagName('td');

                                        $tableArray = [];
                                        $count = 0;
                                        foreach ($detail as $nodeDetail) {
                                            if (!trim($nodeDetail->textContent) == "") {
                                                $tableArray['f' . $values_['id'] . "_" . $count] = trim($nodeDetail->textContent);
                                                $count++;
                                            }
                                        }

                                        $client_array [] = [$client->id, $sectionName, $values_['id'], $values_['name'], $tableArray];
                                    } else {
                                        $values_['value'] = preg_replace("/\r|\n/", "", $values_['value']);
                                        $client_array [] = [$client->id, $sectionName, $values_['id'], $values_['name'], strip_tags($values_['value'])];
                                    }
                                    break;
                                default:
                                    $client_array [] = [$client->id, $sectionName, $values_['id'], $values_['name'], (isset($values_['value']) ? $values_['value'] : '')];
                                    break;
                            }
                        } else {
                            $client_array [] = [$client->id, $sectionName, $values_['id'], $values_['name'], ''];
                        }
                    }
                }
            }
        }

        $value_array = $client_array;
        foreach($steps as $step) {

            foreach($step->activities as $activity) {

                if (isset($activity["actionable"]->data) && count($activity["actionable"]->data) > 0 ) {

                    foreach ($activity->actionable->data as $value) {
                        switch ($activity['actionable_type']){
                            case 'App\ActionableDropdown':
                                $data = ActionableDropdownItem::where('id',$value->actionable_dropdown_item_id)->first();

                                if($data){
                                    $value_array []= [$value->client_id, $step->name, $activity->id, $activity->name, $data["name"],($step->signature == '1' ? '1' : '0')];
                                }
                                break;
                            case 'App\ActionableBoolean':
                                $items = ActionableBooleanData::where('client_id',$clientId)->where('actionable_boolean_id',$value->actionable_boolean_id)->first();

                                if($items){
                                    $value_array[]= [$value->client_id, $step->name, $activity->id, $activity->name, ($items->data == '0' ? 'No' : 'Yes'),($step->signature == 1 ? '1' : '0')];
                                }
                                break;
                            case 'App\ActionableDate':
                                $value_array[]= [$value->client_id, $step->name, $activity->id, $activity->name, date('Ymd', strtotime($value->data)),($step->signature == 1 ? '1' : '0')];
                                break;
                            case 'App\ActionableTextarea':
                                // If field is text area and has a table, break the table up and extract values from the table,
                                // else just take the field as it is and strip tags as well as remove line breaks, so it does break the docFusion signature
                                if(strpos($value->data, '</table>') !== false){
                                    $domLine = new \DOMDocument();
                                    $domLine->loadHTML(trim($value->data));
                                    $detail = $domLine->getElementsByTagName('td');

                                    $tableArray = [];
                                    $count = 0;
                                    foreach($detail as $nodeDetail) {
                                        // if(! trim($nodeDetail->textContent) == "") {
                                        $tableArray['f'.$activity->id."_".$count] = trim($nodeDetail->textContent);
                                        $count++;
                                        // }
                                    }

                                    $value_array[]= [$value->client_id, $step->name, $activity->id, $activity->name, $tableArray,($step->signature == 1 ? '1' : '0')];
                                } else{
                                    $value->data = preg_replace( "/\r|\n/", "", $value->data );
                                    $value_array[] = [$value->client_id, $step->name, $activity->id, $activity->name, strip_tags($value->data),($step->signature == 1 ? '1' : '0')];
                                }
                                break;
                            default:

                                $value_array[] = [$value->client_id, $step->name, $activity->id, $activity->name, $value->data,($step->signature == '1' ? '1' : '0')];
                                break;
                        }
                    }
                }
            }
        }

        //FA Details

        $forms = Forms::with('sections.form_section_input')
            ->where('id', 3)
            ->get();

        $input_types = array();
        $final_collection = array();
        $office_users = OfficeUser::where('office_id', auth()->user()->offices()->first()->id)
            ->get(['user_id'])->map(function($user){
                return $user->user_id;
            })->toArray();

        $helper = new HelperFunction();

        foreach ($forms[0]->sections as $section){
            $input_types = [
                'id' => $section->id,
                'name' => $section->name,
                'inputs' => []
            ];

            foreach ($section->form_section_input as $input){

                $input_type_id = $helper->formatToTableColumnName($input);
                $data = app($input["input_type"]."Data")
                    ->where($input_type_id, $input["id"])
                    ->whereIn('user_id', $office_users)
                    ->where('client_id', 0)
                    ->latest()
                    ->first(['id', 'data']);

                if (isset($data) && request()->edit){
                    array_push($input_types["inputs"], [
                        'id' => $input->id,
                        'label' => $input->name,
                        'input_type' => $input->input_type,
                        'data' => $data->data??null,
                        'data_id' => $data->id
                    ]);
                }
            }
            array_push($final_collection,$input_types);
        }

        foreach($value_array as $value) {
            $csvOutput []= $value;

        };

        $string = "";
        $returnArray = [];

        $returnArray['last_name'] = $client->last_name;
        $returnArray['first_name'] = $client->first_name;
        $returnArray['initials'] = $client->initials;
        $returnArray['cell'] = $client->contact;
        $returnArray['email'] = $client->email;
        $returnArray['id_number'] = $client->id_number;

        $signatories = [];

        foreach($csvOutput as $id => $values) {
            if(is_array($values[4])){
                foreach ($values[4] as $key1 => $value1){
                    $returnArray [$key1] = $value1;
                    $string .= $key1 ."|". $values['1'] ."|". $values['3'] ."|". json_encode($value1) . PHP_EOL;
                    $returnArray [$key1] = $value1;
                }
            }
            else {
                // Dynamically build up signatory data
                if(isset($values['5']) && $values['5'] == '1') {
                    $signatories[] = $values;

                    $string .= "s".$values['2'] ."|". $values['1'] ."|". $values['3'] ."|". json_encode($values['4']) . PHP_EOL;
                    $returnArray ['s'.$values[2]] = $values[4];
                } else {
                    $string .= "f" . $values['2'] . "|" . $values['1'] . "|" . $values['3'] . "|" . json_encode($values['4']) . PHP_EOL;
                    $returnArray ['f' . $values[2]] = $values[4];
                }
            }
        }

        // Split the signatories up
        $chunkedResults = array_chunk($signatories, 4);
        $signatoryCount = 1;

        // Do some alterations and build up the signatories sub-array
        foreach($chunkedResults as $signatory) {
            $signatoryName = $signatory[0][4];
            $signatorySurname = $signatory[1][4];
            $signatoryEmail = $signatory[2][4];
            $signatoryCellphone = $signatory[3][4];

            // Hack around the name being a single field in Flow, but Signiflow expects a split name.
            //$splitName =  explode(' ', $signatoryName);
            $returnArray['signatories']['Sig'.$signatoryCount] = ['Fieldname' => 'S00'.$signatoryCount,'FieldType' => 'Signature','Firstname' => $signatoryName,'Lastname'=> $signatorySurname,'Email' => $signatoryEmail, 'Date' => date('Y-m-d'),'Mobile' => $signatoryCellphone,'OutField' => '','Data' => []];
            $signatoryCount++;
        }
        // For testing purposes
        //$returnArray['signatories']['Sig'.$signatoryCount] = ['Fieldname' => 'S00'.$signatoryCount,'FieldType' => 'Signature','Firstname' => 'Igor','Lastname'=> 'Kolodziejczyk','Email' => 'igor@blackboardbi.com','Date' => date('Y-m-d'),'Mobile' => '08212345578','OutField' => '','Data' => []];

        // Temporarily save a CSV for the Template Designers.
        file_put_contents('goshen13.csv', $string);

        // return ['name' => 'Klaas', 'surname' => 'Rikhotso'];
        return $returnArray;
    }


    private function authorise() {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://dr.docfusion-paas.com:44331/core/connect/token?Client_ID=AttoohClient&Client_secret=@tT0o%2523357",
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

        return "Authorization: Bearer ". $decoded['access_token'];
    }

    public function getDocument( $authString,$process_id, $inputJson ) {

        $process = Process::where('id',$process_id)->first();

        $curl = curl_init();

        $data = base64_encode($inputJson);

        $postFields =   "{
            \r\n    \"Source\": \"Atooh Test\",
            \r\n    \"JobReference\": \"Atooh Hello World\",
            \r\n    \"Timeout\": \"00:01:00\",
            \r\n    \"Process\": {
            \r\n        \"ProcessType\": \"Process\",
            \r\n        \"BusinessUnitGuid\" : \"5ba96b19-5402-4ff9-8dd6-2990365eca81\",
            \r\n        \"ChainGuid\": \"72f1976c-d7de-4b0b-9e0f-576eab7753e1\",
            \r\n        \"ChainVersion\": null,
            \r\n        \"WorkflowProcessGuid\": \"".$process->docfusion_process_id."\",
            \r\n        \"WorkflowProcessVersion\": null,
            \r\n        \"TemplateGuid\" : \"".$process->docfusion_template_id."\",
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
                "$authString",
                "Content-Type: application/json"
            ),
        ));

        $curlOutput = curl_exec($curl);

        curl_close($curl);
        return $curlOutput;
    }

    /**
     * API Endpoint exposed to allow DocFusion to send periodic updates to a document
     * during the signature process.
     *
     * "DocumentId" : intValue, // this is an ID I will provide you with when you initially kick off the workflow
     * "EventType" : "", // this will specifiy and event on a document ID… for instance, “Someone Signed”, or “Signing Completed” etc…
     * "EventDate" : "", // server date and time that event was triggered
     * "User" : "", // a user reference (perhaps e-mail address)… so if you get an event that says “someone signed” the user field will tell you who it was
     * "Document" : "base64"                 //if you receive a “Signing Completed” event, this field will contain the final signed document
     *
     * @param Request $request
     * @return void
     */
    public function updateDocument(Request $request) {
        $inbound = $request->all();

        // Exit immediately should parameter count not be correct.
        if(count($inbound) != 5) {
            return response()->json(["success" => false, "Message" => "Please supply all required parameters"]);
        }

        // Instantiate a validator, append error messages to array and return json should validation fail.
        $validator = Validator::make($request->all(), [
            'DocumentId' => 'required|max:255',
            'EventType' => 'required|max:255',
            'EventDate' => 'required|max:255',
            'User' => 'required|email|max:255',
            'Document' => 'required'
        ]);

        if ($validator->passes()) {
            return response()->json( ['success'=> true, 'errors' => ""]);
        }

        return response()->json(['success'=> false, 'errors' => $validator->errors()]);

    }
}