@extends('flow.default')

@section('title') Create Form Section @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="saveFormSection()" class="btn btn-success btn-lg mt-3 ml-2 float-right">Save</a>
            <a href="{{route('forms.show',$forms)}}" class="btn btn-outline-primary btn-sm mt-3">Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                    {{Form::open(['url' => route('form_section.store',$forms), 'method' => 'post','class'=>'mt-3 mb-3 form-inline','id'=>'save_form_section_form'])}}
                    <div class="col-md-12">
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
                                {{Form::label('group_section', 'Group Section')}}
                                <div class="col-lg-8">
                                    <div role="radiogroup" class="mt-0">
                                        <input type="radio" class="group_section" value="1" name="group_section" id="group_section-enabled" ref="grouped">
                                        <label for="group_section-enabled">Yes</label><!-- remove whitespace
                                                                    --><input type="radio" class="group_section" value="0" name="group_section" id="group_section-disabled" checked><!-- remove whitespace
                                                                    --><label for="group_section-disabled">No</label>

                                        <span class="selection-indicator"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                {{Form::label('show_name_in_tab', 'Show name in tab')}}
                                <div class="col-lg-8">
                                    <div role="radiogroup" class="mt-0">
                                        <input type="radio" class="show_name_in_tab" value="1" name="show_name_in_tab" id="show_name_in_tab-enabled" ref="grouped">
                                        <label for="show_name_in_tab-enabled">Yes</label><!-- remove whitespace
                                                                    --><input type="radio" class="show_name_in_tab" value="0" name="show_name_in_tab" id="show_name_in_tab-disabled" checked><!-- remove whitespace
                                                                    --><label for="show_name_in_tab-disabled">No</label>

                                        <span class="selection-indicator"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <hr>
                    <div class="col-md-12">
                        {{Form::label('fields', 'Fields')}}
                        <blackboard-forms-editor  ref="section_a"></blackboard-forms-editor>
                    </div>

                {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-js')
    <script>
        $(function () {
            if($(".group_section").val() === 1){
                vm.$refs.section_a.disen(1);
            }

            $(".group_section").on('click',function () {
                vm.$refs.section_a.disen($(this).val());
            });
        });
    </script>
@endsection