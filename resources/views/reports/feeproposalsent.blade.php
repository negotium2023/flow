@extends('layouts.app')

@section('title') Clients: Fee Proposal sent @endsection

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
                <th>First Date</th>
                <th>Last Date</th>
                <th>Process</th>
            </tr>
            </thead>
            <tbody>
            @forelse($actionable_template_email_data as $data)
                <tr>
                    <td><a href="{{route('clients.show',$data->client)}}">{{$data->client->company}}</a></td>
                    <td>{{Carbon\Carbon::parse($data->min_created_at)->diffForHumans()}}</td>
                    <td>{{Carbon\Carbon::parse($data->max_created_at)->diffForHumans()}}</td>
                    <td>{{$data->client->process->name}}</td>
                </tr>
            @empty

            @endforelse
            </tbody>
        </table>
    </div>
@endsection