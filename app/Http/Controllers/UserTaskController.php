<?php

namespace App\Http\Controllers;

use App\Client;
use App\MicrosoftCalendar;
use App\OfficeUser;
use App\User;
use App\UserTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\TokenStore\MicrosoftTokenCache;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class UserTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request){

        /*$url = route('user-task.save');

        $tokenCache = new MicrosoftTokenCache();
        $accessToken = $tokenCache->getAccessToken();

        if(($accessToken == '') || ($accessToken == null)){
            $this->microsoftSignin($url);
        }*/

        $date_start = $request->input('task_date_start');
        $date_end = $request->input('task_date_end');
        $client_id = $request->input('task_client');

        $client_name = Client::find($client_id);

        $viewData = $this->loadViewData();
        $viewData['userTimeZone'] = "South Africa Standard Time"; // Todo: Set this dynamically

        $graph = $this->getGraph();

        $attendeeAddresses = explode(';', $request->input('attendees'));

        $attendees = [];
        foreach($attendeeAddresses as $attendeeAddress)
        {
            array_push($attendees, [
                // Add the email address in the emailAddress property
                'emailAddress' => [
                    'address' => $attendeeAddress
                ],
                // Set the attendee type to required
                'type' => 'required'
            ]);
        }

        /*$newEvent = [
            'subject' => ($client_name->company != null ? $client_name->company : $client_name->first_name.' '.$client_name->last_name).' - '.$request->input('task_type'),
            'IsAllDay' => false,
            'attendees' => $attendees,
            'start' => [
                'dateTime' => $date_start,
                'timeZone' => $viewData['userTimeZone']
            ],
            'end' => [
                'dateTime' => $date_end,
                'timeZone' => $viewData['userTimeZone']
            ],
            'body' => [
                'content' => $request->input('task_message'),
                'contentType' => 'text'
            ],
            'IsReminderOn' => false
        ];

        $response = $graph->createRequest('POST', '/me/events')
            ->attachBody($newEvent)
            ->setReturnType(Model\Event::class)
            ->execute();

        if($response) {*/
            $task = new UserTask();
            $task->message = $request->input('task_message');
            $task->client_id = $client_id;
            $task->task_type = $request->input('task_type');
            $task->task_other = $request->input('task_other');
            $task->task_date_start = $date_start;
            $task->task_date_end = $date_end;
            $task->user_id = Auth::id();
            $task->attendees = $request->input('attendees');
            $task->office_id = OfficeUser::select('office_id')->where('user_id', Auth::id())->first()->office_id;
            $task->save();

            return response()->json(['message' => 'success', 'task_attendees'=>$task->attendees,'task_id' => $task->id, 'task_type' => $task->task_type, 'task_other' => $task->task_other, 'task_date_start' => Carbon::parse($task->task_date_start)->format('Y-m-d H:i'), 'task_date_end' => Carbon::parse($task->task_date_end)->format('Y-m-d H:i'), 'task_message' => $task->message, 'client' => ($task->client->company != null ? $task->client->company : $task->client->full_name), 'user' => $task->user->full_name]);
        /*}*/
    }

    public function show(Request $request, $task_id){
        $task = UserTask::find($task_id);

        $task_arr = [
            "task_id" => $task->id,
            "client_id" => $task->client_id,
            "task_type"=>$task->task_type,
            "task_other"=>$task->task_other,
            'task_attendees'=>$task->attendees,
            "task_date_start"=>Carbon::parse($task->task_date_start)->format("Y-m-d H:i"),
            "task_date_end"=>Carbon::parse($task->task_date_end)->format("Y-m-d H:i"),
            "client" => ($task->client_id != null ? ($task->client->full_name != '' ? '' : $task->client_name($task->client_id)) : ''),
            "task_message" => $task->message
        ];

        return response()->json($task_arr);
    }

    public function update(Request $request, $task_id){

        $task = UserTask::find($task_id);
        $task->message = $request->input('task_message');
        $task->client_id = $request->input('task_client');
        $task->task_type = $request->input('task_type');
        $task->task_other = $request->input('task_other');
        $task->task_date_start = $request->input('task_date_start');
        $task->task_date_end = $request->input('task_date_end');
        $task->save();

        return response()->json(['message'=>'success','task_id'=>$task->id,'task_type'=>$task->task_type,'task_other'=>$task->task_other,'task_date_start'=>Carbon::parse($task->task_date_start)->format("Y-m-d H:i"),'task_date_end'=>Carbon::parse($task->task_date_end)->format("Y-m-d H:i"),'task_message'=>$task->message,'client'=>($task->client->company != null ? $task->client->company : $task->client->full_name),'user'=>$task->user->full_name]);
    }

    public function delete(Request $request, $task_id){

        UserTask::destroy($task_id);

        return response()->json(['message'=>"success"]);
    }

    public function complete(Request $request, $task_id){

        $task = UserTask::find($task_id);
        $task->status_id = 0;
        $task->task_completed_date = now();
        $task->save();

        return response()->json(['message'=>"success"]);
    }

    public function microsoftSignin($url){
        $tokenCache = new MicrosoftTokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // If no token is found, signin


        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId'                => config('azure.appId'),
            'clientSecret'            => config('azure.appSecret'),
            'redirectUri'             => $url,
            'urlAuthorize'            => config('azure.authority').config('azure.authorizeEndpoint'),
            'urlAccessToken'          => config('azure.authority').config('azure.tokenEndpoint'),
            'urlResourceOwnerDetails' => '',
            'scopes'                  => config('azure.scopes')
        ]);
        dd($oauthClient);

        $authUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in callback
        session(['oauthState' => $oauthClient->getState()]);

        return redirect()->away($authUrl);

    }

    public function loadViewData()
    {
        $viewData = [];

        // Check for flash errors
        if (session('error')) {
            $viewData['error'] = session('error');
            $viewData['errorDetail'] = session('errorDetail');
        }

        // Check for logged on user
        if (session('userName'))
        {
            $viewData['userName'] = session('userName');
            $viewData['userEmail'] = session('userEmail');
            $viewData['userTimeZone'] = session('userTimeZone');
        }

        return $viewData;
    }

    private function getGraph(): Graph
    {
        // Get the access token from the cache
        $tokenCache = new MicrosoftTokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // Create a Graph client
        $graph = new Graph();
        $graph->setAccessToken($accessToken);
        return $graph;
    }
}
