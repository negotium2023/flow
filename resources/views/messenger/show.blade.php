@extends('flow.default')
@section('title') {{ ($thread->subject == '' ? 'Messages' : $thread->subject) }} @endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <a href="{{route('messages')}}" class="btn btn-dark btn-sm float-right"><i class="fa fa-caret-left"></i> Back</a>
    </div>
@endsection
@section('content')
    <div class="content-container">
        <div class="row col-md-12">
            @yield('header')
            <div class="container-fluid">
                <div class="table-responsive">
                    <div class="col-md-12">
                        @each('messenger.partials.messages', $thread->messages, 'message')
                    </div>
                            <div class="col-md-12">
                        @include('messenger.partials.form-message')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection