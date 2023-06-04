<?php

namespace App\Http\Controllers;

use App\MicrosoftCalendar;
use App\TokenStore\MicrosoftTokenCache;
use Illuminate\Http\Request;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use App\TimeZones\Timezones;

class MicrosoftCalendarController extends Controller
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
    public function listView()
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
        $timezone = Timezones::getTzFromWindows($viewData['userTimeZone']);

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
        $getEventsUrl = '/me/calendarView?'.http_build_query($queryParams);

        $events = $graph->createRequest('GET', $getEventsUrl)
            // Add the user's timezone to the Prefer header
            ->addHeaders(array(
                'Prefer' => 'outlook.timezone="'.$viewData['userTimeZone'].'"'
            ))
            ->setReturnType(Model\Event::class)
            ->execute();

        $viewData['events'] = $events;

        return view('microsoft.calendar.index', $viewData);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        $year = date('Y');
        if(isset($request->year)){
            $year = $request->year;
        }

        $month = (int)date('m');
        if(isset($request->month)){
            $month = $request->month;
        }

        $month_text = date('F', strtotime(date($year.'-'.$month.'-01')));

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
        $timezone = Timezones::getTzFromWindows($viewData['userTimeZone']);

        // Get start and end of month
        $startOfMonth = new \DateTimeImmutable('first day of '.$month_text.' '.$year, $timezone);
        $endOfMonth = new \DateTimeImmutable('last day of '.$month_text.' '.$year, $timezone);

        $queryParams = array(
            'startDateTime' => $startOfMonth->format(\DateTimeInterface::ISO8601),
            'endDateTime' => $endOfMonth->format(\DateTimeInterface::ISO8601),
            // Only request the properties used by the app
            '$select' => 'subject,organizer,start,end,attendees,body,bodyPreview,location',
            // Sort them by start time
            '$orderby' => 'start/dateTime',
            // Limit results to 25
            '$top' => 1500
        );

        // Append query parameters to the '/me/calendarView' url
        $getEventsUrl = '/me/calendarView?'.http_build_query($queryParams);

        $events = $graph->createRequest('GET', $getEventsUrl)
            // Add the user's timezone to the Prefer header
            ->addHeaders(array(
                'Prefer' => 'outlook.timezone="'.$viewData['userTimeZone'].'"'
            ))
            ->setReturnType(Model\Event::class)
            ->execute();

        // dd($events[0]->getAttendees()[0]['emailAddress']['address']);

        $parameters = [
            'events' => $events,
            'year' => $year,
            'month' => $month
        ];

        // dd($parameters);
        
        return view('microsoft.calendar.calendar', $parameters);
    }

    // <getNewEventFormSnippet>
    public function create()
    {
        $parameters = [
            'message' => ''
        ];

        return view('microsoft.calendar.create')->with($parameters);
    }
    // </getNewEventFormSnippet>

    // <createNewEventSnippet>
    public function store(Request $request)
    {
        // Validate required fields
        $request->validate([
            'eventSubject' => 'nullable|string',
            'eventAttendees' => 'nullable|string',
            'eventStart' => 'required|date',
            'eventEnd' => 'required|date',
            'eventBody' => 'nullable|string'
        ]);

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

        // Build the event
        $newEvent = [
            'subject' => $request->eventSubject,
            'attendees' => $attendees,
            'start' => [
                'dateTime' => $request->eventStart,
                'timeZone' => $viewData['userTimeZone']
            ],
            'end' => [
                'dateTime' => $request->eventEnd,
                'timeZone' => $viewData['userTimeZone']
            ],
            'body' => [
                'content' => $request->eventBody,
                'contentType' => 'text'
            ]
        ];

        // POST /me/events
        $response = $graph->createRequest('POST', '/me/events')
            ->attachBody($newEvent)
            ->setReturnType(Model\Event::class)
            ->execute();

        return redirect(route('calendar.index'))->with('flash_success', 'Event created successfully.');
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

    public function microsoftToken()
    {
        $tenantId = 'f8cdef31-a31e-4b4a-93e4-5f571e91255a';
        $clientId = 'bab780e7-152e-4f96-85a3-baa98d6cc3ab';
        $clientSecret = 'qadqxKGND5457?:^~avYYR7';

        $guzzle = new \GuzzleHttp\Client();
        $url = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/token?api-version=1.0';
        // $url = 'https://login.microsoftonline.com/' . $tenantId . '/oauth2/v2.0/authorize';
        $token = json_decode($guzzle->post($url, [
            'form_params' => [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'resource' => 'https://graph.microsoft.com/',
                'grant_type' => 'client_credentials',
            ],
        ])->getBody()->getContents());

        return $token->access_token;
    }
}
