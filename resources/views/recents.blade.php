@extends('flow.default')

@section('title') Recents @endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100">
            <div class="container-fluid container-title">
                <h3>@yield('title')</h3>
            </div>
            <div class="container-fluid container-content">
                <div class="table-responsive ">
                    <table class="table table-sm table-bordered blackboard-recents">
                        <thead>
                        <tr>
                            <th colspan="3">
                                Recent Applications
                                <a href="{{route('clients.index')}}" class="btn btn-primary btn-sm float-right"><i class="fa fa-eye"></i> View all</a>
                            </th>
                        </tr>
                        <tr>
                            <th>
                                Client
                            </th>
                            <th>
                                Application
                            </th>
                            <th>
                                Section
                            </th>
                        </tr>
                        </thead>
                        @forelse($clients as $client)
                            <tr>
                                <td>
                                    <a href="{{route('clients.overview',[$client->id,$client->process_id,$client->step_id])}}">{{(isset($client["company"] ) && $client["company"] != ' ' ? $client["company"]  : 'Not Captured')}}</a>
                                </td>
                                <td>
                                    <a href="{{route('clients.overview',[$client->id,$client->process_id,$client->step_id])}}">{{$client->process->name}}</a>
                                </td>
                                <td>
                                    <a href="{{route('clients.overview',[$client->id,$client->process_id,$client->step_id])}}">{{$client->step->name}}</a>
                                    <span class="float-right text-muted"><small><i class="fa fa-clock-o"></i> {{$client->updated_at}}</small></span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">No recent applications</td>
                            </tr>
                        @endforelse
                    </table>
        </div>
            </div>
        </div>
    </div>
@endsection
