@extends('flow.default')

@section('title') {{$type_name}} @endsection

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
                    <div>
                    <a href="{{route('processesgroup.create')}}?f={{$process_type_id}}" class="btn btn-sm btn-primary float-right ml-2 mt-2">Add {{$type_name}} Category</a>
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
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                    <table class="table table-bordered table-hover table-sm table-fixed">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Sub-Processes</th>
                            <th>Created</th>
                            <th>Modified</th>
                            <th class="last">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($processes as $key => $value)
                        @forelse($value as $k => $val)
                            <tr>
                                <td>{{$val["name"]}}</td>
                                <td>{{$val["pcount"]}}</td>
                                <td>{{$val["created_at"]}}</td>
                                <td>{{$val["updated_at"]}}</td>
                                <td class="last">
                                    <a href="{{route('processes.index',$val["id"])}}?f={{$process_type_id}}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                    <a href="{{route('processesgroup.edit',(isset($val["id"]) ? $val["id"] : '0'))}}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                    {{Form::open(['url' => route('processesgroup.destroy',(isset($val["id"]) ? $val["id"] : '0')).'?t='.$process_type_id, 'method' => 'delete','style'=>'display:inline;width:fit-content;margin:0px;'])}}
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    {{Form::close()}}
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center"><small class="text-muted">No {{$type_name}} match those criteria.</small></td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
