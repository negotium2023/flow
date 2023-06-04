@extends('flow.default')

@section('title') Whatsapp Templates @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <form autocomplete="off">
                <div class="form-row">
                    <div class="form-group">
                        <div class="input-group ">
                            {{Form::search('q',old('query'),['class'=>'form-control search','placeholder'=>'Search...'])}}
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{route('whatsapptemplates.create')}}" class="btn btn-primary float-right ml-2 mt-2">Add Whatsapp Template</a>
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
                    <table class="table table-bordered table-sm table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th class="last">Action</th>
                        </tr>
                        </thead>
                        <tbody class="blackboard-locations">
                        @foreach($template as $result)
                            <tr>
                                <td><a href="{{route('whatsapptemplates.show',$result)}}">{{$result->name}}</a></td>
                                <td class="last">
                                    <a href="{{route('whatsapptemplates.edit',$result)}}" class="btn btn-success btn-sm">Edit</a>
                                    {{ Form::open(['method' => 'DELETE','route' => ['whatsapptemplates.destroy','id'=>$result],'style'=>'display:inline']) }}
                                    <a href="#" class="delete deleteDoc btn btn-danger btn-sm">Delete</a>
                                    {{Form::close() }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
