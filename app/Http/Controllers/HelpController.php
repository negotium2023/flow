<?php

namespace App\Http\Controllers;

use App\Config;
use App\Mail\HelpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HelpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('help.create');
    }

    public function store(Request $request)
    {
        $config = Config::first();

        Mail::to($config->support_email)->send(new HelpMail(auth()->user(), $request->input('page'), $request->input('comment')));

        return redirect()->back()->with(['flash_success' => "Request sent successfully."]);
    }
}
