@extends('layouts.app')

@section('title') Referrer Report @endsection

@section('header')
    <h1><i class="fa fa-line-chart"></i> @yield('title')</h1>
    <a href="" class="btn btn-outline-light float-right"><i class="fa fa-download"></i> PDF</a>
@endsection

@section('content')
    <form id="referrerreportform" class="form-inline mt-3">
        Referrer&nbsp;{{Form::select('referrer',$referrer_options ,old('r'),['class'=>'form-control form-control-sm'])}}
    </form>
    <hr>
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-sm table-hover">
            <thead class="thead-light">
            <tr>
                <th>Client Name</th>
                <th>Company Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Referrer Name</th>
                <th>Referrer Email</th>
                <th>Referrer Contact</th>
            </tr>
            </thead>
            <tbody>
            @forelse($clients as $client)
                <tr>
                    <td>{{$client->first_name}} {{$client->last_name}}</td>
                    <td><a href="{{ route('clients.show',$client)}}">{{$client->company}}</a></td>
                    <td>{{$client->email}}</td>
                    <td>{{$client->contact}}</td>
                    <td>{{isset($client->referrer->first_name)?$client->referrer->first_name:''}} {{isset($client->referrer->last_name)?$client->referrer->last_name:''}}</td>
                    <td>{{isset($client->referrer->email)?$client->referrer->email:''}}</td>
                    <td>{{isset($client->referrer->contact)?$client->referrer->contact:''}}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">No results were found for this query.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <small class="text-muted">Found <b>{{$clients->count()}}</b> clients matching those criteria.</small>
@endsection
@section('extra-js')
    <script>
        $('select[name="referrer"]').change(function () {
            $('#referrerreportform').submit();
        });
    </script>
@endsection