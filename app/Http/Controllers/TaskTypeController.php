<?php

namespace App\Http\Controllers;

use App\TaskType;
use Illuminate\Http\Request;

class TaskTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){

        $task_type = TaskType::orderBy('id');

        if ($request->has('q')) {
            $task_type->where('name', 'LIKE', "%" . $request->input('q') . "%");
        }

        $parameters = [
            'task_type' => $task_type->get()
        ];


        return view('master_data.task_types.index')->with($parameters);
    }

    public function create(){

        return view('master_data.task_types.create');
    }

    public function store(Request $request){
        $template = new TaskType();
        $template->name = $request->input('name');
        $template->created_by = auth()->id();
        $template->save();

        return redirect(route('task_types.index'))->with('flash_success', 'Task Type captured successfully');
    }

    public function show($templateid){
    }

    public function edit($tasktypeid){

        $task_type = TaskType::where('id',$tasktypeid)->get();

        $parameters = [
            'task_type' => $task_type
        ];

        return view('master_data.task_types.edit')->with($parameters);
    }

    public function update(Request $request,$tasktypeid){
        $template = TaskType::find($tasktypeid);
        $template->name = $request->input('name');
        $template->created_by = auth()->id();
        $template->save();

        return redirect(route('task_types.index'))->with('flash_success', 'Task Type saved successfully');
    }

    public function destroy($id)
    {
        TaskType::destroy($id);
        return redirect()->route('task_types.index')
            ->with('success','Task Type deleted successfully');
    }
}
