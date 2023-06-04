@extends('adminlte.default')

@section('title') View Email Log @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <a href="{{route('emaillogs.index')}}" class="btn btn-dark btn-sm float-right"><i class="fa fa-caret-left"></i> Back</a>
    </div>
@endsection

@section('content')
    <div class="container-fluid">

        <hr>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover">
                <tbody>
                @foreach($emails as $result)
                    <tr>
                        <th>Date Sent:</th>
                        <td>{{$result->date}}</td>
                    </tr>
                    <tr>
                        <th>To:</th>
                        <td>{{$result->to}}</td>
                    </tr>
                    <tr>
                        <th>Subject:</th>
                        <td>{{$result->subject}}</td>
                    </tr>
                    <tr>
                        <th>Body:</th>
                        <td class="email_body">{!! $result->body !!}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
