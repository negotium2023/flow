<?php

namespace App\Http\Controllers;

use App\BusinessUnits;
use Illuminate\Http\Request;

class BusinessUnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){
        $businessunits = BusinessUnits::orderBy('name')->get();

        $parameters = [
            'businessunits' => $businessunits
        ];

        return view('businessunits.index')->with($parameters);
    }

    public function create(Request $request)
    {
        return view('businessunits.create');
    }

    public function store(Request $request)
    {
        $business_unit = new BusinessUnits();
        $business_unit->name = $request->input('name');
        $business_unit->save();

        return redirect(route('businessunits.index'))->with('flash_success', 'Business Unit captured successfully');
    }

    public function edit($businessunit){
        $businessunits = BusinessUnits::where('id',$businessunit)->get();

        $parameters = [
            'businessunits' => $businessunits
        ];

        return view('businessunits.edit')->with($parameters);
    }

    public function show($businessunit){
        $businessunits = BusinessUnits::where('id',$businessunit)->get();

        $parameters = [
            'businessunits' => $businessunits
        ];

        return view('businessunits.show')->with($parameters);
    }

    public function update(Request $request,$businessunit)
    {
        $business_unit = BusinessUnits::find($businessunit);
        $business_unit->name = $request->input('name');
        $business_unit->save();

        return redirect(route('businessunits.index'))->with('flash_success', 'Business Unit captured successfully');
    }

    public function destroy($businessunit){
        BusinessUnits::destroy($businessunit);
        return redirect()->route("businessunits.index")->with('flash_success','Business Unit deleted successfully');
    }
}
