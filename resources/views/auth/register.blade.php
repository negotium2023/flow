@extends('layouts.proud')

@section('proud-form')
    <form class="form-horizontal" method="POST" action="{{ route('register') }}">
        {{ csrf_field() }}

        <div class="form-group">
            {{Form::label('first_name','First name')}}
            {{Form::text('first_name',old('first_name'),['class'=>'form-control'. ($errors->has('first_name') ? ' is-invalid' : ''),'placeholder'=>'First name','required','autofocus'])}}
            @foreach($errors->get('first_name') as $error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            {{Form::label('last_name','Last name')}}
            {{Form::text('last_name',old('last_name'),['class'=>'form-control'. ($errors->has('last_name') ? ' is-invalid' : ''),'placeholder'=>'Last name','required'])}}
            @foreach($errors->get('last_name') as $error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            {{Form::label('email','Email address')}}
            {{Form::email('email',old('email'),['class'=>'form-control'. ($errors->has('email') ? ' is-invalid' : ''),'placeholder'=>'Email address','required'])}}
            @foreach($errors->get('email') as $error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            {{Form::label('password','Password')}}
            {{Form::password('password',['class'=>'form-control'. ($errors->has('password') ? ' is-invalid' : ''),'placeholder'=>'Password','required'])}}
            @foreach($errors->get('password') as $error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            {{Form::label('password_confirmation','Confirm Password')}}
            {{Form::password('password_confirmation',['class'=>'form-control'. ($errors->has('password_confirmation') ? ' is-invalid' : ''),'placeholder'=>'Confirm Password','required'])}}
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-block">
                Register
            </button>
        </div>
    </form>
@endsection

@section('proud-footer')
    <div class="card-footer text-center p-3">
        Already have an account? <a href="{{route('login')}}">Log In</a>
    </div>
@endsection
