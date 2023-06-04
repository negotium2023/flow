<?php

namespace App\Http\Controllers;

use App\Process;
use App\RelatedPartyProcess;
use App\Http\Requests\RelatedPartyProcessRequest;

class RelatedPartyProcessController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $related_party_processes = RelatedPartyProcess::orderBy('id')->get();
        $parameters = [
            'related_party_processes' => $related_party_processes
        ];

        return view('relatedpartyprocess.index')->with($parameters);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $related_party_processes_drop_down = Process::orderBy('name')->whereNotNull('name')->where('name', '!=', '')->pluck('name', 'id')->prepend('Process', '');
        $parameters = [
            'related_party_processes_drop_down' => $related_party_processes_drop_down
        ];

        return view('relatedpartyprocess.create')->with($parameters);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RelatedPartyProcessRequest $request)
    {
        $related_party_process = new RelatedPartyProcess();
        $related_party_process->process_id = $request->process_id;
        $related_party_process->save();

        return redirect(route('relatedpartyprocess.index'))->with('flash_success', 'Related party process successfully saved.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RelatedPartyProcess  $relatedPartyProcess
     * @return \Illuminate\Http\Response
     */
    public function show($related_party_process_id)
    {
        $related_party_process = RelatedPartyProcess::find($related_party_process_id);
        $related_party_processes_drop_down = Process::orderBy('name')->whereNotNull('name')->where('name', '!=', '')->pluck('name', 'id')->prepend('Process', '');
        $parameters = [
            'related_party_process' => $related_party_process,
            'related_party_processes_drop_down' => $related_party_processes_drop_down
        ];

        return view('relatedpartyprocess.show')->with($parameters);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RelatedPartyProcess  $relatedPartyProcess
     * @return \Illuminate\Http\Response
     */
    public function edit($related_party_process_id)
    {
        $related_party_process = RelatedPartyProcess::find($related_party_process_id);
        $related_party_processes_drop_down = Process::orderBy('name')->whereNotNull('name')->where('name', '!=', '')->pluck('name', 'id')->prepend('Process', '');
        $parameters = [
            'related_party_process' => $related_party_process,
            'related_party_processes_drop_down' => $related_party_processes_drop_down
        ];

        return view('relatedpartyprocess.edit')->with($parameters);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RelatedPartyProcess  $relatedPartyProcess
     * @return \Illuminate\Http\Response
     */
    public function update(RelatedPartyProcessRequest $request, $related_party_process_id)
    {
        $related_party_process = RelatedPartyProcess::find($related_party_process_id);
        $related_party_process->process_id = $request->process_id;
        $related_party_process->save();

        return redirect(route('relatedpartyprocess.index'))->with('flash_success', 'Related party process successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RelatedPartyProcess  $relatedPartyProcess
     * @return \Illuminate\Http\Response
     */
    public function destroy(RelatedPartyProcess $relatedPartyProcess)
    {
        //
    }
}
