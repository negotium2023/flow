@extends('flow.default')

@section('title') Create step for {{$process->name}} @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveStep()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Save</a>
            <a href="{{route('processes.show',[(isset($step->process->pgroup) ? $step->process->pgroup->id : '0'),$process->id])}}" class="btn btn-outline-primary mt-3">Back</a>
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
                        {{Form::open(['url' => route('steps.store',$process), 'method' => 'post','class'=>'mt-3 mb-3 form-inline','id'=>'save_step_form'])}}
                            <div class="form-row w-100">
                                <div class="form-group col-md-12 pt-0 pb-0">
                                    {{Form::label('name', 'Name',['style'=>'justify-content:left !important;'])}}
                                    <div class="col-md-11">
                                    {{Form::text('name',old('name'),['class'=>'form-control form-control-sm w-100'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>'Name'])}}
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
                                        <input name="step_colour" type="text" style="background-color: rgba(242,99,91,.7)" value="rgba(242,99,91,.7)" class="step_colour form-control form-control-sm" title="Step colour"/>

                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    {{Form::label('group', 'Group Step')}}
                                    <div class="col-lg-8">
                                        <div role="radiogroup" class="mt-0">
                                            <input type="radio" class="group_step" value="1" name="group_step" id="group_step-enabled" ref="grouped">
                                            <label for="group_step-enabled">Yes</label><!-- remove whitespace
                                                                    --><input type="radio" class="group_step" value="0" name="group_step" id="group_step-disabled" checked><!-- remove whitespace
                                                                    --><label for="group_step-disabled">No</label>

                                            <span class="selection-indicator"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    {{Form::label('signature', 'Signature Step')}}
                                    <div class="col-lg-8">
                                        <div role="radiogroup" class="mt-0">
                                            <input type="radio" class="signature_step" value="1" name="signature_step" id="signature_step-enabled" ref="grouped">
                                            <label for="signature_step-enabled">Yes</label><!-- remove whitespace
                                                                    --><input type="radio" class="signature_step" value="0" name="signature_step" id="signature_step-disabled" checked><!-- remove whitespace
                                                                    --><label for="signature_step-disabled">No</label>

                                            <span class="selection-indicator"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <hr>

                            <div class="form-row col-sm-12">
                                {{Form::label('activities', 'Activities')}}
                                <blackboard-process-editor ref="step_a" :black-fields="{{$fields}}"></blackboard-process-editor>
                            </div>

                        {{Form::close()}}
                </div>
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