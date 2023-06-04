<?php

namespace App\Http\Controllers;

use App\BillboardMessage;
use App\OfficeUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillboardMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request){

        $message = new BillboardMessage();
        $message->message = $request->input('billboard_message');
        $message->heading = $request->input('billboard_heading');
        $message->client_id = $request->input('billboard_client');
        $message->user_id = Auth::id();
        $message->office_id = OfficeUser::select('office_id')->where('user_id',Auth::id())->first()->office_id;
        $message->save();

        return response()->json(['message'=>'success','message_id'=>$message->id,'billboard_heading'=>$message->heading,'billboard_message'=>$message->message,'client'=>($message->client_id != null ? ($message->client->company != null ? $message->client->company : $message->client->full_name) : ''),'user'=>$message->user->full_name]);
    }

    public function show(Request $request, $message_id){
        $message = BillboardMessage::find($message_id);

        $message_arr = [
            "message_id" => $message->id,
            "client_id" => $message->client_id,
            "client" => ($message->client_id != null ? ($message->client->full_name != '' ? '' : $message->client_name($message->client_id)) : ''),
            "billboard_heading" => $message->heading,
            "billboard_message" => $message->message
        ];

        return response()->json($message_arr);
    }

    public function update(Request $request, $message_id){

        $message = BillboardMessage::find($message_id);
        $message->message = $request->input('billboard_message');
        $message->heading = $request->input('billboard_heading');
        $message->client_id = $request->input('billboard_client');
        $message->save();

        return response()->json(['message'=>'success','message_id'=>$message->id,'billboard_heading'=>$message->heading,'billboard_message'=>$message->message,'client'=>($message->client_id != null ? ($message->client->company != null ? $message->client->company : $message->client->full_name) : ''),'user'=>$message->user->full_name]);
    }

    public function delete(Request $request, $message_id){

        BillboardMessage::destroy($message_id);

        return response()->json(['message'=>"success"]);
    }

    public function complete(Request $request, $message_id){

        $message = BillboardMessage::find($message_id);
        $message->status_id = 0;
        $message->save();

        return response()->json(['message'=>"success"]);
    }
}
