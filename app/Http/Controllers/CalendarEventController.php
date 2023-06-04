<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CalendarEvent;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\CalendarEventMail;

class CalendarEventController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        
        $calendar_events = CalendarEvent::all();
        $calendar_events = CalendarEvent::whereDate('start_date', '>=', date("Y-m-d"))
               ->get();
        
        return view('calendar.index')->with(['calendar_events' => $calendar_events]);
        
    }
    
    public function create()
    {
        return view('calendar.create');
    }
    
    public function store(Request $request){
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'email' => 'required|email|max:255'
        ]);
        
        if ($validator->passes()) {
            $calendar_event = new CalendarEvent;
            $calendar_event->title = $request->input('title');
            $calendar_event->row_order = '1';
            $calendar_event->start_date = $request->input('start_date');
            $calendar_event->end_date = $request->input('end_date');
            $calendar_event->save();
            
            Mail::to($request->input('email'))->send(new CalendarEventMail($calendar_event->id, $calendar_event->start_date));
            
            return response()->json([
                'success' => 'Event added successfully.',
                'id' => $calendar_event->id,
                'title' => $request->input('title'), 
                'start_date' => $request->input('start_date'), 
                'end_date' => $request->input('end_date')
            ]);
        }
        
        return response()->json(['errors' => $validator->errors(), 'post_data' => $request->all()]);
        
    }
    
    public function destroy($id)
    {
        $calendar_event = CalendarEvent::find($id);
        $calendar_event->delete();
        return response()->json(['success' => 'Event deleted successfully.']);
    }
    
    public function accept($id)
    {
        
    }
    
    public function reject($id)
    {
        
    }
    
}
