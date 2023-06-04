@extends('layouts.app')

@section('title') Client Reports @endsection

@section('header')
    <h1><i class="fa fa-line-chart"></i> @yield('title')</h1>
    <a href="" class="btn btn-outline-light float-right"><i class="fa fa-download"></i> PDF</a>
@endsection

@section('content')
    {{Form::open(['url' => route('reports.clientreports'), 'method' => 'get','id'=>'clientreportsform'])}}

    <div class="table-responsive mt-3">
        <table class="table table-bordered table-sm table-hover text-center">
            <thead class="thead-light">
            <tr>
                <th>{{Form::text('q',old('q'),['class'=>'form-control form-control-sm','placeholder'=>'Search client...'])}}</th>
                <th>{{Form::select('reports',[1 => 'Outstandng Documents', 0 => 'Not Progressing'],old('reports'),['class'=>'form-control form-control-sm'])}}</th>
                <th>{{Form::select('s',$steps,old('s'),['class'=>'form-control form-control-sm'])}}</th>
                <th>{{Form::select('sloa',$options,old('sloa'),['class'=>'form-control form-control-sm'])}}</th>
                <th>{{Form::select('loe',$options,old('loe'),['class'=>'form-control form-control-sm'])}}</th>
                <th>{{Form::select('crf',$options,old('crf'),['class'=>'form-control form-control-sm'])}}</th>
                <th>{{Form::select('aml',$options,old('aml'),['class'=>'form-control form-control-sm'])}}</th>
            </tr>
            <tr>
                <th class="text-left">Name</th>
                <th class="text-left">Report</th>
                <th class="text-left">Step</th>
                <th>SLOA</th>
                <th>LOE</th>
                <th>CRF</th>
                <th>AML</th>
            </tr>
            </thead>
            <tbody>
            @forelse($clients as $client)
                <tr>
                    <td class="text-left"><a href="{{route('clients.show',$client['id'])}}">{{$client['name']}}</a></td>
                    <td class="text-left">{{$client['is_progressing'] == 1?'Outstandng Documents':'Not Progressing'}}</td>
                    <td class="text-left">{{$client['step']}}</td>
                    <td class="@if($client['sloa']==1) table-success @endif @if($client['sloa']==2) table-danger @endif @if($client['sloa']==3) table-warning @endif"><i class="fa @if($client['sloa']==1) fa-check @endif @if($client['sloa']==2) fa-times @endif @if($client['sloa']==3) fa-clock-o @endif"></td>
                    <td class="@if($client['loe']==1) table-success @endif @if($client['loe']==2) table-danger @endif @if($client['loe']==3) table-warning @endif"><i class="fa @if($client['loe']==1) fa-check @endif @if($client['loe']==2) fa-times @endif @if($client['loe']==3) fa-clock-o @endif"></td>
                    <td class="@if($client['crf']==1) table-success @endif @if($client['crf']==2) table-danger @endif @if($client['crf']==3) table-warning @endif"><i class="fa @if($client['crf']==1) fa-check @endif @if($client['crf']==2) fa-times @endif @if($client['crf']==3) fa-clock-o @endif"></td>
                    <td class="@if($client['aml']==1) table-success @endif @if($client['aml']==2) table-danger @endif @if($client['aml']==3) table-warning @endif"><i class="fa @if($client['aml']==1) fa-check @endif @if($client['aml']==2) fa-times @endif @if($client['aml']==3) fa-clock-o @endif"></td>
            @empty
                <tr>
                    <td colspan="100%" class="text-center">
                        <small class="text-muted">No clients match those criteria.</small>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{Form::close()}}

    <small class="text-muted">Found <b>{{$clients->count()}}</b> clients matching those criteria.</small>
@endsection

@section('extra-js')
    <script>
        $('select[name="reports"], select[name="s"], select[name="sloa"], select[name="loe"], select[name="crf"] ,select[name="aml"]').change(function () {
            $('#clientreportsform').submit();
        });
    </script>
@endsection