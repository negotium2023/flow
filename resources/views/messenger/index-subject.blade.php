@extends('flow.default')
@section('title') Messages @endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <a href="{{route('messages.create')}}" class="btn btn-dark btn-sm float-right"><i class="fa fa-plus"></i> New Message</a>
    </div>
@endsection
@section('content')
    <div class="content-container">
        <div class="row col-md-12">
            @yield('header')
            <div class="container-fluid">
                <div class="table-responsive">
                    @include('messenger.partials.flash')

                    @each('messenger.partials.thread', $threads, 'thread', 'messenger.partials.no-threads')
                </div>
            </div>
        </div>
    </div>
@endsection