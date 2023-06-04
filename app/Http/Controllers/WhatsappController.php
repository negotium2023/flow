<?php

namespace App\Http\Controllers;

use App\ClientHelper;
use App\WhatsappMessages;
use Illuminate\Http\Request;
use App\Whatsapp;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsappController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['incoming']);
    }

    public function listenToReplies(Request $request)
    {
        // dd('test');

        $from = substr($request->input('From'),9);
        $body = $request->input('Body');

        $fclient = \App\Client::where('contact','0'.substr($from,3))->orWhere('contact',$from)->first();

        /*$myfile = fopen("newfile2.txt", "w") or die("Unable to open file!");
        $txt = $fclient["first_name"];
        fwrite($myfile, $txt);
        $txt = $from;
        fwrite($myfile, $txt);
        $txt = $body;
        fwrite($myfile, $txt);
        fclose($myfile);*/

        if($fclient){
            $whatsapp = new WhatsappMessages();
            $whatsapp->message = $body;
            $whatsapp->sender = 'Client';
            $whatsapp->client_id = $fclient["id"];
            $whatsapp->user_id = '0';
            $whatsapp->save();
        }

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->request('GET', "https://api.github.com/users/$body");
            $githubResponse = json_decode($response->getBody());
            if ($response->getStatusCode() == 200) {
                $message = "*Message:* $githubResponse->message\n";
                $this->sendWhatsAppMessage($message, $from);
            } else {
                $this->sendWhatsAppMessage($githubResponse->message, $from);
            }
        } catch (RequestException $th) {
            $response = json_decode($th->getResponse()->getBody());
            $this->sendWhatsAppMessage($response->message, $from);
        }
        return;
    } 

    /**
     * Sends a WhatsApp message  to user using
     * @param string $message Body of sms
     * @param string $recipient Number of recipient
     */
    public function sendWhatsAppMessage($data)
    {

        $twilio_whatsapp_number = getenv('TWILIO_WHATSAPP_NUMBER');
        $account_sid = getenv("TWILIO_SID");
        $auth_token = getenv("TWILIO_AUTH_TOKEN");

        $to = $data['client'];
        $whatsapp_message = $data['whatsapp_message'];

        $to_number = (int)$to->contact;
        // dd($to_number);

        $whatsapp = new WhatsappMessages();
        $whatsapp->message = $whatsapp_message;
        $whatsapp->sender = 'User';
        $whatsapp->client_id = $to->id;
        $whatsapp->user_id = Auth::id();
        $whatsapp->save();

        $client = new Client($account_sid, $auth_token);

        // Request::session()->put('flash_success','Whatsapp message successfully sent');

        $recipient = (substr($to_number,0,1) == '0' ? '+27'.substr($to_number,0) : '+27'.$to_number );
        // dd($recipient);

        return $client->messages->create('whatsapp:'.$recipient, array('from' => 'whatsapp:'.$twilio_whatsapp_number, 'body' => $whatsapp_message));
    }

    public function message(Request $request){

        // TODO: validate incoming params first!

        $recipient = $request->input('recipient');
        $recipient = (substr($recipient,0,1) == '0' ? '27'.substr($recipient,1) : ltrim($recipient, '+') );
        // dd($recipient);

        $url = "https://messages-sandbox.nexmo.com/v0.1/messages";
        $params = ["to" => ["type" => "whatsapp", "number" => $recipient],
            "from" => ["type" => "whatsapp", "number" => "14157386170"],
            "message" => [
                "content" => [
                    "type" => "text",
                    "text" => $request->input('whatsapp_message')
                ]
            ]
        ];
        $headers = ["Authorization" => "Basic " . base64_encode("91171953" . ":" . "9nhdx3hYvdqJgpvL")];

        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, ["headers" => $headers, "json" => $params]);
        $data = $response->getBody();
        Log::Info($data);

        $whatsapp = new WhatsappMessages();
        $whatsapp->message = $request->input('whatsapp_message');
        $whatsapp->sender = 'User';
        $whatsapp->client_id = $request->input('client_id');
        $whatsapp->user_id = Auth::id();
        $whatsapp->save();

        return $data;
    }
    
    public function incoming(Request $request){

        $data = $request->all();

        $text = $data['message']['content']['text'];
        $number = intval($text);

        NotificationEvent::dispatch(Auth::user()->id, $text);

        // Log::Info($number);
    
        // Log::Info($data);

        // $whatsapp = new WhatsappMessages();
        // $whatsapp->message = $text;
        // $whatsapp->sender = 'Client';
        // $whatsapp->client_id = 'client_id';
        // $whatsapp->user_id = Auth::id();
        // $whatsapp->save();

        // DB::table('replies')->insert([
        //     'message' => $text
        // ]);
        // Log::Info($data);
    }

}
 