<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SLAReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){

        return view('reports.sla');
    }

}
