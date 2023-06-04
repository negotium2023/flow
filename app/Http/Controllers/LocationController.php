<?php

namespace App\Http\Controllers;

use App\Division;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        $divisions = Division::with('regions','regions.areas','regions.areas.offices');

        if ($request->has('q') && $request->input('q') != '') {
            $divisions->where('name', 'like', '%'.$request->input('q').'%')
            ->orwhereHas('regions', function($q) use($request) {
               $q->where('name', 'like', '%'.$request->input('q').'%');
            })->orWhereHas('regions.areas', function($q) use($request){
                $q->where('name', 'like', '%'.$request->input('q').'%');
            })->orWhereHas('regions.areas.offices', function($q) use($request){
                $q->where('name', 'like', '%'.$request->input('q').'%');
            });
        };

        $divisions->orderBy('name')->get();

        $parameters = [
            'divisions' => $divisions->orderBy('name')->get()
        ];

        return view('master_data.locations.index')->with($parameters);
    }
}
