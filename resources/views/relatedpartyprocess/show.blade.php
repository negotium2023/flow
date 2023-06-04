@extends('adminlte.default')
@section('title') Create Related Party Process @endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="btn-toolbar float-right">
            <div class="btn-group mr-2">
                <a href="{{route('relatedpartyprocess.index')}}" class="btn btn-dark btn-sm"><i class="fa fa-caret-left"></i> Back</a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="table-responsive">
            {{Form::open(['url' => route('relatedpartyprocess.store'), 'method' => 'post','class'=>'mt-3', 'files' => true])}}
            <table class="table table-bordered table-sm table-hover">
                <thead class="btn-dark">
                <tr>
                    <th colspan="2">Related Party Processes</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Process Name</td>
                    <td>
                        {{Form::select('process_id',$related_party_processes_drop_down, $related_party_process->process_id,['class'=>'form-control form-control-sm  col-sm-12'. ($errors->has('process_id') ? ' is-invalid' : ''), 'disabled' => 'disabled'])}}
                        @foreach($errors->get('process_id') as $error)
                            <div class="invalid-feedback">
                                {{$error}}
                            </div>
                        @endforeach
                    </td>
                </tr>
                </tbody>
            </table>
            {{Form::close()}}
        </div>
    </div>
@endsection
