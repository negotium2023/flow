<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsightController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){

        $parameters = [

        ];


        return view('insight')->with($parameters);
    }
}
