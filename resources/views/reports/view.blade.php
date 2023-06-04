@extends('adminlte.default')

@section('title') Reports @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <a href="{{route('reports.create')}}" class="btn btn-dark btn-sm float-right"><i class="fa fa-plus"></i> Report</a>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <hr />
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-sm table-hover">
            <thead class="btn-dark">
            <tr>
                <th>Name</th>
                <th>Activity Name</th>
                <th>Added By</th>
                <th>Created</th>
                <th class="last">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($reports as $report)
                @if($report->activity != null)
                <tr>
                    <td><a href="{{route('reports.show', $report->activity_id)}}">{{$report->name}}</a></td>
                    <td>{{$report->activity->name}}</td>
                    <td>{{$report->user->first_name}} {{$report->user->last_name}}</td>
                    <td>{{$report->created_at}}</td>
                    <td class="last"><a href="{{route('reports.edit',['reportid' => $report->id])}}" class="btn btn-success btn-sm">Edit </a>
                        {{ Form::open(['method' => 'DELETE','route' => ['reports.destroy','id'=>$report],'style'=>'display:inline']) }}
                        <a href="#" class="delete deleteDoc btn btn-danger btn-sm">Delete</a>
                        {{Form::close() }}</td>
                </tr>
                @endif
            @empty
                <tr><td colspan="5">No Reports were found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    </div>
@endsection