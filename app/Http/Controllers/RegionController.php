<?php

namespace App\Http\Controllers;

use App\Division;
use App\Http\Requests\StoreRegionRequest;
use App\Http\Requests\UpdateRegionRequest;
use App\Region;

class RegionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        return view('master_data.regions.create')->with(['divisions' => Division::orderBy('name')->pluck('name', 'id')]);
    }

    public function store(StoreRegionRequest $request)
    {
        $region = new Region;
        $region->name = $request->input('name');
        $region->division_id = $request->input('division');
        $region->save();

        return redirect(route('locations.index'))->with('flash_success', 'Region captured successfully');
    }

    public function edit(Region $region)
    {
        return view('master_data.regions.edit')->with(['region' => $region, 'divisions' => Division::orderBy('name')->pluck('name', 'id')]);
    }

    public function update(UpdateRegionRequest $request, Region $region)
    {
        $region->name = $request->input('name');
        $region->division_id = $request->input('division');
        $region->save();

        return redirect(route('locations.index'))->with('flash_success', 'Region updated successfully');
    }

    public function destroy($id)
    {
        //
    }
}
