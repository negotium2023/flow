<?php

use App\ActionableBooleanData;
use App\ActionableDateData;
use App\ActionableDocumentData;
use App\ActionableDropdownData;
use App\ActionableDropdownItem;
use App\ActionableNotificationData;
use App\ActionableTemplateEmailData;
use App\ActionableTextData;
use App\Client;
use App\ClientComment;
use App\FormInputDate;
use App\FormInputDateData;
use App\FormInputDropdownData;
use App\FormInputTextData;
use App\Step;
use Illuminate\Database\Seeder;
use League\Csv\Reader;
use League\Csv\Statement;
use Carbon\Carbon;

class ClientSeeder extends Seeder
{
    public function __construct() {
        //
    }

    public function run()
    {
        /*$this->getClientFiles('Gaukes_Moster_Data_New.csv', 45);
        $this->getClientFiles('Philip_Roesch_Data_New.csv', 65);
        $this->getClientFiles('Rayno_van_Vuuren_Data_New.csv', 47);
        $this->getClientFiles('Stian_de_Witt_Data_New.csv', 43);*/
        $this->getClientFiles('latest_clients.csv');
    }

    public function getClientFiles($fileName)
    {
        $csv = Reader::createFromPath(database_path('/data/'.$fileName, 'r'));
        $csv->setDelimiter(',');
        $csv->setHeaderOffset(0);
        $stmt = (new Statement());
        $records = $stmt->process($csv);

        foreach ($records as $record_key => $record) {
            $client = new Client;
            $client->first_name = $record['first_name'];
            $client->last_name = $record['last_name'];
            $client->company = $record['company'];
            $client->email = ($record['email']);
            $client->contact = ($record['contact']);
            $client->id_number = ($record['id_number']);
            $client->introducer_id = 1;
            $client->process_id = 19;
            $client->step_id = 91;
            $client->office_id = 1;
            $client->created_at = now();
            $client->save();

            $cp = new \App\ClientProcess;
            $cp->client_id = $client->id;
            $cp->process_id = 19;
            $cp->step_id = 91;
            $cp->active = 1;
            $cp->save();

            // Cellphone Number = 378
            // $clientCellPhoneNumber = isset($record['contact']) && $record['contact'] != '' && $record['contact'] != 'NULL' ? $record['contact'] : NULL;
            // $formInputDateData = new FormInputTextData();
            // $formInputDateData->form_input_text_id = 378;
            // $formInputDateData->data = trim($clientCellPhoneNumber);
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();

            // TelNumber - Work = 20
            // $clientTelWorkNumber = isset($record['Client_TelWork']) && $record['Client_TelWork'] != '' && $record['Client_TelWork'] != 'NULL' ? $record['Client_TelWork'] : NULL;
            // $formInputDateData = new FormInputTextData();
            // $formInputDateData->form_input_text_id = 20;
            // $formInputDateData->data = trim($clientTelWorkNumber);
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();

            /*// ClientFaxHome = 377
            $ClientFaxHome = isset($record['Client_FaxHome']) && $record['Client_FaxHome'] != '' && $record['Client_FaxHome'] != 'NULL' ? $record['Client_FaxHome'] : NULL;
            $formInputDateData = new FormInputTextData();
            $formInputDateData->form_input_text_id = 377;
            $formInputDateData->data = trim($ClientFaxHome);
            $formInputDateData->client_id = $client->id;
            $formInputDateData->user_id = 1;
            $formInputDateData->duration = 120;
            $formInputDateData->save();*/

            // TelNumber - Home = 19
            // $clientTelHomeNumber = isset($record['Client_TelHome']) && $record['Client_TelHome'] != '' && $record['Client_TelHome'] != 'NULL' ? $record['Client_TelHome'] : NULL;
            // $formInputDateData = new FormInputTextData();
            // $formInputDateData->form_input_text_id = 19;
            // $formInputDateData->data = trim($clientTelHomeNumber);
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();

            // Date of Birth = 65
            // $clientDateOfBirth = isset($record['Client_DOB']) && $record['Client_DOB'] != '' && $record['Client_DOB'] != 'NULL' ? $record['Client_DOB'] : NULL;
            // $formInputDateData = new FormInputDateData();
            // $formInputDateData->form_input_date_id = 65;
            // $formInputDateData->data = date('y-m-d', strtotime($clientDateOfBirth));
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();

            // Client Address 1 - 23
            // $clientAddressLine1 = isset($record['Client_HomeAddress1']) && $record['Client_HomeAddress1'] != '' && $record['Client_HomeAddress1'] != 'NULL' ? $record['Client_HomeAddress1'] : NULL;
            // $formInputDateData = new FormInputTextData();
            // $formInputDateData->form_input_text_id = 23;
            // $formInputDateData->data = $clientAddressLine1;
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();

            /*// Client Address 2 - 25
            $clientAddressLine2 = isset($record['Client_HomeAddress2']) && $record['Client_HomeAddress2'] != '' && $record['Client_HomeAddress2'] != 'NULL' ? $record['Client_HomeAddress2'] : NULL;
            $formInputDateData = new FormInputTextData();
            $formInputDateData->form_input_date_id = 25;
            $formInputDateData->data = $clientAddressLine2;
            $formInputDateData->client_id = $client->id;
            $formInputDateData->user_id = 1;
            $formInputDateData->duration = 120;
            $formInputDateData->save();*/

            // Client Suburb - 26
            // $clientSuburb = isset($record['Client_HomeAddress2']) && $record['Client_HomeAddress2'] != '' && $record['Client_HomeAddress2'] != 'NULL' ? $record['Client_HomeAddress2'] : NULL;
            // $formInputDateData = new FormInputTextData();
            // $formInputDateData->form_input_text_id = 26;
            // $formInputDateData->data = $clientSuburb;
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();

            // // Client City - 27
            // $clientCity = isset($record['Client_HomeAddress3']) && $record['Client_HomeAddress3'] != '' && $record['Client_HomeAddress3'] != 'NULL' ? $record['Client_HomeAddress3'] : NULL;
            // $formInputDateData = new FormInputTextData();
            // $formInputDateData->form_input_text_id = 27;
            // $formInputDateData->data = $clientCity;
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();

            // // Client Postal Code - 101
            // $clientPostalCode = isset($record['Client_HomeCode']) && $record['Client_HomeCode'] != '' && $record['Client_HomeCode'] != 'NULL' ? $record['Client_HomeCode'] : NULL;
            // $formInputDateData = new FormInputTextData();
            // $formInputDateData->form_input_text_id = 101;
            // $formInputDateData->data = $clientPostalCode;
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();

            // // Client Postal Address 1 - 31
            // $clientPostalAddressLine1 = isset($record['Client_PostalAddress1']) && $record['Client_PostalAddress1'] != '' && $record['Client_PostalAddress1'] != 'NULL' ? $record['Client_PostalAddress1'] : NULL;
            // $formInputDateData = new FormInputTextData();
            // $formInputDateData->form_input_text_id = 31;
            // $formInputDateData->data = $clientPostalAddressLine1;
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();

            // /*// Client Postal Address 2 - 32
            // $clientPostalAddressLine2 = isset($record['Client_HomeAddress2']) && $record['Client_HomeAddress2'] != '' && $record['Client_HomeAddress2'] != 'NULL' ? $record['Client_HomeAddress2'] : NULL;
            // $formInputDateData = new FormInputTextData();
            // $formInputDateData->form_input_date_id = 32;
            // $formInputDateData->data = $clientAddressLine2;
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();*/

            // // Client Postal Suburb - 32
            // $clientPostalSuburb = isset($record['Client_PostalAddress2']) && $record['Client_PostalAddress2'] != '' && $record['Client_PostalAddress2'] != 'NULL' ? $record['Client_PostalAddress2'] : NULL;
            // $formInputDateData = new FormInputTextData();
            // $formInputDateData->form_input_text_id = 32;
            // $formInputDateData->data = $clientPostalSuburb;
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();

            // // Client Postal City - 33
            // $clientCity = isset($record['Client_PostalAddress3']) && $record['Client_PostalAddress3'] != '' && $record['Client_PostalAddress3'] != 'NULL' ? $record['Client_PostalAddress3'] : NULL;
            // $formInputDateData = new FormInputTextData();
            // $formInputDateData->form_input_text_id = 33;
            // $formInputDateData->data = $clientCity;
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();

            // // Client Postal Postal Code - 35
            // $clientPostalPostalCode = isset($record['Client_PostalCode']) && $record['Client_PostalCode'] != '' && $record['Client_PostalCode'] != 'NULL' ? $record['Client_PostalCode'] : NULL;
            // $formInputDateData = new FormInputTextData();
            // $formInputDateData->form_input_text_id = 35;
            // $formInputDateData->data = $clientPostalPostalCode;
            // $formInputDateData->client_id = $client->id;
            // $formInputDateData->user_id = 1;
            // $formInputDateData->duration = 120;
            // $formInputDateData->save();

            // // Title : 9 - Mr, 10 - Mrs, 11 - Prof, 12 - Dr, 13 - Miss, 121 - Ms
            // $clientTitle = isset($record['Client_Title']) && $record['Client_Title'] != '' && $record['Client_Title'] != 'NULL' ? $record['Client_Title'] : NULL;
            // $title_id = null;
            // switch (trim($clientTitle)){
            //     case 'Mr.';
            //         $title_id = 9;
            //         break;
            //     case 'Mrs.';
            //         $title_id = 10;
            //         break;
            //     case 'Prof.';
            //         $title_id = 11;
            //         break;
            //     case 'Dr.';
            //         $title_id = 12;
            //         break;
            //     case 'Miss.';
            //         $title_id = 13;
            //         break;
            //     case 'Ms.';
            //         $title_id = 121;
            //         break;
            // }

            // if(isset($title_id)) {
            //     $formInputDateData = new FormInputDropdownData();
            //     $formInputDateData->form_input_dropdown_id = 4;
            //     $formInputDateData->form_input_dropdown_item_id = $title_id;
            //     $formInputDateData->client_id = $client->id;
            //     $formInputDateData->user_id = 1;
            //     $formInputDateData->duration = 120;
            //     $formInputDateData->save();
            // }

            // // Gender : 112 - Male, 113 - Female
            // $clientGender = isset($record['Client_Gender']) && $record['Client_Gender'] != '' && $record['Client_Gender'] != 'NULL' ? $record['Client_Gender'] : NULL;
            // $gender_id = null;
            // switch (trim($clientGender)){
            //     case 'Male';
            //         $gender_id = 112;
            //         break;
            //     case 'Female.';
            //         $gender_id = 113;
            //         break;
            //     case 'M';
            //         $gender_id = 112;
            //         break;
            //     case 'F';
            //         $gender_id = 113;
            //         break;
            // }

            // if(isset($gender_id)) {
            //     $formInputDateData = new FormInputDropdownData();
            //     $formInputDateData->form_input_dropdown_id = 31;
            //     $formInputDateData->form_input_dropdown_item_id = $gender_id;
            //     $formInputDateData->client_id = $client->id;
            //     $formInputDateData->user_id = 1;
            //     $formInputDateData->duration = 120;
            //     $formInputDateData->save();
            // }

            // // Marital Status : 14 - Married, 15 - Single, 16 - Divorced, 103 - Common Law, 104 - Widowed
            // $maritalStatusID = isset($record['Marital_Type']) && $record['Marital_Type'] != '' && $record['Marital_Type'] != 'NULL' ? $record['Marital_Type'] : NULL;
            // $marital_status_id = null;
            // switch (trim($maritalStatusID)){
            //     case 'Married';
            //         $marital_status_id = 14;
            //         break;
            //     case 'Single';
            //         $marital_status_id = 15;
            //         break;
            //     case 'Divorced';
            //         $marital_status_id = 16;
            //         break;
            //     case 'Common Law';
            //         $marital_status_id = 103;
            //         break;
            //     case 'Widowed';
            //         $marital_status_id = 104;
            //         break;
            //     /*
            //     case 'Married - ANC without accrual';
            //         $marital_status_id = null;
            //         break;
            //     case 'Married - ANC with accrual';
            //         $marital_status_id = null;
            //         break;
            //     case 'Married in COP';
            //         $marital_status_id = null;
            //         break;
            //     case 'Married out of COP';
            //         $marital_status_id = null;
            //         break;
            //     case 'Married - ANC with accrual';
            //         $marital_status_id = null;
            //         break;
            //     */
            // }

            // if(isset($marital_status_id)) {
            //     $formInputDateData = new FormInputDropdownData();
            //     $formInputDateData->form_input_dropdown_id = 5;
            //     $formInputDateData->form_input_dropdown_item_id = $marital_status_id;
            //     $formInputDateData->client_id = $client->id;
            //     $formInputDateData->user_id = 1;
            //     $formInputDateData->duration = 120;
            //     $formInputDateData->save();
            // }

        }
    }
}
