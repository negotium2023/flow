@extends('layouts.proud')

@section('proud-form')
    @if(Session::has('message'))
        {!! Session::get('message') !!}
    @else
    <form method="POST" id="login">
        {{ csrf_field() }}
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fa fa-user"></i>
                    </div>
                </div>
                {{Form::email('email',old('email'),['class'=>'form-control form-control-sm'. ($errors->has('email') ? ' is-invalid' : ''),'placeholder'=>'Email','required','autofocus'])}}
            </div>
            @foreach($errors->get('email') as $error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fa fa-lock"></i>
                    </div>
                </div>
                {{Form::password('password',['class'=>'form-control form-control-sm'. ($errors->has('password') ? ' is-invalid' : ''),'placeholder'=>'Password','required'])}}
            </div>
            @foreach($errors->get('password') as $error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endforeach
        </div>

        {{--<div class="form-group">
            <div class="input-group">
                {!! app('captcha')->render() !!}
                @if ($errors->has('g-recaptcha-response'))
                    <div class="invalid-feedback">
                        {{ $errors->first('g-recaptcha-response') }}
                    </div>
                @endif
            </div>
        </div>--}}

        {{--<div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember" {{old('remember') ? 'checked' : ''}}> Remember Me
                </label>
            </div>
        </div>--}}

        <div class="form-group">
            <button type="submit" class="btn btn-block">
                Log In
            </button>
        </div>

    </form>
    @endif
@endsection

@section('proud-footer')
    {{--<div class="card-footer text-center p-3">
        First time here? <a href="{{route('register')}}">Register</a>
    </div>--}}
@endsection

@section('proud-extra')
    <a style="color:#FFFFFF !important;" class="password-request" href="{{route('password.request')}}">
        Forgot Your Password?
    </a>

<script>
    var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
    };


        if (isMobile.any()) {
            //some code...
            console.log('Test');
            document.getElementById('login').action = '{{route('mlogin')}}';
        } else {
            document.getElementById('login').action = '{{route('login')}}';
        }

</script>
@endsection
