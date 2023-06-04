@extends('flow.default')

@section('title') Actions @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="{{route('actions.create')}}" class="btn btn-primary float-right ml-2 mt-3"><i class="fa fa-plus"></i> Action</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                    <table class="table table-bordered table-hover table-sm">
                        <thead>
                        <tr>
                            <th>@sortablelink('name', 'Name')</th>
                            <th>@sortablelink('description', 'Description')</th>
                            <th>@sortablelink('activities', 'Activities Included')</th>
                            <th>@sortablelink('created_by', 'Added By')</th>
                            <th class="last">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($actions as $action)
                            <tr>
                                <td>{{$action["name"]}}</td>
                                <td>{{$action["description"]}}</td>
                                <td>
                                    @foreach($action["activity"] as $activity)
                                        {{$activity}} <br/>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($action["created_by"] as $user)
                                        {{$user}} <br/>
                                    @endforeach
                                </td>
                                <td class="last">
                                    <a href="{{route('action.edit',$action["id"])}}" class="btn btn-sm btn-success"><i class="fas fa-pencil-alt"></i></a>
                                    @if($action["status"] != 1)
                                        <a class="reactivate btn btn-outline-danger btn-sm" href="{{route('action.activate',$action["id"])}}"><i class="fa fa-recycle"></i></a>
                                    @else
                                        <a class="deactivate btn btn-danger btn-sm" href="{{route('action.deactivate',$action["id"])}}"><i class="fa fa-trash"></i></a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center"><small class="text-muted">No actions match those criteria.</small></td></td>
                            </tr>
                        @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
