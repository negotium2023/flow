@extends('flow.default')
@section('title') View Activities @endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
        </div>
    </div>
@endsection
@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                    <table class="table table-bordered table-sm table-hover">
                        <thead class="btn-dark">
                        <tr>
                            <th>Client</th>
                            <th>Activity</th>
                            <th>Date Assigned</th>
                            <th>Assigned By</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($activities_log as $activity_log)
                            <tr>
                                <td><a href="{{route('clients.show',$client[0])}}">{{$client[0]->company}}</a></td>
                                <td>{{$activity_log->activity_name}}</td>
                                <td>{{$activity_log->created_at}}</td>
                                <td>{{$user[0]->first_name}} {{$user[0]->last_name}}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">No activities match those criteria.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
    </div>
@endsection