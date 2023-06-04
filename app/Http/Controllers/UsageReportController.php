<?php

namespace App\Http\Controllers;

use App\UsageLogs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UsageReportController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function index(Request $request){

        if(($request->has('from') && $request->input('from') != '') && ($request->has('to') && $request->input('to') != '')){
            $startDate = Carbon::parse($request->input('from'))->format('Ymd');
            $endDate = Carbon::parse($request->input('to'))->format('Ymd');
        } else {
            $now = Carbon::now();
            $startDate = $now->subDays(5)->format('Ymd');
            $endDate = Carbon::parse($startDate)->addDays(7)->format('Ymd');
        }

        $date_diff = Carbon::parse($startDate)->diffInDays($endDate);
        $date_array = array();

        for($i=0;$i<=$date_diff;$i++){
            array_push($date_array,Carbon::parse($startDate)->addDay($i)->format("Y-m-d"));
        }

        $users_list = User::select('id',DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->pluck('full_name','id')->prepend('Select','0');


        if($request->has('user_search') && $request->input('user_search') != 0){
            $users =User::select('id',DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->where('id',$request->input('user_search'))->get();
        } else {
            $users = User::select('id',DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->get();
        }


        if($request->has('user_search') && $request->input('user_search') != 0){
            $usages = UsageLogs::select(DB::raw("COUNT(user_name) as cnt"),'user_id',DB::Raw('DATE(created_at) day'))->where('user_id',$request->input('user_search'))->whereBetween('created_at', [$startDate, $endDate])->groupBy('day')->groupBy('user_id')->orderBy('user_id')->get();
        } else {
            $usages = UsageLogs::select(DB::raw("COUNT(user_name) as cnt"),'user_id',DB::Raw('DATE(created_at) day'))->whereBetween('created_at', [$startDate, $endDate])->groupBy('day')->groupBy('user_id')->orderBy('user_id')->get();
        }

        $usage_array = array();
        foreach($usages as $usage){
            $usage_array[$usage->user_id][(int)str_replace('-','',$usage->day)] = $usage->cnt;
        }

        $parameters = [
            'date_diff' => $date_diff,
            'date_range' => $date_array,
            'users_list' => $users_list,
            'users' => $users,
            'usage' => $usage_array,
            'week_start' => $startDate,
            'week_end' => $endDate,
        ];

        return view('reports.usage')->with($parameters);

    }
}
