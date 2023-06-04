@extends('flow.default')

@section('title') Reports @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <form autocomplete="off">
                <div class="form-row">
                    <div class="form-group" style="display: inline-block">
                        <div class="input-group input-group-sm">
                            {{Form::search('q',old('query'),['class'=>'form-control form-control-sm search','placeholder'=>'Search...'])}}
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-sm btn-default" style="line-height: 1.35rem !important;"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="ml-2 mt-2">
                        <a href="{{route('custom_report.create')}}" class="btn btn-primary btn-sm float-right"><i class="fa fa-plus"></i> Report</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content" style="overflow: auto !important;">
                <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm table-hover">
                <thead>
                <tr>
                    <th>Name</th>
                    {{--<th>Activities</th>--}}
                    <th>Added By</th>
                    <th>Created</th>
                    <th class="last">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($reports as $report)

                        <tr>
                            <td><a href="{{route('custom_report.show', [$report->id,$report->type])}}">{{$report->name}}</a></td>
                            {{--<td>

                            </td>--}}
                            <td>{{$report->user->first_name}} {{$report->user->last_name}}</td>
                            <td>{{$report->created_at}}</td>
                            <td class="last"><a href="{{route('custom_report.edit',['custom_report_id' => $report->id])}}" class="btn btn-success btn-sm">Edit </a>
                                {{ Form::open(['method' => 'DELETE','route' => ['custom_report.destroy','id'=>$report],'style'=>'display:inline']) }}
                                <a href="#" class="delete deleteDoc btn btn-danger btn-sm">Delete</a>
                                {{Form::close() }}</td>
                        </tr>

                @empty
                    <tr><td colspan="5">No Reports were found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
            </div>
        </div>
    </div>
@endsection