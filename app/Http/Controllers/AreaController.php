<?php

namespace App\Http\Controllers;

use App\Area;
use App\Http\Requests\StoreAreaRequest;
use App\Division;
use App\Http\Requests\UpdateAreaRequest;
use App\Office;
use App\Process;
use Illuminate\Http\Request;

class AreaController extends Controller
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
                }
            ]
        )->orderBy('name')->get();

        $region_array = [];
        foreach ($divisions as $division) {
            foreach ($division->regions as $region) {
                $region_array[$division->name][$region->id] = $region->name;
            }
        }

        return view('master_data.areas.create')->with(['regions' => $region_array]);
    }

    public function store(StoreAreaRequest $request)
    {
        $area = new Area;
        $area->name = $request->input('name');
        $area->region_id = $request->input('region');
        $area->save();

        return redirect(route('locations.index'))->with('flash_success', 'Area captured successfully');
    }

    public function edit(Area $area)
    {
        $divisions = Division::with(
            [
                'regions' => function ($query) {
                    $query->orderBy('name');
                }
            ]
        )->orderBy('name')->get();

        $region_array = [];
        foreach ($divisions as $division) {
            foreach ($division->regions as $region) {
                $region_array[$division->name][$region->id] = $region->name;
            }
        }

        return view('master_data.areas.edit')->with(['area' => $area, 'regions' => $region_array]);
    }

    public function update(UpdateAreaRequest $request, Area $area)
    {
        $area->name = $request->input('name');
        $area->region_id = $request->input('region');
        $area->save();

        return redirect(route('locations.index'))->with('flash_success', 'Area updated successfully');
    }

    public function destroy($id)
    {
        //
    }

    public function getOffices(Request $request){

        $area = [];

        $areas = $request->input('areas');

        $offices = Office::whereHas('area',function ($q) use ($areas) {
            $q->whereIn('id', $areas);
        })->get();

        //dd($cps);
        foreach($offices as $office){
                $area[$office->area->name][] = [
                    "id" => $office->id,
                    "name" => $office->name
                ];
        }

        return response()->json($area);
    }
}
