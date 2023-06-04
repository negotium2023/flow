<?php

namespace App\Http\Controllers;

use App\Activity;
use App\Client;
use App\Config;
use App\Process;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GraphController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function newClients(Request $request)
    {
        return view('graphs.newclients');   
    }
    
    public function targetData(Request $request)
    {
        return view('graphs.clienttargetdata');   
    }
    
    public function yearlyComparison(Request $request)
    {
        return view('graphs.yearlycomparison');   
    }
}