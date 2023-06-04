@extends('flow.default')

@section('title') Profile @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>{{$user->name()}}</h3>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100">
            <div class="container-fluid container-title">
                <h3>@yield('header')</h3>
            </div>
            <div class="container-fluid container-content">
                <div class="table-responsive ">
                    <div class="row mt-3">
                        <div class="col-lg-9">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-3">
                                            <img src="{{route('avatar',['q'=>$user->avatar])}}" class="blackboard-avatar blackboard-avatar-profile"/>
                                        </div>
                                        <div class="col-lg-9">
                                            <ul>
                                                <dt>
                                                    Email
                                                </dt>
                                                <dd>
                                                    {{$user->email}}
                                                </dd>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Roles</th>
                                    </tr>
                                    </thead>
                                    @forelse($roles as $role)
                                        <tr>
                                            <td>{{$role}}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td><small class="text-muted">This user is not assigned to any roles</small></td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Divisions</th>
                                    </tr>
                                    </thead>
                                    @forelse($user->divisions as $division)
                                        <tr>
                                            <td>{{$division->name}}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td><small class="text-muted">This user is not assigned to any divisions</small></td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Regions</th>
                                    </tr>
                                    </thead>
                                    @forelse($user->regions as $region)
                                        <tr>
                                            <td>{{$region->name}}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td><small class="text-muted">This user is not assigned to any regions</small></td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Areas</th>
                                    </tr>
                                    </thead>
                                    @forelse($user->areas as $area)
                                        <tr>
                                            <td>{{$area->name}}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td><small class="text-muted">This user is not assigned to any areas</small></td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Offices</th>
                                    </tr>
                                    </thead>
                                    <tr>
                                        <td>{{$user->offices[0]->name??"This user is not assigned to any offices"}}</td>
                                    </tr>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- <div class="container">
                    <blackboard-fa-details-show is-financial-advisor="{{in_array("Financial advisor", $roles->toArray())}}"></blackboard-fa-details-show>
                </div> --}}
            </div>
        </div>
    </div>
@endsection
