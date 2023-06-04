<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EmailSignature;
use App\Http\Requests\StoreEmailSignature;
use App\Http\Requests\UpdateEmailSignature;


class EmailSignatureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){

        $signature = EmailSignature::where('user_id',auth()->id())->orderBy('id')->get();

        $parameters = [
            'signature' => $signature
        ];


        return view('emailsignatures.index')->with($parameters);
    }

    public function create(){

        return view('emailsignatures.create');
    }

    public function store(StoreEmailSignature $request){
        $template = new EmailSignature();
        $template->name = $request->input('name');
        $template->template_content = $request->input('content');
        $template->user_id = auth()->id();
        $template->save();

        return redirect(route('emailsignatures.index'))->with('flash_success', 'Email Signature captured successfully');
    }

    public function show($templateid){
        $template = EmailSignature::where('id',$templateid)->get();

        $parameters = [
            'template' => $template
        ];

        return view('emailsignatures.show')->with($parameters);
    }

    public function edit($templateid){

        $template = EmailSignature::where('id',$templateid)->get();

        $parameters = [
            'template' => $template
        ];

        return view('emailsignatures.edit')->with($parameters);
    }

    public function update(UpdateEmailSignature $request,$templateid){
        $template = EmailSignature::find($templateid);
        $template->name = $request->input('name');
        $template->template_content = $request->input('content');
        $template->user_id = auth()->id();
        $template->save();

        return redirect(route('emailsignatures.index'))->with('flash_success', 'Email Signature saved successfully');
    }

    public function destroy($id)
    {
        EmailSignature::destroy($id);
        return redirect()->route('emailsignatures.index')
            ->with('success','Email Templates deleted successfully');
    }
}
