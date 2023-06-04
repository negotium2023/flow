@extends('flow.default')

@section('title') {{(isset($process_group->name) ? $process_group->name : 'None')}} {{$type_name}} @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <form autocomplete="off">
                <div class="form-row">
                    <div class="form-group" style="display: inline-block">
                        <div class="input-group input-group-sm">
                            {{Form::search('q',old('query'),['class'=>'form-control form-control-sm search','placeholder'=>'Search...'])}}
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-sm btn-default" style="line-height: 1.35rem !important;"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="ml-2 mt-2">
                        <a href="{{route('processes.create',(isset($process_group->id) ? $process_group->id : '0'))}}?f={{$process_group->form}}" class="btn btn-sm btn-primary float-right ml-1">Add {{$type_name_single}}</a>
                        <a href="{{route('processesgroup.index')}}?f={{$process_group->form}}" class="btn btn-sm btn-outline-primary float-right">Back</a>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                    <table class="table table-bordered table-hover table-sm table-fixed">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Steps</th>
                            <th>Created</th>
                            <th>Modified</th>
                            <th class="last">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($processes as $process)
                            <tr>
                                <td>{{$process->name}}</td>
                                <td>{{count($process->steps)}}</td>
                                <td>{{$process->created_at->diffForHumans()}}</td>
                                <td>{{$process->updated_at->diffForHumans()}}</td>
                                <td class="last">
                                    <a href="javascript:void(0)" onclick="copyProcess({{$process->id}})" class="btn btn-default btn-sm"><i class="fas fa-copy"></i></a>
                                    {{--<a href="{{route("process.copy", $process)}}" class="btn btn-default btn-sm"><i class="fas fa-copy"></i></a>--}}
                                    <a href="{{route('processes.show',[$process_group,$process])}}?t={{$process_type_id}}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> </a>
                                    <a href="{{route('processes.edit',['processgroup' => $process_group,'process' => $process])}}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                    {{Form::open(['url' => route('processes.destroy',['processgroup'=>(isset($process->pgroup) ? $process->pgroup->id : 0),'process' => $process,'processid' => $process]).'?t='.$process_type_id, 'method' => 'delete','style'=>'display:inline;width:fit-content;margin:0px;'])}}
                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    {{Form::close()}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center"><small class="text-muted">No {{$type_name}} match those criteria.</small></td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="copy_process">
        <div class="modal-dialog" style="width:800px !important;max-width:800px;">
            <div class="modal-content">
                <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                    <h5 class="modal-title">Copy Process</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="box-body">
                        <div class="form-group">
                            <input type="hidden" class="form-control" name="email_id" id="copy_process_id" >
                        </div>
                        <div class="form-group mt-3">
                            {{Form::label('process_group', 'Process Group')}}
                            {{Form::select('process_group_id',$process_groups,(isset($process_group->id) ? $process_group->id : '0'),['class'=>'form-control form-control-sm','placeholder'=>'Please select...','id'=>'copy_process_group_id'])}}

                        </div>
                        <div class="form-group">
                            {{Form::label('process_name', 'New Process Name')}}
                            {{Form::text('process_name',null,['class'=>'form-control','placeholder'=>'New Process Name','id'=>'copy_process_name'])}}

                        </div>
                        <div class="form-group text-right">
                            <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                            <button type="button" onclick="saveCopyProcess()" class="btn btn-sm btn-primary">Copy Process</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-js')
    <script>
        function copyProcess(process_id){
            $('#copy_process').modal('show');
            $('#copy_process').find('#copy_process_id').val(process_id);
        }

        function saveCopyProcess() {
            let process_id = $('#copy_process').find('#copy_process_id').val();
            let process_group_id = $('#copy_process').find('#copy_process_group_id').val();
            let process_name = $('#copy_process').find('#copy_process_name').val();

            $('#overlay').fadeIn();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: '/processes/' + process_id + '/copy',
                data: {id:process_id,name:process_name,process_group:process_group_id},
                success: function( data ) {
                    if(data.name !== '') {
                        $('#copy_process').find('#copy_process_id').val('');
                        $('#copy_process').find('#copy_process_name').val('');
                        $('#copy_process').find('#copy_process_group_id').val('');
                        $('#copy_process').modal('hide');

                        window.location.href = "/processes/" + process_group_id + "/" + data.id + "/show?t=1";
                    }
                }
            });
        }
    </script>
@endsection
