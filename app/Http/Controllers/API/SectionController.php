<?php

namespace App\Http\Controllers\API;

use App\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $sections = Section::orderBy('id')->where('board_id', $request->board_id)->get();

        return response()->json(['success' => 1, 'message' => 'Sections retrieved successfully.', 'data' => $sections]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'board_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation failed',
                'data' => $validator->errors()
            ], 422);
        }

        $section = new Section();
        $section->name = $request->name;
        $section->board_id = $request->board_id;
        $section->created_at = now();
        $section->creator_id = 1;
        $section->status_id = 1;
        $section->save();

        return response()->json(['success' => 1, 'message' => 'Sections successfully created.', 'data' => $section]);
    }
}
