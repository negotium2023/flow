@extends('flow.default')
@section('title') Calendar @endsection

@section('header')
    <h1><i class="fa fa-calendar-o"></i> @yield('title')</h1>
@endsection

@section('content')

  <div class="content-container page-content">
    <div class="row col-md-12 h-100">
      <div class="container-fluid container-title">
        <div class="row">
          <div class="col-md-6 col-sm-12">
            <h3>@yield('title')</h3>
          </div>
          <div class="col-md-6 col-sm-12">
            <p class="text-right">
                <a class="btn-sm btn-primary mr-2" href="{{route('calendar.create')}}">Create Event</a>
            </p>
          </div>
        </div>
        <div class="table-responsive ">
          <hr>
          <div id='calendar-container'>
            <input type="hidden" id="year" name="year" value="{{$year}}" />
            <input type="hidden" id="month" name="month" value="{{$month}}" />
            <div id="calendar" style="min-height: 550px; !important;">
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalViewEvent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 618px;max-width: 618px;">
        <div class="modal-content">
            <div class="modal-header text-center" style="border-bottom: 0px;padding:32px 32px 0px 32px;">
                <h5 class="modal-title">View Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0 mb-0">

                <form method="POST" action="http://localhost:8000/azure/calendar" accept-charset="UTF-8" autocomplete="off" class="createEventForm" style="min-width: 100%; background-color: inherit;">
                  <input name="_token" type="hidden" value="eH2Pa5Wrl74ApHrx7xZnkFDpdcFfAyJMx0PKgW0q"> 
                  <div class="form-group">
                    <label for="eventSubject">Subject</label> 
                    <input placeholder="Subject" name="eventSubject" type="text" id="eventSubject" class="form-control form-control-sm" disabled="disabled">
                  </div> 
                  <div class="form-group">
                    <label for="eventAttendees">Attendees <small>(Email address ; separated by)</small></label> 
                    <input placeholder="Attendees" id="eventAttendees" name="eventAttendees" type="text" class="form-control form-control-sm" disabled="disabled">
                  </div> 
                  <div class="form-group">
                    <label for="eventStart">Start</label> 
                    <!-- <input type="datetime-local" name="eventStart" id="eventStart" class="form-control"> -->
                    <input type="text" id="eventStart" name="eventStart" id="eventStart" class="form-control" disabled="disabled">
                  </div> 
                  <div class="form-group">
                    <label for="eventEnd">End</label> 
                    <input type="text" id="eventEnd" name="eventEnd" id="eventEnd" class="form-control" disabled="disabled">
                  </div> 
                  <div class="form-group">
                    <label for="eventBody">Body</label> 
                    <textarea placeholder="Body" name="eventBody" cols="30" rows="10" id="eventBody" class="my-editor form-control form-control-sm" disabled="disabled"></textarea>
                  </div> 
                  <!--
                  <input type="submit" value="Save" class="btn-sm btn-primary mr-2"> 
                  <a href="http://localhost:8000/azure/calendar" class="btn-sm btn-secondary">Cancel</a>
                  -->
                </form>

            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-primary" data-dismiss="modal">Close</button>&nbsp;
                <!-- <button class="btn btn-success" onclick="saveUserTask()">Save</button> -->
            </div>
        </div>
    </div>
    

  </div>

@endsection

@section('extra-css')
  <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.7.0/main.min.css' rel='stylesheet' />
  <style>
    .fc .fc-button {
      line-height: 1 !important;
      color: #fff !important;
      background-color: #06496f !important;
      border-color: #06496f !important; 
    }

    .fc .fc-button:hover {
      background-color: #007bff !important;
    }

    .fc .fc-button-active {
      background-color: #007bff !important;
    }

    .mce-tinymce {
      display: block;
      -webkit-box-shadow: 0px !important;
      -moz-box-shadow: 0px !important;
      box-shadow: 0px !important;
      border:1px solid #b5c9d4 !important;
    }
  </style>

@endsection

@section('extra-js')
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.7.0/main.min.js'></script>

    <script>

        $(document).ready(function() {

          var calendarEl = document.getElementById('calendar');

          var calendar = new FullCalendar.Calendar(calendarEl, {
            height: '100%',
            headerToolbar: {
              left: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
              center: 'title',
              right: 'previous next1'
            },
            customButtons: {
              previous: {
                text: 'Prev',
                click: function(previousMonth) {
                  let year = parseInt($('#year').val(), 10);
                  let month = parseInt($('#month').val(), 10) - 1;
                  if(month == 0){
                    month = 12;
                    year = year - 1;
                  }
                  //location.href = '/azure/calendar?year='+year+'&month=' + month;
                  calendar.prev();
                }
              },
              next1: {
                text: 'Next',
                click: function(nextMonth) {
                  let year = parseInt($('#year').val(), 10);
                  let month = parseInt($('#month').val(), 10) + 1;
                  if(month == 13){
                    month = 1;
                    year = year + 1;
                  }
                  //location.href = '/azure/calendar?year='+year+'&month=' + month;
                  calendar.next();
                }
              }
            },
            /*eventDidMount: function(info) {
              var tooltip = new Tooltip(info.el, {
                title: info.event.extendedProps.description,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
              });
            },*/
            eventClick: function(info) {
              console.log(info);
              // alert(info.event.extendedProps.description);
              var dateNow = new Date();
              
              $("#modalViewEvent").find("#eventSubject").val(info.event.title);
              $("#modalViewEvent").find("#eventAttendees").val(info.event.extendedProps.attendees);
              $("#modalViewEvent").find("#eventStart").val(info.event.start);
              $("#modalViewEvent").find("#eventEnd").val(info.event.end);
              $("#modalViewEvent").find("#eventBody").val(info.event.extendedProps.bodyPreview);
              // $("#modalUserTask").find(".task_client").val('').trigger('chosen:updated');
              tinymce.init(editor_config_read_only);
              tinymce.get('eventBody').setContent(info.event.extendedProps.bodyPreview);
              $("#modalViewEvent").modal('show');

                info.jsEvent.preventDefault(); // don't let the browser navigate
                if (info.event.url) {
                    window.open(info.event.url);
                }
            },
            initialDate: {!! "'".$year."-".($month < 10 ? '0'.$month : $month)."-01'" !!},
            dayMaxEvents: true,
            events: [
              
              @foreach($events as $event)
                @php 
                  $attendees = ''; 
                  foreach($event->getAttendees() as $key => $attendee){
                    $attendees .= $attendee['emailAddress']['address'].';';
                  }
                @endphp 
                {!! 
                  "{".
                    "title: '".str_replace("'", "\'", $event->getSubject())."',".
                    "start: '".$event->getStart()->getDateTime()."',".
                    "end: '".$event->getEnd()->getDateTime()."',".
                    // "body: '".$event->getBody()."',". 
                    "bodyPreview: '".preg_replace( "/\r\n/", "<br />",str_replace("'", "\'", $event->getBodyPreview()))."',".
                    "attendees: '".trim($attendees, ';')."',".
                    // "location: '".$event->getLocation()."',".
                    // "organiser: '".$event->getOrganizer()."',".
                  "},"
                !!}
              @endforeach
            ]
          });

          calendar.render();

          var editor_config_read_only = {
            readonly:1,
            body_id: 'my_id',
            content_style: 'body { background: #e9ecef !important;color:#06496f !important; },.mce-tinymce {display: block;-webkit-box-shadow: 0px !important;-moz-box-shadow: 0px !important;box-shadow: 0px !important;border:1px solid #b5c9d4 !important;}',
            path_absolute : "/",
            branding: false,
            relative_urls: false,
            convert_urls : false,
            menubar : false,
            paste_data_images: true,
            browser_spellcheck: true,
            selector: "textarea.my-editor",
            statusbar: false,
            setup: function (editor) {
              editor.on('change', function () {
                tinymce.triggerSave();
              });
            },
            plugins: [
              "advlist autolink lists link image charmap print preview hr anchor pagebreak",
              "searchreplace visualblocks visualchars code fullscreen",
              "insertdatetime media nonbreaking save table contextmenu directionality",
              "emoticons template paste textcolor colorpicker textpattern"
            ],
            paste_as_text: true,
            toolbar: false,

            external_filemanager_path:"{{url('tinymce/filemanager')}}/",
            filemanager_title:"Responsive Filemanager" ,
            external_plugins: { "filemanager" : "{{url('tinymce')}}/filemanager/plugin.min.js"}
          };
        });
    </script>
@endsection