<?php

namespace App\Http\Controllers;

use App\Client;
use App\RelatedParty;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\DB;
use App\UsageLogs;

class ProductivityReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){

        $now = Carbon::now();
        //$startDate = $now->startOfWeek(Carbon::MONDAY)->format('Ymd');
        $startDate = Carbon::parse('2020-02-13')->format('Ymd');
        $endDate = $now->endOfWeek(Carbon::SUNDAY)->format('Ymd');

        // Apply date filters should they be included in the request.
        if(($request->has('from') && $request->input('from') != '') && ($request->has('to') && $request->input('to') != '')){
            $startDate = Carbon::parse($request->input('from'))->format('Ymd');
            $endDate = Carbon::parse($request->input('to'))->format('Ymd');
        }

        $date_diff = Carbon::parse($startDate)->diffInDays($endDate);
        $date_array = array();

        // Loop over dates and build up an array.
        for($i=0;$i<=$date_diff;$i++){
            array_push($date_array,Carbon::parse($startDate)->addDay($i)->format("D d M"));
        }
        
        // Pluck the User name and id for select list.
        $users_list = User::select('id', \DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->pluck('full_name','id')->prepend('Select','0');

        // Apply Search filter for users, otherwise return all.
        if($request->has('user_search') && $request->input('user_search') != 0){
            $users =User::select('id', \DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->where('id',$request->input('user_search'))->get();
        } else {
            $users = User::select('id', \DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->get();
        }
        
        $usage_array = array();

        // return all Clients for user
        foreach ($users as $user) {
            $clients = Client::with('related_parties')
                                ->select('*',\DB::Raw('DATE(instruction_date) day'))
                                ->whereNotNull('assigned_date')
                                ->whereNotNull('instruction_date')
                                ->where('instruction_date','<=',Carbon::parse($endDate)->format('Y-m-d'))
                                ->where('instruction_date','>=',Carbon::parse($startDate)->format('Y-m-d'))
                                ->where('consultant_id',$user->id)
//                                ->where('is_progressing', 1)
                                ->get();
            
            // Loop over found clients
            foreach ($clients as $client) {

                
                 // Primes the usage_array variable with the user id and the dates under filter
                for($i = 0; $i < Carbon::parse($client->day)->diffInDays($endDate)+1; $i++) {
                    if (!isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))]['completed'])) {
                        $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))]['completed'] = 0;
                    }
                    if (!isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))]['assigned'])) {
                        $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))]['assigned'] = 0;
                    }
                }

                /**
                 * The client and related party counts for 'completed' and 'assigned' are then tallied up and written 
                 * back to usage_array
                 */
                if ($client->completed) {

                    for ($i = 0; $i < Carbon::parse($client->day)->diffInDays($endDate) + 1; $i++) {

                        if (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->instruction_date)->addDay($i)))]['assigned'])) {
                            if ((int)str_replace('-', '', (Carbon::parse($client->day))) >= (int)str_replace('-', '', (Carbon::parse($startDate))) && ((int)str_replace('-', '', (Carbon::parse($client->completed_date)->addDay($i))) <= (int)str_replace('-', '', (Carbon::parse($startDate)->addDay($i))))) {

                                $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))] ['assigned'] = $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))] ['assigned'];

                            } elseif ((int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i))) >= (int)str_replace('-', '', (Carbon::parse($startDate))) && (int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i))) <= (int)str_replace('-', '', (Carbon::parse($client->completed_date)))) {

                                $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))] ['assigned'] = $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))]['assigned'] + 1;

                           }
                        }
                    }
                }

                foreach ($client->related_parties as $related_party) {
                    if ($related_party->completed) {
                        for ($i = 0; $i < Carbon::parse($related_party->instruction_date)->diffInDays($endDate) + 1; $i++) {

                                if ((int)str_replace('-', '', (Carbon::parse($related_party->instruction_date))) >= (int)str_replace('-', '', (Carbon::parse($startDate))) && ((int)str_replace('-', '', (Carbon::parse($related_party->completed_date))) <= (int)str_replace('-', '', (Carbon::parse($startDate)->addDay($i))))) {

                                    if (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i)))]['assigned'])) {

                                        $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i)))] ['assigned'] = $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i)))] ['assigned'];

                                    }
                                } else {
                                    if (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i)))]['assigned']) && (int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i))) <= (int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))) {

                                        $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i)))] ['assigned'] = $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i)))] ['assigned'] + 1;

                                    }
                                }
                        }
                    }

                }

                if ($client->completed != 0) {
                    for ($i = 0; $i < Carbon::parse($startDate)->diffInDays($endDate)+1; $i++) {

                        if ((int)str_replace('-', '', (Carbon::parse($client->completed_date))) == (int)str_replace('-', '', (Carbon::parse($startDate)->addDay($i)))) {

                            if (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed'])) {
                                $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed'] = $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed'] + 1;
                            } else {
                                $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed'] = 1;
                            }
                        } else {
                            $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed'] = (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed']) ? $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed'] : 0);
                        }
                    }
                }

                foreach ($client->related_parties as $related_party){
                    if ($related_party->completed_date != null) {
                        for ($i = 0; $i < Carbon::parse($startDate)->diffInDays($endDate)+1; $i++) {

                            if ((int)str_replace('-', '', (Carbon::parse($related_party->completed_date))) == (int)str_replace('-', '', (Carbon::parse($startDate)->addDay($i)))) {

                                if (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed'])) {
                                    $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed'] = $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed'] + 1;
                                } else {
                                    $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed'] = 1;
                                }
                            } else {
                                $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed'] = (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed']) ? $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed'] : 0);
                            }
                        }
                    }
                }

                asort($usage_array);
            }
        }

        // the $totals array reorganises the $usage_array for display in HighCharts.
        $totals = [];
        
        foreach($usage_array as $usage){
            foreach ($usage as $keyo=>$value){
                $key = $keyo;

                $totals[$key] = array('assigned'=>(isset($totals[$key]['assigned']) ? $totals[$key]['assigned'] : 0),'completed'=>(isset($totals[$key]['completed']) ? $totals[$key]['completed'] : 0));

                if(isset($value['assigned'])) {
                    $totals[$key]['assigned'] = $totals[$key]['assigned'] + $value['assigned'];
                }
                if(isset($value['completed'])) {
                    $totals[$key]['completed'] = $totals[$key]['completed'] + $value['completed'];
                }
            }
        }

        ksort($totals);

        $parameters = [
            'date_diff' => $date_diff,
            'date_range' => $date_array,
            'users_list' => $users_list,
            'users' => $users,
            'usersa' => collect($users)->toArray(),
            'usage' => $usage_array,
            'week_start' => $startDate,
            'week_end' => $endDate,
            'totals' => $totals
        ];
        
        return view('reports.productivity')->with($parameters);
    }

    public function ajaxCall(Request $request){
        if(($request->has('from') && $request->input('from') != '') && ($request->has('to') && $request->input('to') != '')){
            $startDate = Carbon::parse($request->input('from'))->format('Ymd');
            $endDate = Carbon::parse($request->input('to'))->format('Ymd');
        } else {
            $now = Carbon::now();
            $startDate = $now->startOfWeek(Carbon::MONDAY)->format('Ymd');
            $endDate = $now->endOfWeek(Carbon::SUNDAY)->format('Ymd');
        }

        $date_diff = Carbon::parse($startDate)->diffInDays($endDate);
        $date_array = array();

        for($i=0;$i<=$date_diff;$i++){
            array_push($date_array,Carbon::parse($startDate)->addDay($i)->format("Y-m-d"));
        }

        if($request->has('allVal')){
            if(is_array($request->input('allVal'))){
                $users = User::select('id', DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->whereIn(DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),'',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50)))"), $request->input('allVal'))->get();
            } else {
                $users = User::select('id', DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->where(DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),'',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50)))"), $request->input('allVal'))->get();
            }
        } else {
            $users = User::select('id',DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->get();
        }

        $usage_array = array();
        foreach ($users as $user) {
            $clients = Client::with('related_parties')->select('*',DB::Raw('DATE(instruction_date) day'))->whereNotNull('assigned_date')->whereNotNull('instruction_date')->where('instruction_date','<=',Carbon::parse($endDate)->format('Y-m-d'))->where('instruction_date','>=',Carbon::parse($startDate)->format('Y-m-d'))->where('consultant_id',$user->id)->where('is_progressing', 1)->get();
            foreach ($clients as $client) {

                for($i = 0; $i < Carbon::parse($client->day)->diffInDays($endDate)+1; $i++) {
                    if (!isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))]['completed'])) {
                        $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))]['completed'] = 0;
                    }
                    if (!isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))]['assigned'])) {
                        $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))]['assigned'] = 0;
                    }
                }

                if ($client->completed) {

                    for ($i = 0; $i < Carbon::parse($client->day)->diffInDays($endDate) + 1; $i++) {
                       
                        if (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->instruction_date)->addDay($i)))]['assigned'])) {
                            if ((int)str_replace('-', '', (Carbon::parse($client->day))) >= (int)str_replace('-', '', (Carbon::parse($startDate))) && ((int)str_replace('-', '', (Carbon::parse($client->completed_date)->addDay($i))) <= (int)str_replace('-', '', (Carbon::parse($startDate)->addDay($i))))) {

                                $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))] ['assigned'] = $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))] ['assigned'];

                            } elseif ((int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i))) >= (int)str_replace('-', '', (Carbon::parse($startDate))) && (int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i))) <= (int)str_replace('-', '', (Carbon::parse($client->completed_date)))) {

                                $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))] ['assigned'] = $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->day)->addDay($i)))]['assigned'] + 1;

                            }
                        }
                    }
                }

                foreach ($client->related_parties as $related_party) {
                    if ($related_party->completed) {
                        for ($i = 0; $i < Carbon::parse($related_party->instruction_date)->diffInDays($endDate) + 1; $i++) {

                            if ((int)str_replace('-', '', (Carbon::parse($related_party->instruction_date))) >= (int)str_replace('-', '', (Carbon::parse($startDate))) && ((int)str_replace('-', '', (Carbon::parse($related_party->completed_date))) <= (int)str_replace('-', '', (Carbon::parse($startDate)->addDay($i))))) {

                                if (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i)))]['assigned'])) {

                                    $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i)))] ['assigned'] = $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i)))] ['assigned'];

                                }
                            } else {
                                if (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i)))]['assigned']) && (int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i))) <= (int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))) {

                                    $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i)))] ['assigned'] = $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->instruction_date)->addDay($i)))] ['assigned'] + 1;

                                }
                            }
                        }
                    }

                }

                if ($client->completed != 0) {
                    for ($i = 0; $i < Carbon::parse($startDate)->diffInDays($endDate)+1; $i++) {
                        
                        if ((int)str_replace('-', '', (Carbon::parse($client->completed_date))) == (int)str_replace('-', '', (Carbon::parse($startDate)->addDay($i)))) {

                            if (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed'])) {
                                $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed'] = $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed'] + 1;
                            } else {
                                $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed'] = 1;
                            }
                        } else {
                            $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed'] = (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed']) ? $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($client->completed_date)))]['completed'] : 0);
                        }
                    }
                }

                foreach ($client->related_parties as $related_party){
                    if ($related_party->completed_date != null) {
                        for ($i = 0; $i < Carbon::parse($startDate)->diffInDays($endDate)+1; $i++) {

                            if ((int)str_replace('-', '', (Carbon::parse($related_party->completed_date))) == (int)str_replace('-', '', (Carbon::parse($startDate)->addDay($i)))) {

                                if (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed'])) {
                                    $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed'] = $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed'] + 1;
                                } else {
                                    $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed'] = 1;
                                }
                            } else {
                                $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed'] = (isset($usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed']) ? $usage_array[$user->id][(int)str_replace('-', '', (Carbon::parse($related_party->completed_date)))]['completed'] : 0);
                            }
                        }
                    }
                }

                asort($usage_array);
            }

        }

        $totals = [];

        foreach($usage_array as $usage){
            foreach ($usage as $keyo=>$value){
                $key = $keyo;

                $totals[$key] = array('assigned'=>(isset($totals[$key]['assigned']) ? $totals[$key]['assigned'] : 0),'completed'=>(isset($totals[$key]['completed']) ? $totals[$key]['completed'] : 0));

                if(isset($value['assigned'])) {
                    $totals[$key]['assigned'] = $totals[$key]['assigned'] + $value['assigned'];
                }
                if(isset($value['completed'])) {
                    $totals[$key]['completed'] = $totals[$key]['completed'] + $value['completed'];
                }
            }
        }

        ksort($totals);

        $parameters = [
            'message' => 'Success',
            'date_diff' => $date_diff,
            'date_range' => $date_array,
            'users' => $users,
            'usersa' => collect($users)->toArray(),
            'usage' => $usage_array,
            'week_start' => $startDate,
            'week_end' => $endDate,
            'totals' => $totals
        ];

        return response()->json($parameters);
    }
}
