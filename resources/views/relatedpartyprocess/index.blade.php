@extends('adminlte.default')
@section('title') Related Party Process @endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        @if(!isset($related_party_processes[0]->process))
        <a href="{{route('relatedpartyprocess.create')}}" class="btn btn-dark btn-sm float-right"><i class="fa fa-plus"></i> Related Party Process</a>
        @endif
    </div>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover">
                <thead class="btn-dark">
                    <tr>
                        <th>Process Name</th>
                        <th class="pright">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($related_party_processes as $related_party_process)
                    <tr>
                        <td>{{isset($related_party_process->process->name)?$related_party_process->process->name:''}}</td>
                        <td class="right">
                            <a href="{{route('relatedpartyprocess.show',$related_party_process)}}" class="btn btn-info btn-sm">View</a> <a href="{{route('relatedpartyprocess.edit',$related_party_process)}}" class="btn btn-dark btn-sm">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center">No roles match those criteria.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
