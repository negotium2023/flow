@extends('layouts.app')

@section('title') AML forms upload @endsection

@section('header')
    <h1><i class="fa fa-line-chart"></i> @yield('title')</h1>
    <a href="" class="btn btn-outline-light float-right"><i class="fa fa-download"></i> PDF</a>
@endsection

@section('content')
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-sm table-hover">
            <thead class="thead-light">
            <tr>
                <th>Client</th>
                <th>Process</th>
            </tr>
            </thead>
            <tbody>
            @forelse($clients as $client)
                <tr>
                    <td><a href="{{ route('clients.show',$client)}}">{{$client->company}}</a></td>
                    <td>{{$client->process->name}}</td>
                </tr>
            @empty

            @endforelse
            </tbody>
        </table>
    </div>
@endsection