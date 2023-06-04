<?php

namespace App\Http\Controllers;

use App\WhatsappTemplate;
use App\Client;
use Illuminate\Http\Request;

class WhatsappTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){

        $template = WhatsappTemplate::orderBy('id');

        if ($request->has('q')) {
            $template->where('name', 'LIKE', "%" . $request->input('q') . "%");
        }

        $parameters = [
            'template' => $template->get()
        ];


        return view('whatsapptemplates.index')->with($parameters);
    }

    public function create(){

        return view('whatsapptemplates.create');
    }

    public function store(Request $request){
        $template = new WhatsappTemplate();
        $template->name = $request->input('name');
        $template->whatsapp_content = $request->input('content');
        $template->user_id = auth()->id();
        $template->save();

        return redirect(route('whatsapptemplates.index'))->with('flash_success', 'Whatsapp Template captured successfully');
    }

    public function show($templateid){
        $template = WhatsappTemplate::where('id',$templateid)->get();

        $parameters = [
            'template' => $template
        ];

        return view('whatsapptemplates.show')->with($parameters);
    }

    public function edit($templateid){

        $template = WhatsappTemplate::where('id',$templateid)->get();

        $parameters = [
            'template' => $template
        ];

        return view('whatsapptemplates.edit')->with($parameters);
    }

    public function update(Request $request,$templateid){
        $template = WhatsappTemplate::find($templateid);
        $template->name = $request->input('name');
        $template->whatsapp_content = $request->input('content');
        $template->user_id = auth()->id();
        $template->save();

        return redirect(route('whatsapptemplates.index'))->with('flash_success', 'Whatsapp Template saved successfully');
    }

    public function destroy($id)
    {
        WhatsappTemplate::destroy($id);
        return redirect()->route('whatsapptemplates.index')
            ->with('success','Whatsapp Templates deleted successfully');
    }

    public function getTemplate($id, $client_id){
        $template = WhatsappTemplate::where('id',$id)->first();
        
        $client = Client::where('id', $client_id)->first();
        // dd($client->first_name);

        $newMessage = str_replace("(name of client)",$client->first_name,$template->whatsapp_content);
            $data = [
                'whatsapp_content' => $newMessage
            ];
            
        return response()->json($data);
    }
}
