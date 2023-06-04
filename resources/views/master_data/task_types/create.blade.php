@extends('flow.default')

@section('title') Add Task Type @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveTaskType()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Save</a>
            <a href="{{route('task_types.index')}}" class="btn btn-outline-primary btn-sm mt-3"><i class="fa fa-caret-left"></i> Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                    {{Form::open(['url' => route('task_types.store'), 'method' => 'post','files'=>true, 'id' => 'tasktypeform'])}}

                    <div class="form-group mt-3">
                        {{Form::label('name', 'Name')}}
                        {{Form::text('name',old('name'),['class'=>'form-control'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
                        @foreach($errors->get('name') as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endsection