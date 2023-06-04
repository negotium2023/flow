<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActionableVideoUploadController extends Controller
{
    public function upload(Request $request){

        $uploadedFile = $request->file('videoFile');

            $filename = time().$uploadedFile->getClientOriginalName();

        Storage::disk('public')->putFileAs(
            'files/',
            $uploadedFile,
            $filename
        );

            return response()->json(['filename'=>$filename]);

    }
}
