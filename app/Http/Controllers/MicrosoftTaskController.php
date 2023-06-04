<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TokenStore\MicrosoftTokenCache;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TimeZones\TimeZones;

class MicrosoftTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // <LoadViewDataSnippet>
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
    // </LoadViewDataSnippet>

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tokenCache = new MicrosoftTokenCache();
        $accessToken = $tokenCache->getAccessToken();

        // If no token is found, signin
        if(($accessToken == '') || ($accessToken == null)){
            return redirect(route('azure.signin'));
        }

        $viewData = $this->loadViewData();
        $viewData['userTimeZone'] = "South Africa Standard Time"; // Todo: Set this dynamically

        $graph = $this->getGraph();

        // Get user's timezone
        $timezone = TimeZones::getTzFromWindows($viewData['userTimeZone']);

        // Get start and end of week
        $startOfWeek = new \DateTimeImmutable('sunday -1 week', $timezone);
        $endOfWeek = new \DateTimeImmutable('sunday', $timezone);

        $viewData['dateRange'] = $startOfWeek->format('M j, Y').' - '.$endOfWeek->format('M j, Y');

        $queryParams = array(
            'startDateTime' => $startOfWeek->format(\DateTimeInterface::ISO8601),
            'endDateTime' => $endOfWeek->format(\DateTimeInterface::ISO8601),
            // Only request the properties used by the app
            '$select' => 'subject,organizer,start,end',
            // Sort them by start time
            '$orderby' => 'start/dateTime',
            // Limit results to 25
            '$top' => 25
        );

        // Append query parameters to the '/me/calendarView' url
        $getTaskssUrl = '/me/todo/lists';
        // $getEventsUrl = '/me/todo/lists?'.http_build_query($queryParams);

        $events = $graph->createRequest('GET', $getTaskssUrl)
            // Add the user's timezone to the Prefer header
            ->addHeaders(array(
                'Prefer' => 'outlook.timezone="'.$viewData['userTimeZone'].'"'
            ))
            ->setReturnType(Model\TodoTaskList::class)
            ->execute();
        dd($events);

        $viewData['events'] = $events;

        return view('microsoft.tasks.index', $viewData);
    }

    // <getNewEventFormSnippet>
    public function create()
    {
        $parameters = [
            'message' => ''
        ];

        return view('microsoft.task.create')->with($parameters);
    }
    // </getNewEventFormSnippet>

    // <createNewEventSnippet>
    public function store(Request $request)
    {
        // Validate required fields
        $request->validate([
            'title' => 'required|string',
            'importance' => 'required|string',
            'isReminderOn' => 'required|string',
            'status' => 'required|string',
            'eventEnd' => 'required|date',
            'eventBody' => 'nullable|string'
        ]);

        $tokenCache = new MicrosoftTokenCache();
        $accessToken = $tokenCache->getAccessToken();

        $userPrincipalName = $tokenCache->getUserPrincipalName();

        $viewData = $this->loadViewData();
        $viewData['userTimeZone'] = "South Africa Standard Time"; // Todo: Set this dynamically

        $graph = $this->getGraph();

        // Attendees from form are a semi-colon delimited list of
        // email addresses
        $attendeeAddresses = explode(';', $request->eventAttendees);

        // The Attendee object in Graph is complex, so build the structure
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

        // Build the task
        $newTask = [
            'title' => $request->title,
            'importance' => $request->importance, // low, normal, high
            'isReminderOn' => $request->isReminderOn,
            'reminderDateTime' => $request->reminderDateTime,
            'completedDateTime' => '',
            'status' => $request->status, // notStarted, inProgress, completed, waitingOnOthers, deferred
            'dueDateTime' => [
                '@odata.type' => $viewData['userTimeZone'] // "microsoft.graph.dateTimeTimeZone"
            ],
            'body' => [
                'content' => $request->taskBody,
                'contentType' => 'text'
            ],
            'createdDateTime' => '',
            'lastModifiedDateTime' => '',
            'bodyLastModifiedDateTime' => ''
        ];

        // POST /me/todo/lists
        $response = $graph->createRequest('POST', '/me/todo/lists/{todoTaskListId}/tasks')
            ->attachBody($newTask)
            ->setReturnType(Model\TodoTask::class)
            ->execute();

        return redirect(route('azure_task.index'))->with('flash_success', 'Task created successfully.');
    }
    // </createNewEventSnippet>

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
