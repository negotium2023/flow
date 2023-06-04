<?php

namespace App\Http\Controllers;

use App\BusinessUnits;
use App\Committee;
use Illuminate\Http\Request;

class CommitteeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){
        $committees = Committee::orderBy('name')->get();

        $parameters = [
            'committees' => $committees
        ];

        return view('committees.index')->with($parameters);
    }

    public function create(Request $request)
    {
        return view('committees.create');
    }

    public function store(Request $request)
    {
        $committee = new Committee();
        $committee->name = $request->input('name');
        $committee->save();

        return redirect(route('committees.index'))->with('flash_success', 'Committee captured successfully');
    }

    public function edit($committee){
        $committees = Committee::where('id',$committee)->get();

        $parameters = [
            'committees' => $committees
        ];

        return view('committees.edit')->with($parameters);
    }

    public function show($committee){
        $committees = Committee::where('id',$committee)->get();

        $parameters = [
            'committees' => $committees
        ];

        return view('committees.show')->with($parameters);
    }

    public function update(Request $request,$committee)
    {
        $committee = Committee::find($committee);
        $committee->name = $request->input('name');
        $committee->save();

        return redirect(route('committees.index'))->with('flash_success', 'Committee updated successfully');
    }

    public function destroy($committee){
        Committee::destroy($committee);
        return redirect()->route("committees.index")->with('flash_success','Committee deleted successfully');
    }
}
