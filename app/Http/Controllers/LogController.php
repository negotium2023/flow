<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use App\Log;
use App\ActivityLog;
use App\User;

class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function activityLog($id){
        $log = Log::find($id);

        $parameters = [
            'user' => User::select(['first_name', 'last_name'])->where('id', $log->user_id)->get(),
            'client' =>  Client::select(['id','company'])->where('id', $log->client_id)->get(),
            'activities_log' => ActivityLog::where('log_id', '=', $id)->get()
        ];

        return view('logs.index')->with($parameters);
    }
}
