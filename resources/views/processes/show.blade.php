@extends('flow.default')

@section('title') {{$process->name}} @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="{{route('processes.index',$processgroup)}}" class="btn btn-outline-primary mt-2">Back</a>
            <a href="{{route('steps.create',$process)}}" class="btn btn-sm btn-primary mt-2 ml-2 float-right">Add Step</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                <div class="col-lg-8 d-inline-block">
                            <ul>
                                <dt>
                                    Colours
                                </dt>
                                <dd>
                                    <div class="row text- ml-0 mr-0">
                                        <div class="col"><i class="fa fa-circle" style="color: {{$process->getStageHex(0)}}"></i> Not-started</div>
                                        <div class="col"><i class="fa fa-circle" style="color: {{$process->getStageHex(1)}}"></i> Started</div>
                                        <div class="col"><i class="fa fa-circle" style="color: {{$process->getStageHex(2)}}"></i> Completed</div>
                                    </div>
                                </dd>
                                <dt>
                                    Steps
                                </dt>
                                <dd>
                                    <div class="table-responsive ">
                                        <table class="table table-bordered table-sm table-hover">
                                            <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Activities</th>
                                                <th class="last">Move</th>
                                                <th class="last">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse($process->steps as $step)
                                                <tr>
                                                    <td>{{$step->name}}</td>
                                                    <td>{{$step->activities()->count()}}</td>
                                                    <td class="last">
                                                        <a href="#" title="Move activity up" onclick="document.querySelector('.moveform-up-{{$step->id}}').submit()"><i class="fa fa-arrow-up"></i></a>
                                                        |
                                                        <a href="#" title="Move activity down" onclick="document.querySelector('.moveform-down-{{$step->id}}').submit()"><i class="fa fa-arrow-down"></i></a>
                                                        {{Form::open(['url' => route('steps.move',$step), 'method' => 'post','class'=>'moveform-up-'.$step->id])}}
                                                        {{Form::hidden('direction','up')}}
                                                        {{Form::close()}}
                                                        {{Form::open(['url' => route('steps.move',$step), 'method' => 'post','class'=>'moveform-down-'.$step->id])}}
                                                        {{Form::hidden('direction','down')}}
                                                        {{Form::close()}}
                                                    </td>
                                                    <td class="last">
                                                        <a href="{{route('steps.edit',$step)}}" class="btn btn-success btn-sm"><i class="fa fa-pencil-alt"></i></a>
                                                        <a href="#" onclick="document.querySelector('.deleteform-{{$step->id}}').submit()" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                                        {{Form::open(['url' => route('steps.destroy',$step), 'method' => 'delete','class'=>'deleteform-'.$step->id])}}
                                                        {{Form::close()}}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="100%" class="text-center">
                                                        <small class="text-muted">No Steps created yet.</small>
                                                    </td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </dd>
                            </ul>
                </div>
                <div class="col-lg-4 d-inline-block float-right">
                    <div class="card">
                        <div class="card-body">
                            <ul class="mr-5">
                                <dt>
                                    Assigned Offices
                                </dt>
                                <dd>
                                    <ul class="pl-4">
                                        @foreach($process->process_area as $area)
                                            @if(isset($area->office->name))
                                                <li>
                                                    {{$area->office->name}}
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </dd>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection