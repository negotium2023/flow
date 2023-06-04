@extends('client.show')

@section('title') Members @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
        </div>
    </div>
@endsection

@section('tab-content')
    <div class="client-detail p-0 h-100">
        <div class="content-container m-0 p-0">
            @yield('header')
            <div class="container-fluid h-100 overflow-hidden">
                <table class="table table-bordered table-hover table-sm table-fixed" style="max-height: calc(100% - 30px);">
                    <thead>
                    <tr>
                        <th nowrap>@sortablelink('first_name', 'Member Name')</th>
                        <th nowrap>@sortablelink('email', 'Email')</th>
                        <th nowrap>@sortablelink('contact', 'Contact')</th>
                    </tr>
                    </thead>
                    <tbody>
                @foreach($members as $member)
                    <tr>
                        <td><a href="{{route('clients.overview',[$member->id,$member->process_id,$member->step_id])}}">{{$member->first_name}} {{$member->last_name}}</a></td>
                        <td>{{$member->email}}</td>
                        <td>{{$member->contact}}</td>
                    </tr >
                @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection