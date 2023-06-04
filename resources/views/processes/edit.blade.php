@extends('flow.default')

@section('title') Edit sub-process @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveProcess()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Save</a>
            <a href="{{route('processes.show',[(isset($process->pgroup) ? $process->pgroup->id : 0),$process])}}" class="btn btn-outline-primary btn-sm mt-3">Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                {{Form::open(['url' => route('processes.update',[(isset($process->pgroup) ? $process->pgroup->id : 0),$process]), 'method' => 'post','class'=>'mt-3 mb-3','autocomplete'=>'off','id'=>'save_process_form','files'=>true])}}

                    <div class="form-group">
                            {{Form::label('name', 'Name')}}
                            {{Form::text('name',$process->name,['class'=>'form-control form-control-sm'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
                            @foreach($errors->get('name') as $error)
                                <div class="invalid-feedback">
                                    {{ $error }}
                                </div>
                            @endforeach
                    </div>

                    <div class="form-group">
                    {{Form::label('area', 'Area')}}
                        <select multiple="multiple" id="area" name="area[]" class="form-control form-control-sm chosen-select {{($errors->has('name') ? ' is-invalid' : '')}}">
                            @php
                                foreach($areas as $key => $value){
                                    echo '<option value="'.$key.'" '.(in_array($key,$process_areas) ? 'selected' : '').'>'.$value.'</option>';
                                }
                            @endphp
                        </select>
                        @foreach($errors->get('area') as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                        {{Form::label('office', 'Office')}}
                        <select multiple id="office" name="office[]" class="form-control form-control-sm "></select>
                        @foreach($errors->get('office') as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                        {{Form::label('process', 'Process')}}
                        {{Form::select('process',$process_groups,$process->process_group_id,['class'=>'form-control form-control-sm'. ($errors->has('process') ? ' is-invalid' : '')])}}
                        @foreach($errors->get('process') as $error)
                            <div class="invalid-feedback">
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group">
                    {{Form::label('document', 'Document')}}
                    {{Form::file('document',['class'=>'form-control form-control-sm'. ($errors->has('document') ? ' is-invalid' : ''),'placeholder'=>'Document'])}}
                    @foreach($errors->get('document') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                    </div>

                    <div class="form-group">
                        {{Form::label('docfusion_process_id', 'DocFusion Process ID')}}
                        {{Form::text('docfusion_process_id',$process->docfusion_process_id,['class'=>'form-control form-control-sm'. ($errors->has('docfusion_process_id') ? ' is-invalid' : ''),'placeholder'=>'DocFusion Process ID'])}}
                    </div>

                    <div class="form-group">
                        {{Form::label('docfusion_template_id', 'DocFusion Template ID')}}
                        {{Form::text('docfusion_template_id',$process->docfusion_template_id,['class'=>'form-control form-control-sm'. ($errors->has('docfusion_template_id') ? ' is-invalid' : ''),'placeholder'=>'DocFusion Template ID'])}}
                    </div>

                    <div class="form-row">
                    <div class="col-lg-4 text-center">
                        <label><i class="fa fa-circle" style="color: {{$process->getStageHex(0)}}"></i> Not started</label>
                        <div class="form-control">
                            <input name="not_started_colour" type="text" style="background-color:{{$process->getStageHex(0, false)}};" class="not_started_color form-control form-control-sm"  value="{{$process->getStageHex(1, false)}}" title="Stage colour: Not started"/>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center">
                        <label><i class="fa fa-circle" style="color: {{$process->getStageHex(1)}}"></i> Started</label>
                        <div class="form-control">
                            <input name="started_colour" type="text"  style="background-color:{{$process->getStageHex(1, false)}};" class="started_colour form-control form-control-sm" value="{{$process->getStageHex(1, false)}}" title="Stage colour: Started"/>
                        </div>
                    </div>
                    <div class="col-lg-4 text-center">
                        <label><i class="fa fa-circle" style="color: {{$process->getStageHex(2)}}"></i> Completed</label>
                        <div class="form-control">
                            <input name="completed_colour" type="text" style="background-color:{{$process->getStageHex(2, false)}};" class="completed_colour form-control form-control-sm" value="{{$process->getStageHex(2,false)}}" title="Stage colour: Completed"/>
                        </div>
                    </div>
                </div>

                {{Form::close()}}
            </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-js')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.3/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.3/js/bootstrap-colorpicker.min.js"></script>
    <script src="{{asset('chosen/docsupport/init.js')}}" type="text/javascript" charset="utf-8"></script>
    <link rel="stylesheet" href="{{asset('chosen/chosen.min.css')}}">
    <style>
        .colorpicker-2x .colorpicker-saturation {
            width: 200px;
            height: 200px;
        }

        .colorpicker-2x .colorpicker-hue,
        .colorpicker-2x .colorpicker-alpha {
            width: 30px;
            height: 200px;
        }

        .colorpicker-2x .colorpicker-color,
        .colorpicker-2x .colorpicker-color div {
            height: 30px;
        }
    </style>
    <script>
        $(function () {

            $('#area').on('change', function(change, deselected) { //selected OR deselected
                //do something
                console.log('Value Deselected');
            });

            $("#area_chosen").bind("click", function (){
                if($("#area").val() !== null) {
                    let areas = $("#area").val();

                    $('#old_area').val(areas);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        url: '/get_offices',
                        type:"POST",
                        data:{areas:areas},
                        success:function(data){
                            $(document).find('#office').empty();
                            $.each(data, function(key, value) {
                                $(document).find('#office').append($("<optgroup></optgroup>").attr("label",key).attr("id",key.replace(/ /gi,'').toLowerCase()));
                                $.each(value, function(k, v) {
                                    $(document).find('#'+key.replace(/ /gi,'').toLowerCase()).append($("<option></option>").attr("value",v.id).text(v.name));
                                });
                            });

                            $(document).find('#office').trigger('chosen:updated');
                        }
                    });
                }
            });

            // Basic instantiation:
            $('.not_started_color').colorpicker({
                align: 'center',
                format: 'rgba',
                customClass: 'colorpicker-2x',
                sliders: {
                    saturation: {
                        maxLeft: 200,
                        maxTop: 200
                    },
                    hue: {
                        maxTop: 200
                    },
                    alpha: {
                        maxTop: 200
                    }
                }
            }).on('changeColor', function(event) {
                $('.not_started_color').css('background-color', event.color.toString());

            });

            $('.started_colour').colorpicker({
                align: 'center',
                format: 'rgba',
                customClass: 'colorpicker-2x',
                sliders: {
                    saturation: {
                        maxLeft: 200,
                        maxTop: 200
                    },
                    hue: {
                        maxTop: 200
                    },
                    alpha: {
                        maxTop: 200
                    }
                }
            }).on('changeColor', function(event) {
                $('.started_colour').css('background-color', event.color.toString());

            });

            $('.completed_colour').colorpicker({
                align: 'center',
                format: 'rgba',
                customClass: 'colorpicker-2x',
                sliders: {
                    saturation: {
                        maxLeft: 200,
                        maxTop: 200
                    },
                    hue: {
                        maxTop: 200
                    },
                    alpha: {
                        maxTop: 200
                    }
                }
            }).on('changeColor', function(event) {
                $('.completed_colour').css('background-color', event.color.toString());

            });
        });
    </script>
@endsection