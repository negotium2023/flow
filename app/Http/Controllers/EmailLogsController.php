<?php

namespace App\Http\Controllers;

use App\EmailLogs;
use Illuminate\Http\Request;

class EmailLogsController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
        //return $this->middleware('auth')->except('index');
    }

    public function index(Request $request)
    {
        $logs = EmailLogs::orderBy('date','DESC');

        if($request->has('q') && $request->input('q') != ''){
            $logs->where('to','like','%'.$request->input('q').'%')
                ->orWhere('subject','like','%'.$request->input('q').'%')
                ->orWhere('date','like','%'.$request->input('q').'%')
                ->orWhere('body','like','%'.$request->input('q').'%');
        }

        $logs = $logs->get();

        $parameters = [
            'emails' => $logs
        ];

        return view('emaillogs.index')->with($parameters);
    }

    public function show($email_id){
        $logs = EmailLogs::where('id',$email_id)->get();

        $parameters = [
            'emails' => $logs
        ];

        return view('emaillogs.show')->with($parameters);
    }
}
