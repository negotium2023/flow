<?php

namespace App\Http\Controllers\API;

use App\Card;
use App\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $section = Section::where('board_id', $request->board_id)->orderBy('id')->first();

        $cards = null;
        if(isset($section->id) && $section->id > 0){
            $cards = Card::orderBy('id')->where('section_id', $section->id)->get();
        } else {
            $section = new Section();
            $section->name = 'Section 01';
            $section->board_id = $request->board_id;
            $section->created_at = now();
            $section->creator_id = 1;
            $section->status_id = 1;
            $section->save();

            $cards = Card::orderBy('id')->where('section_id', $section->id)->get();
        }

        return response()->json(['success' => 1, 'message' => 'Cards retrieved successfully.', 'data' => $cards]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'board_id' => 'required|integer',
            'client_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation failed',
                'data' => $validator->errors()
            ], 422);
        }

        $section = Section::where('board_id', $request->board_id)->orderBy('id')->first();
        if(!isset($section->id)){
            $section = new Card();
            $section->name = $request->name;
            $section->board_id = $request->board_id;
            $section->created_at = now();
            $section->creator_id = 1;
            $section->status_id = 1;
            $section->save();
        }

        $card = new Card();
        $card->name = $request->name;
        $card->section_id = $section->id;
        $card->assignee_id = 1;
        $card->priority_id = 1;
        $card->enabled = 1;
        $card->description = 'Testing 123.';
        $card->client_id = $request->client_id;
        $card->created_at = now();
        $card->due_date = date('Y-m-d', strtotime('2022-12-31'));
        $card->creator_id = 1;
        $card->status_id = 1;
        $card->save();

        return response()->json(['success' => 1, 'message' => 'Card successfully created.', 'data' => $card]);
    }
}
