@extends('flow.default')
@section('title') Calendar @endsection
@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100">
            <div class="container-fluid container-title">
                <h3>@yield('title')</h3>
            </div>
            <div class="container-fluid container-content">
                <div class="table-responsive ">
                    <p>{{ $dateRange }}</p>
                    <p class="text-right">
                        <a class="btn-sm btn-primary mr-2" href="{{route('calendar.create')}}">Create Event</a>
                    </p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Organizer</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Start</th>
                                <th scope="col">End</th>
                            </tr>
                        </thead>
                        <tbody>
                        @isset($events)
                            @foreach($events as $event)
                                <tr>
                                    <td>{{ $event->getOrganizer()->getEmailAddress()->getName() }}</td>
                                    <td>{{ $event->getSubject() }}</td>
                                    <td>{{ \Carbon\Carbon::parse($event->getStart()->getDateTime())->format('n/j/y g:i A') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($event->getEnd()->getDateTime())->format('n/j/y g:i A') }}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-js')
    <script src="{{asset('adminlte/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script>
        $(function () {
            $('#calendar_table').DataTable({
                'paging'      : false,
                'lengthChange': true,
                'searching'   : false,
                'ordering'    : false,
                'info'        : true,
                'autoWidth'   : false,
                "pageLength": 15
            });
        });
    </script>
@endsection
@section('extra-css')
    <link rel="stylesheet" href="{{asset('adminlte/plugins/datatables/jquery.dataTables.min.css')}}">
    <style>
        #table_filter {
            margin-right: 8px;
        }
    </style>
@endsection
