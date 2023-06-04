<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class BureauController extends Controller
{
    /**
     * Login to CPB and get token.
     *
     * @return $token
     */
    public function login()
    {
        try {
            $http = new \GuzzleHttp\Client;

            /*$tokenResponse = $http->post('https://attoohtestapi.bureauhouse.co.za/token/token?verify=', [
                'form_params' => [
                    'AccountNumber' => env('CBP_ACCOUNT_NUMBER'),
                    'UserCode' => env('CBP_USER_CODE'),
                    'BureauName' => env('CBP_BUREAU_NAME'),
                    'Password' => env('CBP_PASSWORD'),
                    'CallingModule' => 'Integration'
                ]
            ]);*/

            $tokenResponse = $http->post('https://attoohapi.bureauhouse.co.za/token/token?verify=', [
                'form_params' => [
                    'AccountNumber' => '300107',
                    'UserCode' => 'T_3001070002',
                    'BureauName' => 'APITEST',
                    'Password' => '4BCPWPDEH',
                    'CallingModule' => 'Integration'
                ]
            ]);

            /*$tokenResponse = $http->post('https://attoohapi.bureauhouse.co.za/token/token?verify=', [
                'form_params' => [
                    'AccountNumber' => '902672',
                    'UserCode' => '9026720002',
                    'BureauName' => 'CPB',
                    'Password' => 'PCT33KG88',
                    'CallingModule' => 'Integration'
                ]
            ]);*/

            return (string)$tokenResponse->getBody();

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.']);
            } else if($e->getCode() === 401) {
                return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.']);
            }

            return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.']);
        }
    }

    public function testlogin()
    {
        return $this->login();
    }

    /**
     * Confirm KYC Address.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function confirmKYCAddress(Request $request)
    {

        $client_id = isset($request['client_id']) ? $request['client_id'] : $request->input('client_id');

        $client = Client::select('id', 'first_name', 'last_name', 'initials', 'id_number')->where('id', $client_id)->first();

        $activities = Activity::orderBy('id')->whereIN('id', [802, 803, 804, 805, 806, 807])->get();
        // Get the Address input fields
        $form_section_inputs_ids = FormSectionInputs::whereIN('id', [65, 66, 67, 68, 69, 70, 71, 178])->pluck('input_id')->toArray();

        $form_inputs_text_data = [];
        $unit_number = '';
        $complex = '';
        $street_number = '';
        $street_name = '';
        $surburb = '';
        $city = '';
        $region = '';
        $code = '';
        $counter = 0;
        foreach ($form_section_inputs_ids as $form_section_inputs_id)
        {
            $form_inputs_text_data[$counter] = FormInputTextData::select('id', 'data', 'form_input_text_id')->where('form_input_text_id', $form_section_inputs_id)->where('client_id', $client->id)->orderBy('id', 'desc')->first();

            if(isset($form_inputs_text_data[$counter]->form_input_text_id)) {
                switch ($form_inputs_text_data[$counter]->form_input_text_id) {
                    case 22: // Suite/Unit Number - form_input_text_id = 22
                        $unit_number = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 23: // Complex Name - form_input_text_id = 23
                        $complex = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 24: // Street Number - form_input_text_id = 24
                        $street_number = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 25: // Street Name - form_input_text_id = 25
                        $street_name = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 26: // Suburb - form_input_text_id = 26
                        $suburb = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 27: // City - form_input_text_id = 27
                        $city = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 28: // Region - form_input_text_id = 28
                        $region = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 101: // Code - form_input_text_id = 101
                        $code = trim($form_inputs_text_data[$counter]->data);
                        break;
                }
            }

            $counter++;
        }
        // dd($unit_number.' - '.$complex.' - '.$street_number.' - '.$street_name.' - '.$suburb.' - '.$city.' - '.$code);

        $token = $this->login();

        try {

            $http = new \GuzzleHttp\Client;

            $response = $http->post('https://attoohtestapi.bureauhouse.co.za/address/kyc?verify=', [
                'form_params' => [
                    'Token' => $token,
                    'IDNumber' => $client->id_number, // $client->id_number,
                    'Reference' => '',
                    // 'InputAddress' => '{"Line1":"129","Line2":"stenostelma","Line3":"Pretoria","Line4":"","Line5":"","PostCode":"0182"}',
                    'InputAddress' => '{"Line1":"'.$unit_number.'","Line2":"'.$complex.'","Line3":"'.$street_number.' '.$street_name.'","Line4":"'.$suburb.'","Line5":"'.$city.'","PostCode":"'.$code.'"}'
                ]
            ]);

            $response = json_decode($response->getBody());
            $addresses = $response->AddressCompare;

            $matchAddress = null;
            $counter = 0;

            $dateMinusThreeMonths = date("Y-m-d", strtotime(date('Y-m-d')." -3 months"));
            $addressFoundFlag = false;
            $addressWithinThreeMonthsFlag = false;
            $message = 'No address matching the provided address found.';

            foreach ($addresses as $address)
            {
                if($address->MatchType == 'Fuzzy Only') {
                    $addressFoundFlag = true;
                    if($dateMinusThreeMonths < date('Y-m-d', strtotime($address->Address2->RecordDate))) {
                        $addressWithinThreeMonthsFlag = true;
                        $matchAddress[$counter]['Address'] = $address->Address2;
                        $matchAddress[$counter]['FullAddress'] = $address->Address2->Address . ', ' . $address->Address2->Suburb . ', ' . $address->Address2->Town;
                        $matchAddress[$counter]['FullSource'] = $address->Address2->FullSource;
                        $matchAddress[$counter]['RecordDate'] = $address->Address2->RecordDate;
                        $matchAddress[$counter]['dateMinusThreeMonths'] = $dateMinusThreeMonths;
                        $counter++;
                        break;
                    }
                }
            }

            if($addressFoundFlag){
                $message = 'Matching address found. Address date is older than 3 months.';
                if($addressWithinThreeMonthsFlag){
                    $message = 'Matching address found. Address is withing 3 months';
                }
            }

            return response()->json(['Address' => $matchAddress, 'message' => $message]);
            // return $addresses->AddressCompare;

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.']);
            } else if($e->getCode() === 401) {
                return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.']);
            }

            return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.']);
        }
    }

    /**
     * Confirm KYC Address.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function confirmIndividualKYCAddress(Request $request)
    {

        $client_id = isset($request['client_id']) ? $request['client_id'] : $request->input('client_id');

        $client = Client::select('id', 'first_name', 'last_name', 'initials', 'id_number')->where('id', $client_id)->first();

        // Get the Address input fields
        $form_section_inputs_ids = FormSectionInputs::whereIN('id', [65, 66, 67, 68, 69, 70, 71, 178])->pluck('input_id')->toArray();

        $form_inputs_text_data = [];
        $unit_number = '';
        $complex = '';
        $street_number = '';
        $street_name = '';
        $suburb = '';
        $city = '';
        $region = '';
        $code = '';
        $counter = 0;
        foreach ($form_section_inputs_ids as $form_section_inputs_id)
        {
            $form_inputs_text_data[$counter] = FormInputTextData::select('id', 'data', 'form_input_text_id')->where('form_input_text_id', $form_section_inputs_id)->where('client_id', $client->id)->orderBy('id', 'desc')->first();

            if(isset($form_inputs_text_data[$counter]->form_input_text_id)) {
                switch ($form_inputs_text_data[$counter]->form_input_text_id) {
                    case 22: // Suite/Unit Number - form_input_text_id = 22
                        $unit_number = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 23: // Complex Name - form_input_text_id = 23
                        $complex = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 24: // Street Number - form_input_text_id = 24
                        $street_number = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 25: // Street Name - form_input_text_id = 25
                        $street_name = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 26: // Suburb - form_input_text_id = 26
                        $suburb = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 27: // City - form_input_text_id = 27
                        $city = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 28: // Region - form_input_text_id = 28
                        $region = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 101: // Code - form_input_text_id = 101
                        $code = trim($form_inputs_text_data[$counter]->data);
                        break;
                }
            }

            $counter++;
        }

        if($street_name == ''){
            return response()->json(['message' => "Please provide the client's street name"]);
        }

        if($suburb == ''){
            return response()->json(['message' => "Please provide the client's suburb"]);
        }

        if($city == ''){
            return response()->json(['message' => "Please provide the client's city"]);
        }

        /*if($region == ''){
            return response()->json(['message' => "Please provide the client's region"]);
        }*/

        if($code == ''){
            return response()->json(['message' => "Please provide the client's code"]);
        }

        $token = $this->login();

        try {

            $http = new \GuzzleHttp\Client;

            $response = $http->post('https://attoohtestapi.bureauhouse.co.za/wrapper/kyc?verify=', [
                'form_params' => [
                    'Token' => $token,
                    'IDNumber' => $client->id_number, // $client->id_number,
                    'Surname' => '',
                    'KYC' => 'YES',
                    'UseDHAExtra' => 'No',
                    'Reference' => '',
                    'InputEmployer' => '',
                    'InputEmail' => '',
                    'InputAddress' => '{"Line1":"'.$unit_number.'","Line2":"'.$complex.'","Line3":"'.$street_number.' '.$street_name.'","Line4":"'.$suburb.'","Line5":"'.$city.'","PostCode":"'.$code.'"}',
                    'InputTelephone' => '{"TelNumber":""}',
                    'InputPerson' => '{"IDNumber":"'.$client->id_number.'","Surname":"","FirstName":"","SecondName":"","DateOfBirth":""}',
                    'PeopleTemplate' => '{"TemplateSupplied":"Y","IDCDVMustBeCorrect":"Y","FailOnMinor":"Y","FailOnDeceased":"Y","FailOnSAFPS":"Y","GoldenSourceCount":"1","OtherSourceCount":"2","MinSourceCount":"1","GoldenSourceMinMonths":"12","OtherSourceMinMonths":"6","GoldenSourceMinSurnameScore":"100","OtherSourceMinSurnameScore":"90","GoldenSourceMinFirstNameScore":"90","OtherSourceMinFirstNameScore":"60","GoldenSourceMinSecondNameScore":"60","OtherSourceMinSecondNameScore":"60","AllowMaidenName":"Y","AllowInitials":"Y","AllowForeNamesToSwitch":"Y","UseDHAExtra":"No","ExcludeSources":"","IncludeSources":"","MaxRecordsToReturn":"3"}',
                    'AddressTemplate' => '{"TemplateSupplied":"Y","MatchPrimarySourceOnly":"N","RecordsMaxMonths":"0","GoldenSourceCount":"1","OtherSourceCount":"1","MinSourceCount":"2","GoldenSourceMinMonths":"12","GoldenSourceMinScore":"70","OtherSourceMinMonths":"6","OtherSourceMinScore":"70","AllowPostal":"N","ComplexNumberMustMatch":"Y","ComplexNameMustMatch":"Y","StreetNumberMustMatch":"Y","StreetNameMustMatch":"Y","SuburbMustMatch":"Y","AllowInformal":"Y","ExcludeSources":"","IncludeSources":"","MaxRecordsToReturn":"3"}',
                    'TelephoneTemplate' => '{"TemplateSupplied":"Y","GoldenSourceCount":"1","OtherSourceCount":"1","MinSourceCount":"1","GoldenSourceMinMonths":"60","OtherSourceMinMonths":"60","GoldenSourceMinScore":"100","OtherSourceMinScore":"100","ExcludeSources":"","IncludeSources":"","MaxRecordsToReturn":"1"}'
                ]
            ]);

            // return $response->getBody();

            $response = json_decode($response->getBody());

            if($response->http_code != 200){
                return response()->json(['http_code' => $response->http_code, 'status_message' => $response->status_message]);
            }

            // return $response;
            $addresses = $response->AddressResults->AddressCompare;
            $encodedPDF = $response->EncodedPDF;

            $matchAddress = null;
            $counter = 0;
            $addressFoundFlag = false;
            $addressWithinThreeMonthsFlag = false;

            $dateMinusThreeMonths = date("Y-m-d", strtotime(date('Y-m-d')." -3 months"));
            foreach ($addresses as $address)
            {
                if($address->MatchType == 'Fuzzy Only' || $address->MatchType == 'Clean') {
                    $addressFoundFlag = true;
                    if($dateMinusThreeMonths < date('Y-m-d', strtotime($address->Address2->RecordDate))) {
                        $addressWithinThreeMonthsFlag = true;
                        $matchAddress[$counter]['Address'] = $address->Address2;
                        $matchAddress[$counter]['FullAddress'] = $address->Address2->Address . ', ' . $address->Address2->Suburb . ', ' . $address->Address2->Town;
                        $matchAddress[$counter]['FullSource'] = $address->Address2->FullSource;
                        $matchAddress[$counter]['RecordDate'] = $address->Address2->RecordDate;
                        $matchAddress[$counter]['dateMinusThreeMonths'] = $dateMinusThreeMonths;
                        $counter++;
                        break;
                    }
                }
            }

            $file_path = "";
            $result = 0;

            $base64string = $encodedPDF;
            // Convert blob (base64 string) back to PDF
            if (!empty($encodedPDF)) {

                // Detects if there is base64 encoding header in the string.
                // If so, it needs to be removed prior to saving the content to a phisical file.
                if (strpos($base64string, ',') !== false) {
                    @list($encode, $base64string) = explode(',', $base64string);
                }

                $folder = 'documents';
                $folder = '';
                $file_name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".pdf";

                $base64data = base64_decode($base64string, true);
                $file_path  = "{$folder}/{$file_name}";

                $result = file_put_contents(storage_path('app/documents').$file_path, $base64data);

                $document = new Document();
                $document->name = 'CPB - Address KYC';
                $document->file = $file_path;
                $document->user_id = 1;
                $document->client_id = $client->id;
                $document->save();
            }

            $message = 'The address provided could not be verified.';
            if($addressFoundFlag){
                $message = 'Matching address found.<br><br> Address date is older than 3 months.';
                if($addressWithinThreeMonthsFlag){
                    $message = 'Matching address found.<br><br> Address is withing 3 months.';
                }
            }

            $message .= "<br><br> Click <a href='/storage/document?q={$file_path}' target='_blank' style='color: red !important;'>here</a> to view the KYC certificate or you can find the certifacate under client's documents.";

            return response()->json(['http_code' => $response->http_code, 'Address' => $matchAddress, 'message' => $message]);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.'/*.' '.$e->getMessage()*/]/*, $e->getCode()*/);
            } else if($e->getCode() === 401) {
                return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.'/*.' '.$e->getMessage()*/]/*, $e->getCode()*/);
            }

            return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.'/*.' '.$e->getMessage()*/]/*, $e->getCode()*/);
        }
    }

    /**
     * Confirm KYC Address.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function confirmIDV(Request $request)
    {
        $client_id = isset($request['client_id']) ? $request['client_id'] : $request->input('client_id');

        $client = Client::select('id', 'first_name', 'last_name', 'initials', 'id_number')->where('id', $client_id)->first();

        $token = $this->login();

        try {

            $http = new \GuzzleHttp\Client;

            $response = $http->post('https://attoohtestapi.bureauhouse.co.za/wrapper/idvlistpdf?verify=', [
                'form_params' => [
                    'Token' => $token,
                    'Reference' => 'test',
                    'IDNumber' => $client->id_number,
                    'hasConsent' => 'Yes',
                    'EnquiryReason' => 'Test',
                    'EnquiryType' => 'idvalidationphoto'
                ]
            ]);

            $response = json_decode($response->getBody());

            if($response->http_code != 200){
                return response()->json(['http_code' => $response->http_code, 'status_message' => $response->status_message]);
            }

            $encodedPDF = $response->EncodedPDF;

            $file_path = "";

            $base64string = $encodedPDF;
            // Convert blob (base64 string) back to PDF
            if (!empty($encodedPDF)) {

                // Detects if there is base64 encoding header in the string.
                // If so, it needs to be removed prior to saving the content to a phisical file.
                if (strpos($base64string, ',') !== false) {
                    @list($encode, $base64string) = explode(',', $base64string);
                }

                $folder = '';
                $file_name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".pdf";

                $base64data = base64_decode($base64string, true);
                $file_path  = "{$folder}/{$file_name}";

                $result = file_put_contents(storage_path('app/documents').$file_path, $base64data);

                $document = new Document();
                $document->name = 'CPB - IDV';
                $document->file = $file_path;
                $document->user_id = 1;
                $document->client_id = $client->id;
                $document->save();
            }

            $message = 'IDV was verified successfully.';

            $message .= "<br><br> Click <a href='/storage/document?q={$file_path}' target='_blank' style='color: red !important;'>here</a> to view the IDV document or you can find the document under client's documents.";

            return response()->json(['http_code' => $response->http_code, 'Record' => '', 'message' => $message]);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.']);
            } else if($e->getCode() === 401) {
                return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.']);
            }

            return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.'.$e->getMessage()]);
        }
    }

    /**
     * Get proof of Address.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getProofOfAddress(Request $request)
    {
        $client_id = isset($request['client_id']) ? $request['client_id'] : $request->input('client_id');

        $client = Client::select('id', 'first_name', 'last_name', 'initials', 'id_number')->where('id', $client_id)->first();

        $token = $this->login();

        try {

            $http = new \GuzzleHttp\Client;

            $response = $http->post('https://attoohtestapi.bureauhouse.co.za/wrapper/proofofaddress?verify=', [
                'form_params' => [
                    'Token' => $token,
                    'IDNumber' => $client->id_number,
                    'Reference' => ''
                ]
            ]);

            $response = json_decode($response->getBody());

            if($response->http_code != 200){
                return response()->json(['http_code' => $response->http_code, 'status_message' => $response->status_message]);
            }

            $encodedPDF = $response->Results[0]->EncodedPDF;
            $address = isset($response->Results[0]->ResidentialAddress_Line1) ? $response->Results[0]->ResidentialAddress_Line1 : '';
            $address .= ' ' . isset($response->Results[0]->ResidentialAddress_Line2) ? $response->Results[0]->ResidentialAddress_Line2 : '';
            $address .= ' ' . isset($response->Results[0]->ResidentialAddress_Line3) ? $response->Results[0]->ResidentialAddress_Line3 : '';
            $address .= ' ' . isset($response->Results[0]->ResidentialAddress_Line4) ? $response->Results[0]->ResidentialAddress_Line4 : '';
            $address .= ' ' . isset($response->Results[0]->ResidentialAddress_PostCode) ? $response->Results[0]->ResidentialAddress_PostCode : '';

            $file_path = "";

            $base64string = $encodedPDF;
            // Convert blob (base64 string) back to PDF
            if (!empty($encodedPDF)) {

                // Detects if there is base64 encoding header in the string.
                // If so, it needs to be removed prior to saving the content to a phisical file.
                if (strpos($base64string, ',') !== false) {
                    @list($encode, $base64string) = explode(',', $base64string);
                }

                $folder = 'documents';
                $folder = '';
                $file_name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".pdf";

                $base64data = base64_decode($base64string, true);
                $file_path  = "{$folder}/{$file_name}";

                $result = file_put_contents(storage_path('app/documents').$file_path, $base64data);

                $document = new Document();
                $document->name = 'CPB - Proof of Address';
                $document->file = $file_path;
                $document->user_id = 1;
                $document->client_id = $client->id;
                $document->save();
            }

            $message = 'Proof of residence generated successfully.';

            $message .= "<br><br> Click <a href='/storage/document?q={$file_path}' target='_blank' style='color: red !important;'>here</a> to view the proof of residence or you can find the proof of residence under client's documents.";

            return response()->json(['http_code' => $response->http_code, 'Address' => $address, 'message' => $message]);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.'/*.' '.$e->getMessage()*/]/*, $e->getCode()*/);
            } else if($e->getCode() === 401) {
                return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.'/*.' '.$e->getMessage()*/]/*, $e->getCode()*/);
            }

            return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.'/*.' '.$e->getMessage()*/]/*, $e->getCode()*/);
        }
    }

    /**
     * Get IDV from cpblist
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getIDVList($client_id)
    {
        $client = Client::select('id', 'first_name', 'last_name', 'initials', 'id_number')->where('id', $client_id)->first();

        $token = $this->login();

        try {

            $http = new \GuzzleHttp\Client;

            $response = $http->post('https://attoohtestapi.bureauhouse.co.za/idv/list?verify=', [
                'form_params' => [
                    'Token' => $token,
                    'IDNumber' => $client->id_number,
                    'Reference' => ''
                ]
            ]);

            $response = json_decode($response->getBody());

            if($response->http_code != 200){
                return response()->json(['http_code' => $response->http_code, 'status_message' => $response->status_message]);
            }

            $message = 'ID profile completed successfully.';

            return response()->json(['http_code' => $response->http_code, 'Record' => $response, 'message' => $message]);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.']);
            } else if($e->getCode() === 401) {
                return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.']);
            }

            return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.']);
        }
    }

    /**
     * Get client address from cpblist
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getAddressList($client_id)
    {
        $client = Client::select('id', 'first_name', 'last_name', 'initials', 'id_number')->where('id', $client_id)->first();

        $token = $this->login();

        try {

            $http = new \GuzzleHttp\Client;

            $response = $http->post('https://attoohtestapi.bureauhouse.co.za/address/list?verify=', [
                'form_params' => [
                    'Token' => $token,
                    'IDNumber' => $client->id_number,
                    'Reference' => ''
                ]
            ]);

            $response = json_decode($response->getBody());

            if($response->http_code != 200){
                return response()->json(['http_code' => $response->http_code, 'status_message' => $response->status_message]);
            }

            $recordedRecordDate = null;
            $latestAddress = null;
            foreach($response->OutputAddresses as $key => $address){
                $recordDate = explode('-', $address->RecordDate);
                if($recordedRecordDate == null){
                    $recordedRecordDate = $address->RecordDate;
                    $latestAddress = $address;
                }

                if(strtotime($address->RecordDate) > strtotime($recordedRecordDate)){
                    if(($address->Suburb != '') && ($address->Status != 'Unknown')) {
                        $recordedRecordDate = $address->RecordDate;
                        $latestAddress = $address;
                    }
                }
            }

            $message = 'Address completed successfully.';

            return response()->json(['http_code' => $response->http_code, 'Record' => $latestAddress, 'message' => $message]);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.']);
            } else if($e->getCode() === 401) {
                return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.']);
            }

            return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.']);
        }
    }

    /**
     * Get client employment from cpblist
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getEmploymentList($client_id)
    {
        $client = Client::select('id', 'first_name', 'last_name', 'initials', 'id_number')->where('id', $client_id)->first();

        $token = $this->login();

        try {

            $http = new \GuzzleHttp\Client;

            $response = $http->post('https://attoohtestapi.bureauhouse.co.za/employment/list?verify=', [
                'form_params' => [
                    'Token' => $token,
                    'IDNumber' => $client->id_number,
                    'Reference' => ''
                ]
            ]);

            $response = json_decode($response->getBody());

            if($response->http_code != 200){
                return response()->json(['http_code' => $response->http_code, 'status_message' => $response->status_message]);
            }

            $recordedRecordDate = null;
            $latestEmployment = null;
            foreach($response->Employers as $key => $employer){
                if($recordedRecordDate == null){
                    $recordedRecordDate = $employer->RecordDate;
                    $latestEmployment = $employer;
                }

                if(strtotime($employer->RecordDate) > strtotime($recordedRecordDate)){
                    $recordedRecordDate = $employer->RecordDate;
                    $latestEmployment = $employer;
                }
            }

            $message = 'Employer completed successfully.';

            return response()->json(['http_code' => $response->http_code, 'Record' => $latestEmployment, 'message' => $message]);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.']);
            } else if($e->getCode() === 401) {
                return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.']);
            }

            return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.']);
        }
    }

    /**
     * Get client telephone from cpblist
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getTelephoneList($client_id)
    {
        $client = Client::select('id', 'first_name', 'last_name', 'initials', 'id_number')->where('id', $client_id)->first();

        $token = $this->login();

        try {

            $http = new \GuzzleHttp\Client;

            $response = $http->post('https://attoohtestapi.bureauhouse.co.za/telephone/list?verify=', [
                'form_params' => [
                    'Token' => $token,
                    'IDNumber' => $client->id_number,
                    'Reference' => ''
                ]
            ]);

            $response = json_decode($response->getBody());

            if($response->http_code != 200){
                return response()->json(['http_code' => $response->http_code, 'status_message' => $response->status_message]);
            }

            $recordedRecordDate = null;
            $latestTelephone = null;
            foreach($response->Telephones as $key => $telephone){
                if($recordedRecordDate == null){
                    $recordedRecordDate = $telephone->LatestDate;
                    $latestTelephone = $telephone;
                }

                if(strtotime($telephone->LatestDate) > strtotime($recordedRecordDate)){
                    $recordedRecordDate = $telephone->LatestDate;
                    $latestTelephone = $telephone;
                }
            }

            $message = 'Telephone completed successfully.';

            return response()->json(['http_code' => $response->http_code, 'Record' => $latestTelephone, 'message' => $message]);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.']);
            } else if($e->getCode() === 401) {
                return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.']);
            }

            return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.']);
        }
    }

    /**
     * Get client email from cpblist
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getEmailList($client_id)
    {
        // $client_id = isset($request['client_id']) ? $request['client_id'] : $request->input('client_id');

        $client = Client::select('id', 'first_name', 'last_name', 'initials', 'id_number')->where('id', $client_id)->first();

        $token = $this->login();

        try {

            $http = new \GuzzleHttp\Client;

            $response = $http->post('https://attoohtestapi.bureauhouse.co.za/emailaddress/list?verify=', [
                'form_params' => [
                    'Token' => $token,
                    'IDNumber' => $client->id_number,
                    'Reference' => ''
                ]
            ]);

            $response = json_decode($response->getBody());

            if($response->http_code != 200){
                return response()->json(['http_code' => $response->http_code, 'status_message' => $response->status_message]);
            }

            $recordedRecordDate = null;
            $latestEmail = null;
            foreach($response->EmailAddresses as $key => $email){
                if($recordedRecordDate == null){
                    $recordedRecordDate = $email->LatestDate;
                    $latestEmail = $email;
                }

                if(strtotime($email->LatestDate) > strtotime($recordedRecordDate)){
                    $recordedRecordDate = $email->LatestDate;
                    $latestEmail = $email;
                }
            }

            $message = 'Email completed successfully.';

            return response()->json(['http_code' => $response->http_code, 'Record' => $latestEmail, 'message' => $message]);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.']);
            } else if($e->getCode() === 401) {
                return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.']);
            }

            return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.']);
        }
    }

    /**
     * Get client email from cpblist
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getSpouseList($client_id)
    {
        $client = Client::select('id', 'first_name', 'last_name', 'initials', 'id_number')->where('id', $client_id)->first();

        $token = $this->login();

        try {

            $http = new \GuzzleHttp\Client;

            $response = $http->post('https://attoohtestapi.bureauhouse.co.za/relatives/list?verify=', [
                'form_params' => [
                    'Token' => $token,
                    'IDNumber' => $client->id_number,
                    'Reference' => ''
                ]
            ]);

            $response = json_decode($response->getBody());

            if($response->http_code != 200){
                return response()->json(['http_code' => $response->http_code, 'status_message' => $response->status_message]);
            }

            $latestSpouse = null;
            foreach($response->Relatives as $key => $spouse){
                if($spouse->LinkType == 'Marriage'){
                    $latestSpouse = $spouse;
                }
            }

            $message = 'Spouse completed successfully.';

            return response()->json(['http_code' => $response->http_code, 'Record' => $latestSpouse, 'message' => $message]);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.']);
            } else if($e->getCode() === 401) {
                return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.']);
            }

            return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.']);
        }
    }

    /**
     * Get proof of Address.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function getAVS(Request $request)
    {

        $client_id = isset($request['client_id']) ? $request['client_id'] : $request->input('client_id');

        $client = Client::select('id', 'first_name', 'last_name', 'initials', 'id_number', 'email', 'contact')->where('id', $client_id)->first();

        // Get the Account input fields
        $form_section_inputs_ids = FormSectionInputs::whereIN('id', [41, 51, 52, 53])->pluck('input_id')->toArray();

        $form_inputs_text_data = [];
        $account_holder_name = '';
        $bank_name = '';
        $branch_name = '';
        $branch_code = '';
        $account_number = '';
        $account_type = '';
        $counter = 0;
        foreach ($form_section_inputs_ids as $form_section_inputs_id)
        {
            $form_inputs_text_data[$counter] = FormInputTextData::select('id', 'data', 'form_input_text_id')->where('form_input_text_id', $form_section_inputs_id)->where('client_id', $client->id)->orderBy('id', 'desc')->first();

            if(isset($form_inputs_text_data[$counter]->form_input_text_id)) {
                switch ($form_inputs_text_data[$counter]->form_input_text_id) {
                    case 1: // Account Holder Name - form_input_text_id = 1
                        $account_holder_name = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 10: // Bank Name - form_input_text_id = 10
                        $bank_name = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 12: // Branch Code - form_input_text_id = 12
                        $branch_code = trim($form_inputs_text_data[$counter]->data);
                        break;
                    case 13: // Account Number - form_input_text_id = 13
                        $account_number = trim($form_inputs_text_data[$counter]->data);
                        break;
                }
            }

            $counter++;
        }

        // Get the Bank name input fields
        $form_section_inputs_id = FormSectionInputs::find(42);

        $form_input_drop_down_items = FormInputDropdownItem::where('form_input_dropdown_id', $form_section_inputs_id->input_id)->get();
        $form_input_drop_down_data = FormInputDropdownData::select('id', 'form_input_dropdown_id', 'form_input_dropdown_item_id')->where('form_input_dropdown_id', $form_section_inputs_id->input_id)->where('client_id', $client->id)->orderBy('id', 'desc')->first();

        if(isset($form_input_drop_down_data)) {
            foreach ($form_input_drop_down_items as $form_input_drop_down_item) {
                if ($form_input_drop_down_item->id == $form_input_drop_down_data->form_input_dropdown_item_id) {
                    // Account Type - form_input_text_id = 14
                    $bank_name = trim($form_input_drop_down_item->name);
                }
            }
        }

        // Get the Account type input field
        $form_section_inputs_id = FormSectionInputs::find(54);

        $form_input_drop_down_items = FormInputDropdownItem::where('form_input_dropdown_id', $form_section_inputs_id->input_id)->get();
        $form_input_drop_down_data = FormInputDropdownData::select('id', 'form_input_dropdown_id', 'form_input_dropdown_item_id')->where('form_input_dropdown_id', $form_section_inputs_id->input_id)->where('client_id', $client->id)->orderBy('id', 'desc')->first();

        if(isset($form_input_drop_down_data)) {
            foreach ($form_input_drop_down_items as $form_input_drop_down_item) {
                if ($form_input_drop_down_item->id == $form_input_drop_down_data->form_input_dropdown_item_id) {
                    // Account Type - form_input_text_id = 14
                    $account_type = trim($form_input_drop_down_item->name);
                }
            }
        }

        /*return $account_holder_name.' - '.$bank_name.' - '.$branch_name.' - '.$branch_code.
            ' - '.$account_number.' - '.$account_type.' - '.$client->initials.' - '.$client->first_name.
            ' - '.$client->last_name.' - '.$client->email.' - '.$client->contact;*/

        $bank_name = str_replace(' ', '', $bank_name);

        if(trim($account_type) == ''){
            return response()->json(['message' => "Please provide the client's account type"]);
        }

        if(trim($account_holder_name) == ''){
            return response()->json(['message' => "Please provide the client's account holder name"]);
        }

        if(trim($bank_name) == ''){
            return response()->json(['message' => "Please provide the client's bank name"]);
        }

        if(trim($account_number) == ''){
            return response()->json(['message' => "Please provide the client's account number"]);
        }

        if(trim($account_type) == ''){
            return response()->json(['message' => "Please provide the client's account type"]);
        }

        $token = $this->login();

        try {

            $http = new \GuzzleHttp\Client;

            $response = $http->post('https://attoohtestapi.bureauhouse.co.za/wrapper/avsreport?verify=', [
                'form_params' => [
                    'Token' => $token,
                    'REF' => '',
                    'AccountNumber' => $account_number, // '012112313',
                    'AccountType' => trim($account_type),
                    'Bank' => trim($bank_name),// 'StandardBank',
                    'Email' => trim($client->email),// 'kmrikhotso@gmail.com',
                    'PhoneNumber' => trim($client->contact), // '0848791089',
                    'Initials' => trim($client->initials), // 'KM',
                    'FirstName' => trim($client->first_name) == '' ? trim($client->first_name) : trim($account_holder_name), // 'Klaas',
                    'HasConsent' => 'true',
                    'IDNumber' => trim($client->id_number),
                    'Surname' => trim($client->last_name) == '' ? trim($client->last_name) : trim($account_holder_name), // 'Rikhotso',
                    'encodePDF' => 'true',
                ]
            ]);

            /*$response = $http->post('https://attoohtestapi.bureauhouse.co.za/wrapper/avsreport?verify=', [
                'form_params' => [
                    'Token' => $token,
                    'REF' => '',
                    'AccountNumber' => $account_number, // '012112313',
                    'AccountType' => $account_type, // 'Current',
                    'Bank' => $bank_name,// 'StandardBank',
                    'Email' => $client->email, // 'kmrikhotso@gmail.com',
                    'PhoneNumber' => $client->contact, // '0848791089',
                    'Initials' => $client->initials, // 'KM',
                    'FirstName' => $client->first_name, // 'Klaas',
                    'HasConsent' => 'true',
                    'IDNumber' => $client->id_number,
                    'Surname' => $client->last_name, // 'Rikhotso',
                    'encodePDF' => 'true',
                ]
            ]);*/

            $response = json_decode($response->getBody());

            if($response->http_code != 200){
                return response()->json(['http_code' => $response->http_code, 'status_message' => $response->status_message]);
            }

            $encodedPDF = $response->EncodedPDF;

            $file_path = "";

            $base64string = $encodedPDF;
            // Convert blob (base64 string) back to PDF
            if (!empty($encodedPDF)) {

                // Detects if there is base64 encoding header in the string.
                // If so, it needs to be removed prior to saving the content to a phisical file.
                if (strpos($base64string, ',') !== false) {
                    @list($encode, $base64string) = explode(',', $base64string);
                }

                $folder = 'documents';
                $folder = '';
                $file_name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".pdf";

                $base64data = base64_decode($base64string, true);
                $file_path  = "{$folder}/{$file_name}";

                $result = file_put_contents(storage_path('app/documents').$file_path, $base64data);

                $document = new Document();
                $document->name = 'CPB - AVS Report';
                $document->file = $file_path;
                $document->user_id = 1;
                $document->client_id = $client->id;
                $document->save();
            }

            if(isset($response->Result->avs->response_avs->accountFound) && $response->Result->avs->response_avs->accountFound == 'Y'){
                $message = 'Client account was verified successfully.';
            } else {
                $message = 'Client account could not be verified.';
            }

            $message .= "<br><br> Click <a href='/storage/document?q={$file_path}' target='_blank' style='color: red !important;'>here</a> to view the avs report or you can find the avs report under client's documents.";

            return response()->json(['http_code' => $response->http_code, 'message' => $message]);

        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json(['http_code' => 400, 'message' => 'Invalid Request. Please fix and try again.'/*.' '.$e->getMessage()*/]/*, $e->getCode()*/);
            } else if($e->getCode() === 401) {
                return response()->json(['http_code' => 401, 'message' => 'Invalid request data. Please try again.'/*.' '.$e->getMessage()*/]/*, $e->getCode()*/);
            }

            return response()->json(['http_code' => 500, 'message' => 'Something went wrong on the server.'/*.' '.$e->getMessage()*/]/*, $e->getCode()*/);
        }
    }
}