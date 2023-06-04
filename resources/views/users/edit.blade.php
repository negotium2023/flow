@extends('flow.default')

@section('title') Edit User @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveUser()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Save</a>
            <a href="{{route('users.index')}}" class="btn btn-outline-primary btn-sm mt-3">Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                {{Form::open(['url' => route('users.update',$user), 'method' => 'put','files'=>true,'id'=>'save_user_form'])}}

                <div class="form-group mt-3">
                    {{Form::label('first_name', 'First Name')}}
                    {{Form::text('first_name',$user->first_name,['class'=>'form-control form-control-sm'. ($errors->has('first_name') ? ' is-invalid' : ''),'placeholder'=>'First Name'])}}
                    @foreach($errors->get('first_name') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    {{Form::label('last_name', 'Last Name')}}
                    {{Form::text('last_name',$user->last_name,['class'=>'form-control form-control-sm'. ($errors->has('last_name') ? ' is-invalid' : ''),'placeholder'=>'Last Name'])}}
                    @foreach($errors->get('last_name') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    {{Form::label('email', 'Email')}}
                    {{Form::text('email',$user->email,['class'=>'form-control form-control-sm'. ($errors->has('email') ? ' is-invalid' : ''),'placeholder'=>'Email'])}}
                    @foreach($errors->get('email') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    {{Form::label('avatar', 'Display Picture')}}
                    {{Form::file('avatar',['class'=>'form-control form-control-sm'. ($errors->has('avatar') ? ' is-invalid' : ''),'placeholder'=>'Avatar','onchange'=>"document.getElementById('blackboard-preview-large').src = window.URL.createObjectURL(this.files[0]); document.getElementById('blackboard-preview-small').src = window.URL.createObjectURL(this.files[0])"])}}
                    @foreach($errors->get('avatar') as $error)
                        <div class="invalid-feedback">
                            {{ $error}}
                        </div>
                    @endforeach
                    <small id="avatar" class="form-text text-muted">
                        Images must be a square and will be resized to 200x200
                    </small>
                    <br>
                    <img src="{{route('avatar',['q'=>$user->avatar])}}" id="blackboard-preview-large" class="blackboard-avatar blackboard-avatar-profile"/>
                    <img src="{{route('avatar',['q'=>$user->avatar])}}" id="blackboard-preview-small" class="blackboard-avatar blackboard-avatar-navbar-img ml-3"/>
                </div>

                <div class="form-group">
                    {{Form::label('role', 'Role')}}
                    {{Form::select('role[]',$roles,$user_roles,['class'=>'form-control form-control-sm chosen-select'. ($errors->has('role') ? ' is-invalid' : ''),'multiple'])}}
                    @foreach($errors->get('role') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    {{Form::label('division', 'Division')}}
                    {{Form::select('division[]',$divisions,$user_divisions,['class'=>'form-control form-control-sm chosen-select'. ($errors->has('division') ? ' is-invalid' : ''),'multiple'])}}
                    @foreach($errors->get('division') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    {{Form::label('region', 'Region')}}
                    {{Form::select('region[]',$regions,$user_regions,['class'=>'form-control form-control-sm chosen-select'. ($errors->has('region') ? ' is-invalid' : ''),'multiple'])}}
                    @foreach($errors->get('region') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    {{Form::label('area', 'Area')}}
                    {{Form::select('area[]',$areas,$user_areas,['class'=>'form-control form-control-sm chosen-select'. ($errors->has('area') ? ' is-invalid' : ''),'multiple'])}}
                    @foreach($errors->get('area') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    {{Form::label('office', 'Office')}}
                    {{Form::select('office[]',$offices,$user_offices,['class'=>'form-control form-control-sm chosen-select'. ($errors->has('office') ? ' is-invalid' : ''),'multiple'])}}
                    @foreach($errors->get('office') as $error)
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
@section('extra-css')
    <style>
        a:focus{
            outline:none !important;
            border:0px !important;
        }

        .activity a{
            color: rgba(0,0,0,0.5) !important;
        }

        .activity a.dropdown-item {
            color:#212529 !important;
        }

        .btn-comment{
            padding: .25rem .25rem;
            font-size: .575rem;
            line-height: 1;
            border-radius: .2rem;
        }

        .modal-dialog {
            max-width: 700px;
            margin: 1.75rem auto;
            min-width: 500px;
        }

        .modal .chosen-container, .modal .chosen-container-multi{
            width:98% !important;
        }

        .chosen-container, .chosen-container-multi{
            line-height: 30px;
            width:98% !important;
        }

        .modal-open .modal{
            padding-right: 0px !important;
        }

        .progress { position:relative; width:100%; border: 1px solid #7F98B2; padding: 1px; border-radius: 3px; display:none; }
        .bar { background-color: #B4F5B4; width:0%; height:25px; border-radius: 3px; }
        .percent { position:absolute; display:inline-block; top:3px; left:48%; color: #7F98B2;}
    </style>
@endsection
@section('extra-js')

    <script src="{{asset('chosen/docsupport/init.js')}}" type="text/javascript" charset="utf-8"></script>

@endsection