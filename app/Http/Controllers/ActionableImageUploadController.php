<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActionableImageUploadController extends Controller
{
    public function upload(Request $request){

        $uploadedFile = $request->file('imageFile');

        $filename = time().$uploadedFile->getClientOriginalName();

        Storage::disk('public')->putFileAs(
            'files/images/',
            $uploadedFile,
            $filename
        );

        return response()->json(['filename'=>$filename]);

    }
}
