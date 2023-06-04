@extends('flow.default')

@section('title') Users @endsection

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
                    <div class="ml-2 mt-2">
                        <a href="{{route('users.create')}}" class="btn btn-primary btn-sm float-right">Add User</a>
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
                            <th></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th><abbr title="This is the amount of locations a user is assigned to.">Domain</abbr></th>
                            <th><abbr title="This is the amount of roles a user is assigned to.">Roles</abbr></th>
                            <th>Added</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="last"><img src="{{route('avatar',['q'=>$user->avatar])}}" class="blackboard-avatar blackboard-avatar-inline"/></td>
                                <td><a href="{{route('profile',$user)}}">{{$user->name()}}</a></td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->domainCount()}}</td>
                                <td>
                                    @foreach($user->roles as $roles)
                                        {{$roles->display_name}} <br/>
                                    @endforeach
                                </td>
                                <td>{{$user->created_at->diffForHumans()}}</td>
                                <td>{{$user->last_login_at}}</td>
                                <td class="last">
                                        <a href="{{route('users.edit',$user)}}" class="btn btn-success btn-sm"><i class="fa fa-pencil-alt"></i></a>
                                    @if(auth()->id() != $user->id)
                                        &nbsp;
                                    @if($user->deleted_at != null)
                                        <a class="reactivate btn btn-danger btn-sm" href="{{route('users.activateuser',$user)}}"><i class="fa fa-user-alt"></i></a>
                                    @else
                                        <a class="deactivate btn btn-outline-danger btn-sm" href="{{route('users.deactivate',$user)}}"><i class="fa fa-user-alt-slash"></i></a>
                                    @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">No users match those criteria.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
         </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $(".deactivate").click(function (e) {
            e.preventDefault();
            var conf = confirm("Are you sure you want to deactivate this user account?");
            if(conf)
                window.location = $(this).attr("href");
        });
        $(".reactivate").click(function (e) {
            e.preventDefault();
            var conf = confirm("Are you sure you want to reactivate this user account?");
            if(conf)
                window.location = $(this).attr("href");
        });
    </script>
@endsection