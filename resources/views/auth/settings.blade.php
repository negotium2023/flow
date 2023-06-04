@extends('flow.default')

@section('title') Settings @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveSettings()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Update profile</a>
            <a href="{{route('home')}}" class="btn btn-outline-primary btn-sm mt-3">Back</a>
        </div>
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
                    <div class="mt-3">
        <h4>Security</h4>
        {{Form::open(['url' => route('settings.password'), 'method' => 'post'])}}

        <input type="hidden" name="email" value="{{auth()->user()->email}}"/>

        <div class="form-group">
            {{Form::label('old_password', 'Old Password')}}
            {{Form::password('old_password',['class'=>'form-control form-control-sm'. ($errors->has('old_password') ? ' is-invalid' : ''),'placeholder'=>'Old password'])}}
            @foreach($errors->get('old_password') as $error)
                <div class="invalid-feedback">
                    {{ $error}}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            {{Form::label('password', 'New Password')}}
            {{Form::password('password',['class'=>'form-control form-control-sm'. ($errors->has('password') ? ' is-invalid' : ''),'placeholder'=>'New password'])}}
            @foreach($errors->get('password') as $error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            {{Form::label('password_confirmation', 'Confirm New Password')}}
            {{Form::password('password_confirmation',['class'=>'form-control form-control-sm'. ($errors->has('password_confirmation') ? ' is-invalid' : ''),'placeholder'=>'Confirm New Password'])}}
            @foreach($errors->get('password_confirmation') as $error)
                <div class="invalid-feedback">
                    {{ $error}}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-sm btn-success">Change password</button>
        </div>

        {{Form::close()}}

        <hr>

        <h4>Profile</h4>
        {{Form::open(['url' => route('settings.profile'), 'method' => 'post','files'=>true,'id'=>'save_settings_form'])}}

        <div class="form-group">
            {{Form::label('first_name', 'First Name')}}
            {{Form::text('first_name',auth()->user()->first_name,['class'=>'form-control form-control-sm'. ($errors->has('first_name') ? ' is-invalid' : ''),'placeholder'=>'First Name'])}}
            @foreach($errors->get('first_name') as $error)
                <div class="invalid-feedback">
                    {{ $error}}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            {{Form::label('last_name', 'Last Name')}}
            {{Form::text('last_name',auth()->user()->last_name,['class'=>'form-control form-control-sm'. ($errors->has('last_name') ? ' is-invalid' : ''),'placeholder'=>'Last Name'])}}
            @foreach($errors->get('last_name') as $error)
                <div class="invalid-feedback">
                    {{ $error}}
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
                Images will be cropped to 200x200
            </small>
            <br>
            <img src="{{route('avatar',['q'=>Auth::user()->avatar])}}" id="blackboard-preview-large" class="blackboard-avatar blackboard-avatar-profile"/>
            <img src="{{route('avatar',['q'=>Auth::user()->avatar])}}" id="blackboard-preview-small" class="blackboard-avatar blackboard-avatar-navbar-img ml-3"/>
        </div>

        {{--<div class="form-group">
            <button type="submit" class="btn btn-sm btn-success">Update Profile</button>
        </div>--}}

        {{Form::close()}}

        {{--<hr>

        <h4>Email Notifications</h4>
        {{Form::open(['url' => route('settings.notifications'), 'method' => 'post'])}}
<table class="table table-responsive table-borderless">
    <tr>
        <td>
        <input type="checkbox" name="notification_emails" {{(Auth::user()->notification_emails == '1' ? 'checked' : '')}} />
                Notification Emails
        </td>
        <td>

                <input type="checkbox" name="message_emails" {{(Auth::user()->message_emails == '1' ? 'checked' : '')}} />
                Message Emails
        </td>
    </tr>
</table>--}}

        {{--<div class="form-group">
            <button type="submit" class="btn btn-sm">Update preferences</button>
        </div>--}}

        {{Form::close()}}
    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
