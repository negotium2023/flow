<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProcessGroup;

class ProcessGroupController extends Controller
{
    public function __construct()
    {
        return $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $process_type_id = $request->has('t') ? $request->input('t') : 1;

        if ($request->has('q')) {
            $process_groups = ProcessGroup::where('name', 'LIKE', "%" . $request->input('q') . "%")->orderBy('id')->get();
        } else {
            $process_groups = ProcessGroup::orderBy('id')->get();
        }


        $parameters = [
            'processes' => $process_groups,
            'type_name' => $process_type_id == 1 ? 'Processes' : 'Related Parties Structure',
            'process_type_id' => $process_type_id
        ];

        return view('processesgroup.index')->with($parameters);
    }
}
