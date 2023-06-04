@extends('layouts.proud')

@section('proud-form')
    <form class="form-horizontal" method="POST" action="{{ route('password.request') }}">
        {{ csrf_field() }}

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            {{Form::email('email',$email,['class'=>'form-control'. ($errors->has('email') ? ' is-invalid' : ''),'placeholder'=>'Email address','required','autofocus'])}}
            @foreach($errors->get('email') as $error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            {{Form::password('password',['class'=>'form-control'. ($errors->has('password') ? ' is-invalid' : ''),'placeholder'=>'Password','required'])}}
            @foreach($errors->get('password') as $error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            {{Form::password('password_confirmation',['class'=>'form-control'. ($errors->has('password_confirmation') ? ' is-invalid' : ''),'placeholder'=>'Confirm Password','required'])}}
            @foreach($errors->get('password_confirmation') as $error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-block">
                Reset Password
            </button>
        </div>
    </form>
@endsection

@section('proud-footer')
    <div class="card-footer text-center p-3">
        <a href="{{route('login')}}">Log In</a>
    </div>
@endsection