<?php

namespace App\Http\Controllers;

use App\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->has('id') && $request->input('id') != ''){
            $section = Section::find($request->input('id'));
            $section->name = $request->name;
            $section->board_id = $request->board_id;
            $section->creator_id = Auth::id();
            $section->status_id = 1;
            $section->created_at = now();
            $section->save();

            $section['editFlag'] = 0;
            $section['updateFlag'] = 0;

            return ['message' => 'Section successfully updated', 'section' => $section->load('cards.discussions')];

        } else {
            $section = new Section();
            $section->name = $request->name;
            $section->board_id = $request->board_id;
            $section->creator_id = Auth::id();
            $section->status_id = 1;
            $section->created_at = now();
            $section->save();

            $section['editFlag'] = 0;
            $section['updateFlag'] = 0;

            return ['message' => 'Section successfully saved', 'section' => $section->load('cards.discussions')];
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function show(Section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function edit(Section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Section $section)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $section = Section::destroy($request->input('section_id'));

        return ['message' => 'Section successfully deleted'];
    }
}
