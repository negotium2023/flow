@extends('flow.default')

@section('title') Roles @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveRole()" class="btn btn-success btn-lg mt-2 ml-2 float-right">Save</a>
            <a href="{{route('roles.create')}}" class="btn btn-primary btn-sm mt-2">Create Role</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                {{Form::open(['url' => route('roles.update'), 'method' => 'put','id'=>'save_role_form'])}}
                <div class="table-responsive h-100">
                    <table class="table table-bordered table-sm table-hover">
                        <thead>
                        <tr>
                            <th>Name</th>
                            @foreach($permissions as $permission)
                                <th>{{$permission->display_name}}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>{{$role->display_name}}</td>
                                @foreach($permissions as $permission)
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input name="permission[{{$role->id.']['.$permission->id}}]" {{($role->permissions->where('id',$permission->id)->count() > 0) ? 'checked' : ''}} type="checkbox" class="custom-control-input" id="permission[{{$role->id.']['.$permission->id}}]" value="{{$permission->id}}">
                                            <label class="custom-control-label" for="permission[{{$role->id.']['.$permission->id}}]">&nbsp;</label>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">No roles match those criteria.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{Form::close()}}
            </div>
        </div>
    </div>
@endsection
