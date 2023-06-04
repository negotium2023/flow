<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Client;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getAvatar(Request $request)
    {
        if ($request->has('q') && file_exists(storage_path('app/avatars/' . $request->input('q')))) {
            return response()->file(storage_path('app/avatars/' . $request->input('q')));
        } else if(file_exists(public_path('storage/avatars/'.$request->input('q')))) {
            return response()->file(public_path('storage/avatars/' . $request->input('q')));
        } else {
            return response()->file('assets/default.png');
        }
    }

    public function getDocument(Request $request)
    {

        if (file_exists(storage_path('app/documents/' . $request->input('q')))) {
            return response()->file(storage_path('app/documents/' . $request->input('q')));
        } else if(file_exists(public_path('storage/documents/processed_applications'.$request->input('q')))) {
            return response()->file(public_path('storage/documents/processed_applications'.$request->input('q')));
        }else{
            abort(404);
        }
    }

    public function getForm(Request $request)
    {
        $client = Client::find($request->input('client'));

        // return ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.str_replace(' ','_',$client->last_name)));
        if (file_exists(storage_path('app/forms/processedforms/'.($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.str_replace(' ','_',$client->last_name))).'/' . $request->input('q')))) {
            return response()->file(storage_path('app/forms/processedforms/'.($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.str_replace(' ','_',$client->last_name))).'/' . $request->input('q')));
        }else{
            abort(404);
        }
    }

    public function getCrf(Request $request)
    {
        if (file_exists(storage_path('app/crf/' . $request->input('q')))) {
            return response()->file(storage_path('app/crf/' . $request->input('q')));
        } else {
            abort(404);
        }
    }

    public function getTemplate(Request $request)
    {
        if (file_exists(storage_path('app/templates/' . $request->input('q')))) {
            return response()->file(storage_path('app/templates/' . $request->input('q')));
        } else {
            abort(404);
        }
    }
}
