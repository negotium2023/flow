@extends('adminlte.default')

@section('title') Email Logs @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <form class="form-inline mt-3">
            Show &nbsp;
            {{Form::select('s',['all'=>'All','mine'=>'My','company'=>'Branch'],old('selection'),['class'=>'form-control form-control-sm'])}}
            &nbsp; matching &nbsp;
            <div class="input-group input-group-sm">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fa fa-search"></i>
                    </div>
                </div>
                {{Form::text('q',old('query'),['class'=>'form-control form-control-sm','placeholder'=>'Search...'])}}
            </div>
            <button type="submit" class="btn btn-sm btn-secondary ml-2 mr-2"><i class="fa fa-search"></i> Search</button>
            <a href="{{route('emaillogs.index')}}" class="btn btn-sm btn-info"><i class="fa fa-eraser"></i> Clear</a>
        </form>

        <hr>

        <div class="table-responsive">
            <table class="table table-bordered table-sm table-hover">
                <thead class="btn-dark">
                <tr>
                    <th>Subject</th>
                    <th>To</th>
                    <th class="last">Action</th>
                </tr>
                </thead>
                <tbody class="blackboard-locations">
                @foreach($emails as $result)
                    <tr>
                        <td><a href="{{route('emaillogs.show',$result)}}">{{$result->subject}}</a></td>
                        <td>{{$result->to}}</td>
                        <td class="last">
                            <span class="float-right text-muted"><small><i class="fa fa-clock-o"></i> {{$result->date}}</small></span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
