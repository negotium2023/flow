<?php

use Illuminate\Database\Seeder;
use League\Csv\Reader;
use League\Csv\Statement;
use App\ActionableDateData;
use App\ActionableTextareaData;
use App\ActionableDropdownData;
use App\ActionableTextData;
use App\ActionableBooleanData;
use App\ClientProcess;
use Carbon\Carbon;

class CrfClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->getClientFiles('latest_client_crf.csv');
    }

    public function getClientFiles($fileName)
    {
        $csv = Reader::createFromPath(database_path('/data/'.$fileName, 'r'));
        $csv->setDelimiter(',');
        $csv->setHeaderOffset(0);
        $stmt = (new Statement());
        $records = $stmt->process($csv);
// dd($records);
        foreach ($records as $record_key => $record) {

            // //form_date
            // if($record['form_date'] != null){
            //     $client_crf_date = new ActionableDateData;
            //     $client_crf_date->actionable_date_id = 62;
            //     if(isset($record['form_date']) && $record['form_date'] != null){
            //         $client_crf_date->data = Carbon::parse($record['form_date'])->format('Y-m-d');
            //     }else{
            //         $client_crf_date->data = '';
            //     }
            //     $client_crf_date->client_id = $record['client_id'];
            //     $client_crf_date->user_id = 1;
            //     $client_crf_date->duration = 120;
            //     $client_crf_date->created_at = '2022-03-10 00:00:00';
            //     $client_crf_date->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_date->save();
            // }


            // if($record['ct_filing_date'] != ''){
            //     $client_crf_date = new ActionableDateData;
            //     $client_crf_date->actionable_date_id = 63;
            //     if(isset($record['ct_filing_date']) && $record['ct_filing_date'] != null){
            //         $client_crf_date->data = Carbon::parse($record['ct_filing_date'])->format('Y-m-d');
            //     }else{
            //         $client_crf_date->data = '';
            //     }
            //     $client_crf_date->client_id = $record['client_id'];
            //     $client_crf_date->user_id = 1;
            //     $client_crf_date->duration = 120;
            //     $client_crf_date->created_at = '2022-03-10 00:00:00';
            //     $client_crf_date->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_date->save();
            // }

            // if($record['pen_tax_filing_date'] != ''){
            //     $client_crf_date = new ActionableDateData;
            //     $client_crf_date->actionable_date_id = 64;
            //     if(isset($record['pen_tax_filing_date']) && $record['pen_tax_filing_date'] != null){
            //         $client_crf_date->data = Carbon::parse($record['pen_tax_filing_date'])->format('Y-m-d');
            //     }else{
            //         $client_crf_date->data = '';
            //     }
            //     $client_crf_date->client_id = $record['client_id'];
            //     $client_crf_date->user_id = 1;
            //     $client_crf_date->duration = 120;
            //     $client_crf_date->created_at = '2022-03-10 00:00:00';
            //     $client_crf_date->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_date->save();
            // }

            // if($record['director'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 142;
            //     if ($record['director'] == 'Alan Farrelly') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1360;
            //     }
            //     if ($record['director'] == 'Martina Gribben') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1361;
            //     }
            //     if ($record['director'] == 'Michael Bellew') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1362;
            //     }
            //     if ($record['director'] == 'Richard Berney') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1363;
            //     }
            //     if ($record['director'] == 'Gareth Evans') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1364;
            //     }
            //     if ($record['director'] == 'Thomas McDonagh') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1365;
            //     }
            //     if ($record['director'] == 'Darren Connolly') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1366;
            //     }
            //     if ($record['director'] == 'Alison Gray') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1367;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['office'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 144;
            //     if ($record['office'] == 'Balbriggan') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1222;
            //     }
            //     if ($record['office'] == 'Dublin') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1223;
            //     }
            //     if ($record['office'] == 'Belfast') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1224;
            //     }
            //     if ($record['office'] == 'Dundalk') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1376;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['manager'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 164;
            //     if ($record['manager'] == 'Mairead Rooney') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1371;
            //     }
            //     if ($record['manager'] == 'Jane Jackson') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1372;
            //     }
            //     if ($record['manager'] == 'Niall Donnelly') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1373;
            //     }
            //     if ($record['manager'] == 'Matthew Whelan') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1374;
            //     }
            //     if ($record['manager'] == 'Fergal Maher') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1375;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['contact_type'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 145;
            //     if ($record['contact_type'] == 'Limited') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1225;
            //     }
            //     if ($record['contact_type'] == 'LLP') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1226;
            //     }
            //     if ($record['contact_type'] == 'Non for Profit Organisation') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1227;
            //     }
            //     if ($record['contact_type'] == 'Other Organisation') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1228;
            //     }
            //     if ($record['contact_type'] == 'Other Person') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1229;
            //     }
            //     if ($record['contact_type'] == 'Partnership') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1230;
            //     }
            //     if ($record['contact_type'] == 'Trust') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1231;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['business_type'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 146;
            //     if ($record['business_type'] == 'Individual') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1232;
            //     }
            //     if ($record['business_type'] == 'Limited') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1233;
            //     }
            //     if ($record['business_type'] == 'LLP') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1234;
            //     }
            //     if ($record['business_type'] == 'Other') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1235;
            //     }
            //     if ($record['business_type'] == 'Partnership') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1236;
            //     }
            //     if ($record['business_type'] == 'Sole Trader') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1237;
            //     }
            //     if ($record['business_type'] == 'Solicitor') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1238;
            //     }
            //     if ($record['business_type'] == 'Unlimited') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1239;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['client_type'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 147;
            //     if ($record['client_type'] == 'Sole Trader') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1240;
            //     }
            //     if ($record['client_type'] == 'Partnership') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1241;
            //     }
            //     if ($record['client_type'] == 'Charity') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1242;
            //     }
            //     if ($record['client_type'] == 'Individual') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1243;
            //     }
            //     if ($record['client_type'] == 'Non for Profit Organisation') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1244;
            //     }
            //     if ($record['client_type'] == 'Pension Scheme') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1245;
            //     }
            //     if ($record['client_type'] == 'Trust') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1246;
            //     }
            //     if ($record['client_type'] == 'Company Limited by Shares (LTD)') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1247;
            //     }
            //     if ($record['client_type'] == 'Company Limited by Guarantee (CLG)') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1248;
            //     }
            //     if ($record['client_type'] == 'Unlimited Company') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1249;
            //     }
            //     if ($record['client_type'] == 'Designated Activity Company (DAC)') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1250;
            //     }
            //     if ($record['client_type'] == 'Public Limited Company (PLC)') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1251;
            //     }
            //     if ($record['client_type'] == 'Limited Liability Partnership (LLP)') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1252;
            //     }
            //     if ($record['client_type'] == 'Societas Europaea Company (SE)') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1253;
            //     }
            //     if ($record['client_type'] == 'Branch') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1254;
            //     }
            //     if ($record['client_type'] == 'Other') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1255;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['country'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 148;
            //     if ($record['country'] == 'ROI') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1256;
            //     }
            //     if ($record['country'] == 'NI') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1257;
            //     }
            //     if ($record['country'] == 'UK') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1258;
            //     }
            //     if ($record['country'] == 'Other') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1259;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['industry'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 149;
            //     if ($record['industry'] == 'Agriculture / Farming / Forestry') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1260;
            //     }
            //     if ($record['industry'] == 'Charity / Not for Profit') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1261;
            //     }
            //     if ($record['industry'] == 'Community Groups') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1262;
            //     }
            //     if ($record['industry'] == 'Construction') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1263;
            //     }
            //     if ($record['industry'] == 'Energy / Natural Resources') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1264;
            //     }
            //     if ($record['industry'] == 'Fishery / Mariculture') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1265;
            //     }
            //     if ($record['industry'] == 'Food / Drink Manufacturing') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1266;
            //     }
            //     if ($record['industry'] == 'Hospitality') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1267;
            //     }
            //     if ($record['industry'] == 'Investment / Holding Company') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1268;
            //     }
            //     if ($record['industry'] == 'IT / Software') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1269;
            //     }
            //     if ($record['industry'] == 'Legal / Professional Services') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1270;
            //     }
            //     if ($record['industry'] == 'Management Company') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1271;
            //     }
            //     if ($record['industry'] == 'Manufacturing') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1272;
            //     }
            //     if ($record['industry'] == 'Medical Devices') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1273;
            //     }
            //     if ($record['industry'] == 'Medical Professionals') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1274;
            //     }
            //     if ($record['industry'] == 'Motor / Auto') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1275;
            //     }
            //     if ($record['industry'] == 'Pharmaceutical') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1276;
            //     }
            //     if ($record['industry'] == 'Property Investment') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1277;
            //     }
            //     if ($record['industry'] == 'Property Management') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1278;
            //     }
            //     if ($record['industry'] == 'Regulated Services') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1279;
            //     }
            //     if ($record['industry'] == 'Services') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1280;
            //     }
            //     if ($record['industry'] == 'Transport / Haulage') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1281;
            //     }
            //     if ($record['industry'] == 'Wholesale / Retail') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1282;
            //     }
            //     if ($record['industry'] == 'Other') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1283;
            //     }
            //     if ($record['industry'] == 'N/A') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1284;
            //     }
            //     if ($record['industry'] == 'Tax Case') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1285;
            //     }
            //     if ($record['industry'] == 'Family Law Case') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1286;
            //     }
            //     if ($record['industry'] == 'Project Work') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1287;
            //     }
            //     if ($record['industry'] == 'Liquidation Case') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1288;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['year_end'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 150;
            //     if ($record['year_end'] == 'January') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1289;
            //     }
            //     if ($record['year_end'] == 'February') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1290;
            //     }
            //     if ($record['year_end'] == 'March') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1291;
            //     }
            //     if ($record['year_end'] == 'April') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1292;
            //     }
            //     if ($record['year_end'] == 'May') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1293;
            //     }
            //     if ($record['year_end'] == 'June') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1294;
            //     }
            //     if ($record['year_end'] == 'July') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1295;
            //     }
            //     if ($record['year_end'] == 'August') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1296;
            //     }
            //     if ($record['year_end'] == 'September') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1297;
            //     }
            //     if ($record['year_end'] == 'October') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1298;
            //     }
            //     if ($record['year_end'] == 'November') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1299;
            //     }
            //     if ($record['year_end'] == 'December') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1300;
            //     }
            //     if ($record['year_end'] == 'Not Currently Available') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1301;
            //     }
            //     if ($record['year_end'] == 'N/A') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1302;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['audit_status'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 153;
            //     if ($record['audit_status'] == 'Required') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1320;
            //     }
            //     if ($record['audit_status'] == 'Exempt') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1321;
            //     }
            //     if ($record['audit_status'] == 'Not Currently Available') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1322;
            //     }
            //     if ($record['audit_status'] == 'N/A') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1323;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['cosec_filing'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 152;
            //     if ($record['cosec_filing'] == 'Yes') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1318;
            //     }
            //     if ($record['cosec_filing'] == 'No') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1319;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['tax_filing'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 154;
            //     if ($record['tax_filing'] == 'Yes - ROI') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1324;
            //     }
            //     if ($record['tax_filing'] == 'Yes - NI/UK') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1325;
            //     }
            //     if ($record['tax_filing'] == 'No') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1326;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['st_personal_tax'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 155;
            //     if ($record['st_personal_tax'] == 'Yes - ROI') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1327;
            //     }
            //     if ($record['st_personal_tax'] == 'Yes - NI/UK') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1328;
            //     }
            //     if ($record['st_personal_tax'] == 'No') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1329;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['p_personal_tax'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 156;
            //     if ($record['p_personal_tax'] == 'Yes - ROI') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1330;
            //     }
            //     if ($record['p_personal_tax'] == 'Yes - NI/UK') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1331;
            //     }
            //     if ($record['p_personal_tax'] == 'No') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1332;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['pen_tax_filing'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 157;
            //     if ($record['pen_tax_filing'] == 'Yes - ROI') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1333;
            //     }
            //     if ($record['pen_tax_filing'] == 'Yes - NI/UK') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1334;
            //     }
            //     if ($record['pen_tax_filing'] == 'No') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1335;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['x_how_did_you_hear'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 158;
            //     if ($record['x_how_did_you_hear'] == 'Referral') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1336;
            //     }
            //     if ($record['x_how_did_you_hear'] == 'Existing Client') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1337;
            //     }
            //     if ($record['x_how_did_you_hear'] == 'Golden Pages') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1338;
            //     }
            //     if ($record['x_how_did_you_hear'] == 'Internet Search') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1339;
            //     }
            //     if ($record['x_how_did_you_hear'] == 'Staff Family or Friend') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1340;
            //     }
            //     if ($record['x_how_did_you_hear'] == 'Targeted Business') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1341;
            //     }
            //     if ($record['x_how_did_you_hear'] == 'Other') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1342;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['p_client_rating'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 161;
            //     if ($record['p_client_rating'] == 'A') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1350;
            //     }
            //     if ($record['p_client_rating'] == 'B') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1351;
            //     }
            //     if ($record['p_client_rating'] == 'C') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1352;
            //     }
            //     if ($record['p_client_rating'] == 'D') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1353;
            //     }
            //     if ($record['p_client_rating'] == 'E') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1354;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['p_tax_type'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 159;
            //     if ($record['p_tax_type'] == 'Standard') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1343;
            //     }
            //     if ($record['p_tax_type'] == 'Exempt') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1344;
            //     }
            //     if ($record['p_tax_type'] == 'Outside Scope') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1345;
            //     }
            //     if ($record['p_tax_type'] == 'Discounted (13.5%)') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1346;
            //     }
            //     if ($record['p_tax_type'] == 'GBP') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1347;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['p_currency'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 160;
            //     if ($record['p_currency'] == 'Euro') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1348;
            //     }
            //     if ($record['p_currency'] == 'GBP') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1349;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['cr_copy_of_certificate'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 163;
            //     if ($record['cr_copy_of_certificate'] == 'Yes') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1358;
            //     }
            //     if ($record['cr_copy_of_certificate'] == 'No') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1359;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // if($record['cr_company_search'] != ''){
            //     $client_crf_dropdown = new ActionableDropdownData;
            //     $client_crf_dropdown->actionable_dropdown_id = 162;
            //     if ($record['cr_company_search'] == 'Yes') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1355;
            //     }
            //     if ($record['cr_company_search'] == 'No') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1356;
            //     }
            //     if ($record['cr_company_search'] == 'N/A as the company is a new incorporation') {
            //         $client_crf_dropdown->actionable_dropdown_item_id = 1357;
            //     }
            //     $client_crf_dropdown->client_id = $record['client_id'];
            //     $client_crf_dropdown->user_id = 1;
            //     $client_crf_dropdown->duration = 120;
            //     $client_crf_dropdown->created_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->updated_at = '2022-03-10 00:00:00';
            //     $client_crf_dropdown->save();
            // }

            // $client_crf_textarea = new ActionableTextareaData;
            // $client_crf_textarea->data = $record['x_notes'];
            // $client_crf_textarea->actionable_textarea_id = 47;
            // $client_crf_textarea->client_id = $record['client_id'];
            // $client_crf_textarea->user_id = 1;
            // $client_crf_textarea->duration = 120;
            // $client_crf_textarea->created_at = '2022-03-10 00:00:00';
            // $client_crf_textarea->updated_at = '2022-03-10 00:00:00';
            // $client_crf_textarea->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['client_name'];
            // $client_crf_text->actionable_text_id = 352;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['form_completed_by'];
            // $client_crf_text->actionable_text_id = 353;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['country'];
            // $client_crf_text->actionable_text_id = 354;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['services'];
            // $client_crf_text->actionable_text_id = 654;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['vat_number'];
            // $client_crf_text->actionable_text_id = 357;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['ct_number'];
            // $client_crf_text->actionable_text_id = 356;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['company_registration_number'];
            // $client_crf_text->actionable_text_id = 355;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['business_address2'];
            // $client_crf_text->actionable_text_id = 366;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['business_address1'];
            // $client_crf_text->actionable_text_id = 365;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['billing_country'];
            // $client_crf_text->actionable_text_id = 364;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['billing_pcode'];
            // $client_crf_text->actionable_text_id = 363;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['billing_county'];
            // $client_crf_text->actionable_text_id = 362;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['billing_town'];
            // $client_crf_text->actionable_text_id = 361;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['billing_address3'];
            // $client_crf_text->actionable_text_id = 360;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['billing_address2'];
            // $client_crf_text->actionable_text_id = 359;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['billing_address1'];
            // $client_crf_text->actionable_text_id = 358;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['registered_town'];
            // $client_crf_text->actionable_text_id = 375;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['registered_address3'];
            // $client_crf_text->actionable_text_id = 374;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['registered_address2'];
            // $client_crf_text->actionable_text_id = 373;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['registered_address1'];
            // $client_crf_text->actionable_text_id = 372;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['business_country'];
            // $client_crf_text->actionable_text_id = 371;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['business_pcode'];
            // $client_crf_text->actionable_text_id = 370;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['business_county'];
            // $client_crf_text->actionable_text_id = 369;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['business_town'];
            // $client_crf_text->actionable_text_id = 368;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['business_address3'];
            // $client_crf_text->actionable_text_id = 367;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['company_website'];
            // $client_crf_text->actionable_text_id = 382;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['company_email_address'];
            // $client_crf_text->actionable_text_id = 381;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['company_fax_number'];
            // $client_crf_text->actionable_text_id = 380;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['company_tel_number1'];
            // $client_crf_text->actionable_text_id = 379;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['registered_country'];
            // $client_crf_text->actionable_text_id = 378;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['registered_pcode'];
            // $client_crf_text->actionable_text_id = 377;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['registered_county'];
            // $client_crf_text->actionable_text_id = 376;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_country'];
            // $client_crf_text->actionable_text_id = 425;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_pcode'];
            // $client_crf_text->actionable_text_id = 424;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_county'];
            // $client_crf_text->actionable_text_id = 423;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_town'];
            // $client_crf_text->actionable_text_id = 422;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_address3'];
            // $client_crf_text->actionable_text_id = 421;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_address2'];
            // $client_crf_text->actionable_text_id = 420;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_address1'];
            // $client_crf_text->actionable_text_id = 419;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_country'];
            // $client_crf_text->actionable_text_id = 418;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_pcode'];
            // $client_crf_text->actionable_text_id = 417;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_county'];
            // $client_crf_text->actionable_text_id = 416;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_town'];
            // $client_crf_text->actionable_text_id = 415;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_address3'];
            // $client_crf_text->actionable_text_id = 414;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_address2'];
            // $client_crf_text->actionable_text_id = 413;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_billing_address1'];
            // $client_crf_text->actionable_text_id = 412;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_vat_number'];
            // $client_crf_text->actionable_text_id = 411;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_ni_number'];
            // $client_crf_text->actionable_text_id = 410;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_uk_tax_number'];
            // $client_crf_text->actionable_text_id = 409;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_website'];
            // $client_crf_text->actionable_text_id = 440;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_email_address2'];
            // $client_crf_text->actionable_text_id = 439;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_email_address1'];
            // $client_crf_text->actionable_text_id = 438;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_fax_number'];
            // $client_crf_text->actionable_text_id = 437;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_mobile_number'];
            // $client_crf_text->actionable_text_id = 436;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_home_number'];
            // $client_crf_text->actionable_text_id = 435;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_office_number'];
            // $client_crf_text->actionable_text_id = 434;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_contact_name'];
            // $client_crf_text->actionable_text_id = 433;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_business_country'];
            // $client_crf_text->actionable_text_id = 432;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_business_pcode'];
            // $client_crf_text->actionable_text_id = 431;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_business_county'];
            // $client_crf_text->actionable_text_id = 430;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_business_town'];
            // $client_crf_text->actionable_text_id = 429;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_business_address3'];
            // $client_crf_text->actionable_text_id = 428;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_business_address2'];
            // $client_crf_text->actionable_text_id = 427;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['st_business_address1'];
            // $client_crf_text->actionable_text_id = 426;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_billing_address1'];
            // $client_crf_text->actionable_text_id = 445;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_vat_number'];
            // $client_crf_text->actionable_text_id = 444;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_ni_number'];
            // $client_crf_text->actionable_text_id = 443;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_uk_tax_number'];
            // $client_crf_text->actionable_text_id = 442;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_partnership_ref_no'];
            // $client_crf_text->actionable_text_id = 441;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_website'];
            // $client_crf_text->actionable_text_id = 463;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_email_address2'];
            // $client_crf_text->actionable_text_id = 462;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_email_address1'];
            // $client_crf_text->actionable_text_id = 461;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_fax_number'];
            // $client_crf_text->actionable_text_id = 460;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_office_number'];
            // $client_crf_text->actionable_text_id = 459;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_business_country'];
            // $client_crf_text->actionable_text_id = 458;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_business_pcode'];
            // $client_crf_text->actionable_text_id = 457;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_business_county'];
            // $client_crf_text->actionable_text_id = 456;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_business_town'];
            // $client_crf_text->actionable_text_id = 455;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_business_address3'];
            // $client_crf_text->actionable_text_id = 454;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_business_address2'];
            // $client_crf_text->actionable_text_id = 453;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_business_address1'];
            // $client_crf_text->actionable_text_id = 452;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_billing_country'];
            // $client_crf_text->actionable_text_id = 451;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_billing_pcode'];
            // $client_crf_text->actionable_text_id = 450;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_billing_county'];
            // $client_crf_text->actionable_text_id = 449;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_billing_town'];
            // $client_crf_text->actionable_text_id = 448;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_billing_address3'];
            // $client_crf_text->actionable_text_id = 447;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_billing_address2'];
            // $client_crf_text->actionable_text_id = 446;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_contact_name'];
            // $client_crf_text->actionable_text_id = 484;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_country'];
            // $client_crf_text->actionable_text_id = 483;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_pcode'];
            // $client_crf_text->actionable_text_id = 482;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_county'];
            // $client_crf_text->actionable_text_id = 481;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_town'];
            // $client_crf_text->actionable_text_id = 480;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_address3'];
            // $client_crf_text->actionable_text_id = 479;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_address2'];
            // $client_crf_text->actionable_text_id = 478;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_address1'];
            // $client_crf_text->actionable_text_id = 477;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_tax_no'];
            // $client_crf_text->actionable_text_id = 476;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_ref_no'];
            // $client_crf_text->actionable_text_id = 475;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_res_country'];
            // $client_crf_text->actionable_text_id = 497;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_res_pcode'];
            // $client_crf_text->actionable_text_id = 496;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_res_county'];
            // $client_crf_text->actionable_text_id = 495;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_res_town'];
            // $client_crf_text->actionable_text_id = 494;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_res_address3'];
            // $client_crf_text->actionable_text_id = 493;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_res_address2'];
            // $client_crf_text->actionable_text_id = 492;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_res_address1'];
            // $client_crf_text->actionable_text_id = 491;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_email_address'];
            // $client_crf_text->actionable_text_id = 490;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_fax_number'];
            // $client_crf_text->actionable_text_id = 489;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_mobile_number'];
            // $client_crf_text->actionable_text_id = 488;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_home_number'];
            // $client_crf_text->actionable_text_id = 487;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_office_number'];
            // $client_crf_text->actionable_text_id = 486;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['pen_billing_position'];
            // $client_crf_text->actionable_text_id = 485;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_home_number'];
            // $client_crf_text->actionable_text_id = 516;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_office_number'];
            // $client_crf_text->actionable_text_id = 515;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_position'];
            // $client_crf_text->actionable_text_id = 514;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_name'];
            // $client_crf_text->actionable_text_id = 513;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_country'];
            // $client_crf_text->actionable_text_id = 512;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_pcode'];
            // $client_crf_text->actionable_text_id = 511;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_county'];
            // $client_crf_text->actionable_text_id = 510;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_town'];
            // $client_crf_text->actionable_text_id = 509;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_address3'];
            // $client_crf_text->actionable_text_id = 508;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_address2'];
            // $client_crf_text->actionable_text_id = 507;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_address1'];
            // $client_crf_text->actionable_text_id = 506;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_email_address'];
            // $client_crf_text->actionable_text_id = 505;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_mobile_number'];
            // $client_crf_text->actionable_text_id = 504;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_home_number'];
            // $client_crf_text->actionable_text_id = 503;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_office_number'];
            // $client_crf_text->actionable_text_id = 502;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_position'];
            // $client_crf_text->actionable_text_id = 501;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d1_name'];
            // $client_crf_text->actionable_text_id = 500;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_tax_ref'];
            // $client_crf_text->actionable_text_id = 499;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_company_registration_number'];
            // $client_crf_text->actionable_text_id = 498;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_country'];
            // $client_crf_text->actionable_text_id = 525;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_pcode'];
            // $client_crf_text->actionable_text_id = 524;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_county'];
            // $client_crf_text->actionable_text_id = 523;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_town'];
            // $client_crf_text->actionable_text_id = 522;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_address3'];
            // $client_crf_text->actionable_text_id = 521;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_address2'];
            // $client_crf_text->actionable_text_id = 520;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_address1'];
            // $client_crf_text->actionable_text_id = 519;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_email_address'];
            // $client_crf_text->actionable_text_id = 518;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['liq_d2_mobile_number'];
            // $client_crf_text->actionable_text_id = 517;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['x_client_name1'];
            // $client_crf_text->actionable_text_id = 531;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['x_relationship_type1'];
            // $client_crf_text->actionable_text_id = 530;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['x_staff_member_name'];
            // $client_crf_text->actionable_text_id = 529;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['x_existing_account_name'];
            // $client_crf_text->actionable_text_id = 528;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['x_referrer_name'];
            // $client_crf_text->actionable_text_id = 527;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['x_how_did_you_hear'];
            // $client_crf_text->actionable_text_id = 526;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_client_value'];
            // $client_crf_text->actionable_text_id = 538;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_email'];
            // $client_crf_text->actionable_text_id = 537;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['p_credit_limit'];
            // $client_crf_text->actionable_text_id = 536;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['x_client_code2'];
            // $client_crf_text->actionable_text_id = 535;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['x_client_name2'];
            // $client_crf_text->actionable_text_id = 534;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['x_relationship_type2'];
            // $client_crf_text->actionable_text_id = 533;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['x_client_code1'];
            // $client_crf_text->actionable_text_id = 532;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['cdd_date_and_place'];
            // $client_crf_text->actionable_text_id = 541;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['cdd_sort_of_business'];
            // $client_crf_text->actionable_text_id = 540;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

            // $client_crf_text = new ActionableTextData;
            // $client_crf_text->data = $record['cdd_source'];
            // $client_crf_text->actionable_text_id = 539;
            // $client_crf_text->client_id = $record['client_id'];
            // $client_crf_text->user_id = 1;
            // $client_crf_text->duration = 120;
            // $client_crf_text->created_at = '2022-03-10 00:00:00';
            // $client_crf_text->updated_at = '2022-03-10 00:00:00';
            // $client_crf_text->save();

//cosec_filing - 157
            if($record['cosec_filing'] != ''){
                $client_crf_boolean = new ActionableBooleanData;
                if($record['cosec_filing'] == 'Yes'){
                    $client_crf_boolean->data = 1;
                }
                if($record['cosec_filing'] == 'No'){
                    $client_crf_boolean->data = 0;
                }
                $client_crf_boolean->actionable_boolean_id = 157;
                $client_crf_boolean->client_id = $record['client_id'];
                $client_crf_boolean->user_id = 1;
                $client_crf_boolean->duration = 120;
                $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
                $client_crf_boolean->save();
            }


//x_tailored_emails - 166
            if($record['x_tailored_emails'] != ''){
                $client_crf_boolean = new ActionableBooleanData;
                if($record['x_tailored_emails'] == 'Yes'){
                    $client_crf_boolean->data = 1;
                }
                if($record['x_tailored_emails'] == 'No'){
                    $client_crf_boolean->data = 0;
                }
                $client_crf_boolean->actionable_boolean_id = 166;
                $client_crf_boolean->client_id = $record['client_id'];
                $client_crf_boolean->user_id = 1;
                $client_crf_boolean->duration = 120;
                $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
                $client_crf_boolean->save();
            }


//x_uk_mailings - 167
            if($record['x_uk_mailings'] != ''){
                $client_crf_boolean = new ActionableBooleanData;
                if($record['x_uk_mailings'] == 'Yes'){
                    $client_crf_boolean->data = 1;
                }
                if($record['x_uk_mailings'] == 'No'){
                    $client_crf_boolean->data = 0;
                }
                $client_crf_boolean->actionable_boolean_id = 167;
                $client_crf_boolean->client_id = $record['client_id'];
                $client_crf_boolean->user_id = 1;
                $client_crf_boolean->duration = 120;
                $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
                $client_crf_boolean->save();
            }


//p_invoice_email_only - 168
            if($record['p_invoice_email_only'] != ''){
                $client_crf_boolean = new ActionableBooleanData;
                if($record['p_invoice_email_only'] == 'Yes'){
                    $client_crf_boolean->data = 1;
                }
                if($record['p_invoice_email_only'] == 'No'){
                    $client_crf_boolean->data = 0;
                }
                $client_crf_boolean->actionable_boolean_id = 168;
                $client_crf_boolean->client_id = $record['client_id'];
                $client_crf_boolean->user_id = 1;
                $client_crf_boolean->duration = 120;
                $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
                $client_crf_boolean->save();
            }


//p_fee_quote - 169
            if($record['p_fee_quote'] != ''){
                $client_crf_boolean = new ActionableBooleanData;
                if($record['p_fee_quote'] == 'Yes'){
                    $client_crf_boolean->data = 1;
                }
                if($record['p_fee_quote'] == 'No'){
                    $client_crf_boolean->data = 0;
                }
                $client_crf_boolean->actionable_boolean_id = 169;
                $client_crf_boolean->client_id = $record['client_id'];
                $client_crf_boolean->user_id = 1;
                $client_crf_boolean->duration = 120;
                $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
                $client_crf_boolean->save();
            }


//cr_list_of_directors - 170
        if($record['cr_list_of_directors'] != ''){
            $client_crf_boolean = new ActionableBooleanData;
            if($record['cr_list_of_directors'] == 'Yes'){
                $client_crf_boolean->data = 1;
            }
            if($record['cr_list_of_directors'] == 'No'){
                $client_crf_boolean->data = 0;
            }
            $client_crf_boolean->actionable_boolean_id = 170;
            $client_crf_boolean->client_id = $record['client_id'];
            $client_crf_boolean->user_id = 1;
            $client_crf_boolean->duration = 120;
            $client_crf_boolean->created_at = '2022-03-10 00:00:00';
            $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
            $client_crf_boolean->save();
        }


//cr_list_of_names -171
        if($record['cr_list_of_names'] != ''){
            $client_crf_boolean = new ActionableBooleanData;
            if($record['cr_list_of_names'] == 'Yes'){
                $client_crf_boolean->data = 1;
            }
            if($record['cr_list_of_names'] == 'No'){
                $client_crf_boolean->data = 0;
            }
            $client_crf_boolean->actionable_boolean_id = 171;
            $client_crf_boolean->client_id = $record['client_id'];
            $client_crf_boolean->user_id = 1;
            $client_crf_boolean->duration = 120;
            $client_crf_boolean->created_at = '2022-03-10 00:00:00';
            $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
            $client_crf_boolean->save();
        }


//cr_photo_id - 172
        if($record['cr_photo_id'] != ''){
            $client_crf_boolean = new ActionableBooleanData;
            if($record['cr_photo_id'] == 'Yes'){
                $client_crf_boolean->data = 1;
            }
            if($record['cr_photo_id'] == 'No'){
                $client_crf_boolean->data = 0;
            }
            $client_crf_boolean->actionable_boolean_id = 172;
            $client_crf_boolean->client_id = $record['client_id'];
            $client_crf_boolean->user_id = 1;
            $client_crf_boolean->duration = 120;
            $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
            $client_crf_boolean->save();
        }


//cr_due_diligence - 173
if($record['cr_due_diligence'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['cr_due_diligence'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['cr_due_diligence'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 173;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}


//ir_satisfied - 174
if($record['ir_satisfied'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ir_satisfied'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ir_satisfied'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 174;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ir_photo_id_saved - 175
if($record['ir_photo_id_saved'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ir_photo_id_saved'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ir_photo_id_saved'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 175;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ir_due_diligence - 176
if($record['ir_due_diligence'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ir_due_diligence'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ir_due_diligence'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 176;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//pep_considered - 177
if($record['pep_considered'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['pep_considered'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['pep_considered'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 177;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}


//pep_due_diligence - 178
if($record['pep_due_diligence'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['pep_due_diligence'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['pep_due_diligence'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 178;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_business_understanding - 179
if($record['ccr_business_understanding'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_business_understanding'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_business_understanding'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 179;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}



//ccr_services_understanding - 180
if($record['ccr_services_understanding'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_services_understanding'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_services_understanding'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 180;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_concerns_ownership - 181
if($record['ccr_services_understanding'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_services_understanding'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_services_understanding'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 181;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}


//ccr_confirm_integrity - 182
if($record['ccr_confirm_integrity'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_confirm_integrity'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_confirm_integrity'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 182;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_management_concerns - 183
if($record['ccr_management_concerns'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_management_concerns'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_management_concerns'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 183;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}


//ccr_financial_concerns - 184
if($record['ccr_financial_concerns'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_financial_concerns'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_financial_concerns'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 184;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_legal_environment - 185
if($record['ccr_legal_environment'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_legal_environment'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_legal_environment'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 185;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_accountant - 186
if($record['ccr_accountant'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_accountant'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_accountant'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 186;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_accountant_frequency - 187
if($record['ccr_accountant_frequency'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_accountant_frequency'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_accountant_frequency'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 187;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_firms - 188
if($record['ccr_firms'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_firms'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_firms'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 188;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_risk - 189
if($record['ccr_risk'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_risk'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_risk'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 189;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_confirm_engagement_director - 190
if($record['ccr_confirm_engagement_director'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_confirm_engagement_director'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_confirm_engagement_director'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 190;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_confirm_engagement_manager - 191
if($record['ccr_confirm_engagement_manager'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_confirm_engagement_manager'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_confirm_engagement_manager'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 191;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_exposure_regulated_business - 192
if($record['ccr_exposure_regulated_business'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_exposure_regulated_business'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_exposure_regulated_business'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 192;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}


//ccr_availability_concerns - 193
if($record['ccr_availability_concerns'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_availability_concerns'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_availability_concerns'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 193;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_partner_concerns - 194
if($record['ccr_partner_concerns'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_partner_concerns'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_partner_concerns'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 194;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_staffing_engagement - 195
if($record['ccr_staffing_engagement'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_staffing_engagement'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_staffing_engagement'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 195;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_client_connection - 196
if($record['ccr_client_connection'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_client_connection'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_client_connection'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 196;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_already_service - 197
if($record['ccr_already_service'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_already_service'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_already_service'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 197;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_staff_connection - 198
if($record['ccr_staff_connection'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_staff_connection'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_staff_connection'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 198;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_previously_acted - 199
if($record['ccr_previously_acted'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_previously_acted'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_previously_acted'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 199;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

//ccr_potential_risk - 200
if($record['ccr_potential_risk'] != ''){
    $client_crf_boolean = new ActionableBooleanData;
    if($record['ccr_potential_risk'] == 'Yes'){
        $client_crf_boolean->data = 1;
    }
    if($record['ccr_potential_risk'] == 'No'){
        $client_crf_boolean->data = 0;
    }
    $client_crf_boolean->actionable_boolean_id = 200;
    $client_crf_boolean->client_id = $record['client_id'];
    $client_crf_boolean->user_id = 1;
    $client_crf_boolean->duration = 120;
    $client_crf_boolean->created_at = '2022-03-10 00:00:00';
                $client_crf_boolean->updated_at = '2022-03-10 00:00:00';
    $client_crf_boolean->save();
}

            // $client_crf_process = new ClientProcess;
            // $client_crf_process->client_id = $record['client_id'];
            // $client_crf_process->process_id = 19;
            // $client_crf_process->step_id = 91;
            // $client_crf_process->active = 1;
            // $client_crf_process->save();
        }
    }
}