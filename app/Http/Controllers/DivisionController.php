<?php

namespace App\Http\Controllers;

use App\Division;
use App\Http\Requests\StoreDivisionRequest;
use App\Http\Requests\UpdateDivisionRequest;

class DivisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('master_data.divisions.create');
    }

    public function store(StoreDivisionRequest $request)
    {
        $division = new Division();
        $division->name = $request->input('name');
        $division->save();

        return redirect(route('locations.index'))->with('flash_success','Division captured successfully');
    }

    public function edit(Division $division)
    {
        return view('master_data.divisions.edit')->with(['division'=>$division]);
    }

    public function update(UpdateDivisionRequest $request, Division $division)
    {
        $division->name = $request->input('name');
        $division->save();

        return redirect(route('locations.index'))->with('flash_success','Division updated successfully');
    }

    public function destroy($id)
    {
        //
    }
}
