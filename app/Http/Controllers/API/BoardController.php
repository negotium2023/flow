<?php

namespace App\Http\Controllers\API;

use App\Board;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BoardController extends Controller
{
    public function index()
    {
        $boards = Board::orderBy('id')->get();

        return response()->json(['success' => 1, 'message' => 'Boards retrieved successfully.', 'data' => $boards]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'message' => 'Validation failed',
                'data' => $validator->errors()
            ], 422);
        }

        $board = new Board();
        $board->name = $request->name;
        $board->created_at = now();
        $board->creator_id = 1;
        $board->office_id = 1;
        $board->status_id = 1;
        $board->save();

        return response()->json(['success' => 1, 'message' => 'Board successfully created.', 'data' => $board]);
    }
}
