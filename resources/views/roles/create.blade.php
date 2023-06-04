@extends('flow.default')

@section('title') Create Role @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveRole()" class="btn btn-success btn-lg mt-2 ml-2 float-right">Save</a>
            <a href="{{route('roles.index')}}" class="btn btn-outline-primary btn-sm mt-2">Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                {{Form::open(['url' => route('roles.store'), 'method' => 'post','id'=>'save_role_form'])}}

                <div class="form-group mt-3">
                    {{Form::label('name', 'Name')}}
                    {{Form::text('name',old('name'),['class'=>'form-control'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
                    @foreach($errors->get('name') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover text-center">
                        <thead>
                        <tr>
                            @foreach($permissions as $permission)
                                <th>{{$permission->display_name}}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            @foreach($permissions as $permission)
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input name="permission[{{$permission->id}}]" type="checkbox" class="custom-control-input" id="permission[{{$permission->id}}]" value="{{$permission->id}}">
                                        <label class="custom-control-label" for="permission[{{$permission->id}}]">&nbsp;</label>
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                        </tbody>
                    </table>
                </div>

                {{Form::close()}}
            </div>
        </div>
    </div>
@endsection