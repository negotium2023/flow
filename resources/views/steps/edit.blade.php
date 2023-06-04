@extends('flow.default')

@section('title') Edit step @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveStep()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Save</a>
            <a href="{{route('processes.show',[(isset($step->process->pgroup) ? $step->process->pgroup->id : '0'),$step->process])}}" class="btn btn-outline-primary mt-3">Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                    <div class="col-md-12">

                        {{Form::open(['url' => route('steps.update',$step), 'method' => 'put','class'=>'mt-3 mb-3 form-inline','id'=>'save_step_form'])}}
                        <div class="form-row w-100">
                            <div class="form-group col-md-12 pt-0 pb-0">
                                {{Form::hidden('process_id',$process_id,['class'=>'form-control'])}}
                                {{Form::label('name', 'Name',['style'=>'justify-content:left !important;'])}}
                                <div class="col-md-11">
                                    {{Form::text('name',$step->name,['class'=>'form-control form-control-sm w-100'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
                                    @foreach($errors->get('name') as $error)
                                        <div class="invalid-feedback">
                                            {{ $error }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="form-row w-100">
                            <div class="form-group col-md-4">
                                {{Form::label('colour', 'Colour')}}
                                <div class="col-lg-4 text-center">
                                    <input name="step_colour" type="text" style="background-color: {{$step->colour}}" value="{{$step->colour}}" class="step_colour form-control form-control-sm" title="Step colour"/>
                                </div>
                            </div>
                        <div class="form-group col-md-4">
                            {{Form::label('group', 'Group Step')}}
                            <div class="col-lg-8">
                                <div role="radiogroup" class="mt-0">
                                    <input type="radio" class="group_step" value="1" name="group_step" id="group_step-enabled" ref="grouped" {{($step->group == 1 ? 'checked' : '')}}>
                                    <label for="group_step-enabled">Yes</label><!-- remove whitespace
                                                                    --><input type="radio" class="group_step" value="0" name="group_step" id="group_step-disabled" {{($step->group != 1 ? 'checked' : '')}}><!-- remove whitespace
                                                                    --><label for="group_step-disabled">No</label>

                                    <span class="selection-indicator"></span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            {{Form::label('signature', 'Signature Step')}}
                            <div class="col-lg-8">
                                <div role="radiogroup" class="mt-0">
                                    <input type="radio" class="signature_step" value="1" name="signature_step" id="signature_step-enabled" ref="grouped" {{($step->signature == 1 ? 'checked' : '')}}>
                                    <label for="signature_step-enabled">Yes</label><!-- remove whitespace
                                                                    --><input type="radio" class="signature_step" value="0" name="signature_step" id="signature_step-disabled" {{($step->signature != 1 ? 'checked' : '')}}><!-- remove whitespace
                                                                    --><label for="signature_step-disabled">No</label>

                                    <span class="selection-indicator"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>

                    <div class="form-row col-sm-12">
                        {{Form::label('activities', 'Activities')}}
                        <blackboard-process-editor  ref="step_a" :black-activities="{{$activities}}" :black-fields="{{$fields}}"></blackboard-process-editor>
                    </div>
                        {{Form::close()}}
                    </div>
            </div>
        </div>
        </div>
</div>
@endsection
@section('extra-css')
    <link rel="stylesheet" href="{{asset('chosen/chosen.min.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <style>
        a:focus{
            outline:none !important;
            border:0px !important;
        }

        .activity a{
            color: rgba(0,0,0,0.5) !important;
        }

        .activity a.dropdown-item {
            color:#212529 !important;
        }

        .btn-comment{
            padding: .25rem .25rem;
            font-size: .575rem;
            line-height: 1;
            border-radius: .2rem;
        }

        .modal-dialog {
            max-width: 700px;
            margin: 1.75rem auto;
            min-width: 500px;
        }

        .modal .chosen-container, .modal .chosen-container-multi{
            width:98% !important;
        }

        .chosen-container, .chosen-container-multi{
            line-height: 30px;
            width:98% !important;
        }

        .modal-open .modal{
            padding-right: 0px !important;
        }

        .progress { position:relative; width:100%; border: 1px solid #7F98B2; padding: 1px; border-radius: 3px; display:none; }
        .bar { background-color: #B4F5B4; width:0%; height:25px; border-radius: 3px; }
        .percent { position:absolute; display:inline-block; top:3px; left:48%; color: #7F98B2;}
    </style>
@endsection
@section('extra-js')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.3/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.3.3/js/bootstrap-colorpicker.min.js"></script>
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
            $(".group_step").on('click',function () {
                vm.$refs.step_a.disen($(this).val());
            });
            // Basic instantiation:
            $('.step_colour').colorpicker({
                align: 'center',
                input: $('.step_colour2'),
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
                $('.step_colour').css('background-color', event.color.toString());

            });
        });
    </script>
@endsection