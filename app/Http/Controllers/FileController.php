<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Template;
use App\Document;

class FileController extends Controller
{
    public function __construct()
    {
        //$this->middleware('auth');
    }

    function index()
    {
        return view('ajax_upload');
    }

    function action(Request $request)
    {
        $validation = Validator::make($request->all(), [
            /*'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'*/
            'file' => 'required'
        ]);
        if($validation->passes())
        {
            $image = $request->file('file');
            $new_name = rand() . '.' . $image->getClientOriginalExtension();
            $ext = pathinfo($new_name, PATHINFO_EXTENSION);
            if($request->input('activitytype') == 'document') {
                $image->move(storage_path('app/documents'), $new_name);

                $document = new Document;
                $document->name = $request->input('filename');
                $document->file = $new_name;
                $document->user_id = (auth()->id() != null ? auth()->id() : '0');
                $document->client_id = $request->input('clientid');
                if($request->has('relatedpartyid') && $request->input('relatedpartyid') != ''){
                    $document->related_party_id = $request->input('relatedpartyid');
                }
                $document->save();

                return response()->json([
                    'message'   => 'Document Upload Successfull',
                    'class_name'  => 'alert-success',
                    'template_id' => $document->id,
                    'template_name' => $document->name
                ]);
            }
            if($request->input('activitytype') == 'template') {
                $image->move(storage_path('app/templates'), $new_name);

                $template = new Template;
                $template->name = $request->input('filename');
                $template->file = $new_name;
                $template->user_id = (auth()->id() != null ? auth()->id() : '0');
                $template->save();

                return response()->json([
                    'message'   => 'Template Upload Successfull',
                    'class_name'  => 'alert-success',
                    'template_id' => $template->id,
                    'template_name' => $template->name
                ]);
            }

        }
        else
        {
            return response()->json([
                'message'   => $validation->errors()->all(),
                'class_name'  => 'alert-danger'
            ]);
        }
    }
}