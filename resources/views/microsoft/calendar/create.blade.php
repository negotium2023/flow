@extends('flow.default')
@section('title') Create Event @endsection
@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100">
            <div class="container-fluid container-title">
                <h3>@yield('title')</h3>
            </div>
            <div class="container-fluid container-content">
                <div class="table-responsive ">

                    <div class="tab-content" id="createEventTab">
                        <div class="tab-pane fade show active" id="default" role="tabpanel" aria-labelledby="default-tab">

                            <div class="col-lg-12 pl-0 pr-0 mt-3 width-98">

                                {{Form::open(['url' => route('calendar.store'), 'method' => 'post','autocomplete'=>'off','class'=>'createEventForm','style'=>'min-width:100%;'])}}
                                <div class="form-group">
                                    {{Form::label('eventSubject', 'Subject')}}
                                    {{Form::text('eventSubject',old('eventSubject'),['class'=>'form-control form-control-sm'. ($errors->has('eventSubject') ? ' is-invalid' : ''),'placeholder'=>'Subject'])}}
                                    @foreach($errors->get('eventSubject') as $error)
                                        <div class="invalid-feedback">
                                            {{$error}}
                                        </div>
                                    @endforeach
                                </div>

                                <div class="form-group">
                                    <label for="eventAttendees">Attendees <small>(Email address ; separated by)</small></label>
                                    {{Form::text('eventAttendees',old('eventAttendees'),['class'=>'form-control form-control-sm'. ($errors->has('eventAttendees') ? ' is-invalid' : ''),'placeholder'=>'Attendees'])}}
                                    @foreach($errors->get('eventAttendees') as $error)
                                        <div class="invalid-feedback">
                                            {{$error}}
                                        </div>
                                    @endforeach
                                </div>

                                <div class="form-group">
                                    {{Form::label('eventStart', 'Start')}}
                                    <input type="datetime-local" class="form-control" name="eventStart" id="eventStart" />
                                    @foreach($errors->get('eventStart') as $error)
                                        <div class="invalid-feedback">
                                            {{$error}}
                                        </div>
                                    @endforeach
                                </div>

                                <div class="form-group">
                                    {{Form::label('eventEnd', 'End')}}
                                    <input type="datetime-local" class="form-control" name="eventEnd" />
                                    @foreach($errors->get('eventEnd') as $error)
                                        <div class="invalid-feedback">
                                            {{$error}}
                                        </div>
                                    @endforeach
                                </div>

                                <div class="form-group">
                                    {{Form::label('eventBody', 'Body')}}
                                    {{Form::textarea('eventBody',old('eventBody'),['size' => '30x5', 'class'=>'form-control form-control-sm'. ($errors->has('eventBody') ? ' is-invalid' : ''),'placeholder'=>'Body'])}}
                                    @foreach($errors->get('eventBody') as $error)
                                        <div class="invalid-feedback">
                                            {{$error}}
                                        </div>
                                    @endforeach
                                </div>

                                <input type="submit" class="btn-sm btn-primary mr-2" value="Save" />
                                <a class="btn-sm btn-secondary" href={{ action('MicrosoftCalendarController@index') }}>Cancel</a>
                                {{Form::close()}}

                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-css')
    <style>
        .width-98 {
            width: 98% !important;
        }
    </style>
@endsection
