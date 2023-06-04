<?php

namespace App\Http\Controllers;

use App\BusinessUnits;
use App\Committee;
use App\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){
        $projects = Project::orderBy('name')->get();

        $parameters = [
            'projects' => $projects
        ];

        return view('projects.index')->with($parameters);
    }

    public function create(Request $request)
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $project = new Project();
        $project->name = $request->input('name');
        $project->save();

        return redirect(route('projects.index'))->with('flash_success', 'Project captured successfully');
    }

    public function edit($project){
        $projects = Project::where('id',$project)->get();

        $parameters = [
            'projects' => $projects
        ];

        return view('projects.edit')->with($parameters);
    }

    public function show($project){
        $projects = Project::where('id',$project)->get();

        $parameters = [
            'projects' => $projects
        ];

        return view('projects.show')->with($parameters);
    }

    public function update(Request $request,$project)
    {
        $projects = Project::find($project);
        $projects->name = $request->input('name');
        $projects->save();

        return redirect(route('projects.index'))->with('flash_success', 'Project updated successfully');
    }

    public function destroy($project){
        Project::destroy($project);
        return redirect()->route("projects.index")->with('flash_success','Project deleted successfully');
    }
}
