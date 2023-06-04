@extends('layouts.app')

@section('title') Calendar @endsection

@section('header')
    <h1><i class="fa fa-calendar-o"></i> @yield('title')</h1>
@endsection

@section('content')
    <form class="form-inline mt-3">
        Show &nbsp;
        {{Form::select('s',['all'=>'All','mine'=>'My','company'=>'Branch'],old('selection'),['class'=>'form-control form-control-sm'])}}
        &nbsp; from &nbsp;
        {{Form::select('s',['all'=>'All Time','mine'=>'My','company'=>'Branch'],old('selection'),['class'=>'form-control form-control-sm'])}}
        &nbsp; matching &nbsp;
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>
            {{Form::text('q',old('query'),['class'=>'form-control','placeholder'=>'Search...'])}}
        </div>
    </form>

    <hr>

    <div id="calendar">
    </div>
@endsection

@section('extra-css')
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css'/>
@endsection

@section('extra-js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js'></script>
    <script>
        $(document).ready(function() {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'listDay,month,agendaWeek,agendaDay'
                },eventSources: [

    // your event source
    {
      events: [ // put the array in the `events` property
        {
          title  : 'event1',
          start  : '2018-04-01'
        },
        {
          title  : 'event2',
          start  : '2018-04-05',
          end    : '2018-04-07'
        },
        {
          title  : 'event3',
          start  : '2018-04-09T12:30:00',
        }
      ],
      color: 'black',     // an option!
      textColor: 'yellow' // an option!
    }

    // any other event sources...

  ]

            })

        });
    </script>
@endsection