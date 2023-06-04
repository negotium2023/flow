<?php

namespace App\Http\Controllers;

use App\Division;
use App\Http\Requests\StoreOfficeRequest;
use App\Http\Requests\UpdateOfficeRequest;
use App\Office;

class OfficeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create()
    {
        $divisions = Division::with(
            [
                'regions' => function ($query) {
                    $query->orderBy('name');
                },
                'regions.areas' => function ($query) {
                    $query->orderBy('name');
                }
            ]
        )->orderBy('name')->get();

        $area_array = [];
        foreach ($divisions as $division) {
            foreach ($division->regions as $region) {
                foreach ($region->areas as $area) {
                    $area_array[$division->name . ' - ' . $region->name][$area->id] = $area->name;
                }
            }
        }

        return view('master_data.offices.create')->with(['areas' => $area_array]);
    }

    public function store(StoreOfficeRequest $request)
    {
        $office = new Office;
        $office->name = $request->input('name');
        $office->area_id = $request->input('area');
        $office->save();

        return redirect(route('locations.index'))->with('flash_success', 'Office captured successfully');
    }

    public function edit(Office $office)
    {
        $divisions = Division::with(
            [
                'regions' => function ($query) {
                    $query->orderBy('name');
                },
                'regions.areas' => function ($query) {
                    $query->orderBy('name');
                }
            ]
        )->orderBy('name')->get();

        $area_array = [];
        foreach ($divisions as $division) {
            foreach ($division->regions as $region) {
                foreach ($region->areas as $area) {
                    $area_array[$division->name . ' - ' . $region->name][$area->id] = $area->name;
                }
            }
        }

        return view('master_data.offices.edit')->with(['office' => $office, 'areas' => $area_array]);
    }

    public function update(UpdateOfficeRequest $request, Office $office)
    {
        $office->name = $request->input('name');
        $office->area_id = $request->input('area');
        $office->save();

        return redirect(route('locations.index'))->with('flash_success', 'Office updated successfully');
    }

    public function destroy($id)
    {
        //
    }
}
