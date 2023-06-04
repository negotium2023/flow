@extends('flow.default')

@section('title')
    Capture Client
    {{--Capture {{(request()->client_type != null ? ($crm->label == '' ? $crm->name : $crm->label) : '')}} Client--}}
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="col-md-12 h-100">
            <div class="container-fluid container-title">
                <h3>@yield('title')</h3>
                {{Form::open(['url' => route('clients.create'), 'method' => 'post','autocomplete'=>'off','id'=>'clienttype'])}}

                    @if(count($client_type) > 2)
                        <div class="form-group form-inline col-md-4" style="padding: 0px 0px 0px 20px;margin-top: 5px;">
                            <select name="client_type" onchange="clientType()" class="form-control form-control-sm chosen-select">
                                @foreach($client_type as $ct)
                                    <option value="{{$ct->id}}" {{($crm->id === $ct->id ? 'selected' : '')}}>{{($ct->label == '' ? $ct->name : $ct->label)}}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="form-group form-inline col-md-4" style="padding: 0px 0px 0px 20px;margin-top: -5px;">
                            <div role="radiogroup" class="mt-0">
                                @foreach($client_type as $ct)
                                    <input type="radio" class="group_step" value="{{$ct->id}}" name="client_type" id="crm_{{$ct->order}}" {{($ct->order == 1 ? 'ref="grouped"' : '')}} {{($crm->id === $ct->id ? 'checked' : '')}}><!-- remove whitespace
                                                                        -->
                                    <label for="crm_{{$ct->order}}">{{($ct->label == '' ? $ct->name : $ct->label)}}</label><!-- remove whitespace
                                                                        -->
                                @endforeach
                                    <span class="selection-indicator"></span>
                            </div>
                        </div>
                    @endif
                {{Form::close()}}
                <div class="nav-btn-group mt-2">
                    <button onclick="saveClientDetails()" class="btn btn-primary float-right ml-2">Save</button>
                </div>
            </div>
            <div class="container-fluid">
                <div class="col-md-12 pl-0 pr-0">
                    {{Form::open(['url' => route('clients.store'), 'method' => 'post','autocomplete'=>'off','class'=>'client-capture-content clientdetailsform2','style'=>'display:none;min-width:100%;'])}}
                    <nav class="tabbable">
                        <div class="nav nav-tabs2">
                            @if($crm->show_default == 1)
                            <a class="nav-link {{($crm->show_default == 0 ? '' : 'show active')}}" id="default-tab" data-toggle="tab" href="#default" role="tab" aria-controls="default" aria-selected="false">Default</a>
                            @endif
                            @foreach($forms as $key =>$value)
                                @foreach($value as $section =>$v1)
                                    <a class="nav-link {{(reset($forms) == $value && $crm->show_default == 0 ? 'show active' : '')}}" id="{{strtolower(str_replace(' ','_',$section))}}-tab" data-toggle="tab" href="#{{strtolower(str_replace(' ','_',$section))}}" role="tab" aria-controls="{{strtolower(str_replace(' ','_',$section))}}" aria-selected="true">{{$section}}</a>
                                @endforeach
                            @endforeach
                        </div>
                    </nav>
                    <div class="tab-content2" id="myTabContent" style="height: calc(100vh - 14rem)">
                        <div class="tab-pane fade {{($crm->show_default == 0 ? '' : 'show active')}}" id="default" role="tabpanel" aria-labelledby="default-tab">

                            <div class="col-lg-12 pl-0 pr-0 mt-3">
                                <input type="hidden" name="process" value="{{($config->default_onboarding_process)}}">
                                <input type="hidden" id="crm_id" name="crm" value="{{($crm->id)}}">

                                @if($crm->id == '2')
                                    <table class="table p-0 table-borderless w-100 client-edit mb-0">
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">First Names</span></td>
                                            <td></td>
                                            <td>{{Form::text('first_name',old('first_name'),['class'=>'form-control form-control-sm'. ($errors->has('first_name') ? ' is-invalid' : ''),'placeholder'=>'First Name'])}}
                                            @foreach($errors->get('first_name') as $error)
                                                <div class="invalid-feedback">
                                                    {{$error}}
                                                </div>
                                            @endforeach
                                            </td>
                                        </tr>
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Surname</span></td>
                                            <td></td>
                                            <td>{{Form::text('last_name',old('last_name'),['class'=>'form-control form-control-sm'. ($errors->has('last_name') ? ' is-invalid' : ''),'placeholder'=>'Last Name'])}}
                                            @foreach($errors->get('last_name') as $error)
                                                <div class="invalid-feedback">
                                                    {{$error}}
                                                </div>
                                            @endforeach
                                            </td>
                                        </tr>
                                        {{-- <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">ID/Passport Number</span></td>
                                            <td></td>
                                            <td>{{Form::text('id_number',old('id_number'),['class'=>'form-control form-control-sm'. ($errors->has('id_number') ? ' is-invalid' : ''),'placeholder'=>'ID Number', 'id'=>'id_number'])}}
                                            @foreach($errors->get('id_number') as $error)
                                                <div class="invalid-feedback">
                                                    {{$error}}
                                                </div>
                                            @endforeach
                                            </td>
                                        </tr> --}}
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Email</span></td>
                                            <td></td>
                                            <td>{{Form::email('email',old('email'),['class'=>'form-control form-control-sm'. ($errors->has('email') ? ' is-invalid' : ''),'placeholder'=>'Email'])}}
                                            @foreach($errors->get('email') as $error)
                                                <div class="invalid-feedback">
                                                    {{$error}}
                                                </div>
                                            @endforeach
                                            </td>
                                        </tr>
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Mobile Number</span></td>
                                            <td></td>
                                            <td>{{Form::text('contact',old('contact'),['class'=>'form-control form-control-sm'. ($errors->has('contact') ? ' is-invalid' : ''),'placeholder'=>'Mobile Number'])}}
                                            @foreach($errors->get('contact') as $error)
                                                <div class="invalid-feedback">
                                                    {{$error}}
                                                </div>
                                            @endforeach
                                            </td>
                                        </tr>
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Office Number</span></td>
                                            <td></td>
                                            <td>{{Form::text('contact_office',old('contact_office'),['class'=>'form-control form-control-sm'. ($errors->has('contact_office') ? ' is-invalid' : ''),'placeholder'=>'Office Number'])}}
                                            @foreach($errors->get('contact_office') as $error)
                                                <div class="invalid-feedback">
                                                    {{$error}}
                                                </div>
                                            @endforeach
                                            </td>
                                        </tr>
                                    </table>
                                @else

                                    <div class="form-group">
                                        {{Form::label('company', 'Company Name')}}
                                        {{Form::text('company',old('company'),['class'=>'form-control form-control-sm'. ($errors->has('company') ? ' is-invalid' : ''),'placeholder'=>'Company'])}}
                                        @foreach($errors->get('company') as $error)
                                            <div class="invalid-feedback">
                                                {{$error}}
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('company_reg', 'Company Registration Number')}}
                                        {{Form::text('company_reg',old('company_reg'),['class'=>'form-control form-control-sm'. ($errors->has('company_reg') ? ' is-invalid' : ''),'placeholder'=>'Company Registration Number'])}}
                                        @foreach($errors->get('company_reg') as $error)
                                            <div class="invalid-feedback">
                                                {{$error}}
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('first_name', 'Contact First Names')}}
                                        {{Form::text('first_name',old('first_name'),['class'=>'form-control form-control-sm'. ($errors->has('first_name') ? ' is-invalid' : ''),'placeholder'=>'Contact First Names'])}}
                                        @foreach($errors->get('first_name') as $error)
                                            <div class="invalid-feedback">
                                                {{$error}}
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('last_name', 'Contact Last Names')}}
                                        {{Form::text('last_name',old('last_name'),['class'=>'form-control form-control-sm'. ($errors->has('last_name') ? ' is-invalid' : ''),'placeholder'=>'Contact Last Name'])}}
                                        @foreach($errors->get('last_name') as $error)
                                            <div class="invalid-feedback">
                                                {{$error}}
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('email', 'Contact Email')}}
                                        {{Form::email('email',old('email'),['class'=>'form-control form-control-sm'. ($errors->has('email') ? ' is-invalid' : ''),'placeholder'=>'Contact Email'])}}
                                        @foreach($errors->get('email') as $error)
                                            <div class="invalid-feedback">
                                                {{$error}}
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('contact', 'Contact Mobile Number')}}
                                        {{Form::text('contact',old('contact'),['class'=>'form-control form-control-sm'. ($errors->has('contact') ? ' is-invalid' : ''),'placeholder'=>'Contact Mobile Number'])}}
                                        @foreach($errors->get('contact') as $error)
                                            <div class="invalid-feedback">
                                                {{$error}}
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('contact_office', 'Contact Office Number')}}
                                        {{Form::text('contact_office',old('contact_office'),['class'=>'form-control form-control-sm'. ($errors->has('contact_office') ? ' is-invalid' : ''),'placeholder'=>'Contact Office Number'])}}
                                        @foreach($errors->get('contact_office') as $error)
                                            <div class="invalid-feedback">
                                                {{$error}}
                                            </div>
                                        @endforeach
                                    </div>

                                    <div class="form-group">
                                        {{Form::label('contact_role', 'Contact Role')}}
                                        {{Form::text('contact_role',old('contact_role'),['class'=>'form-control form-control-sm'. ($errors->has('contact_role') ? ' is-invalid' : ''),'placeholder'=>'Contact Role'])}}
                                        @foreach($errors->get('contact_role') as $error)
                                            <div class="invalid-feedback">
                                                {{$error}}
                                            </div>
                                        @endforeach
                                    </div>

                                @endif


                            </div>

                        </div>
                        @foreach($forms as $key =>$value)
                            @foreach($value as $section =>$v1)
                                <div class="tab-pane fade {{(reset($forms) == $value && $crm->show_default == 0 ? 'show active' : '')}} p-3" id="{{strtolower(str_replace(' ','_',$section))}}" role="tabpanel" aria-labelledby="{{strtolower(str_replace(' ','_',$section))}}-tab" style="padding-bottom: 70px !important;height:100%;overflow: auto">
                                    @if($crm->id == '5')
                                        <table class="table p-0 table-borderless w-100 client-edit mb-0">
                                            <td style="vertical-align: middle;width:243px;padding:7px 0px 0px;margin-bottom:0px;padding-left: 0% !important;"><span class="form-label" style="font-size: 14px;opacity:1;">Company</span></td>
                                                <td></td>
                                            <td><select name="parent_client" id="parent_client" class="form-control form-control-sm">
                                                <option value="">Please Select</option>
                                                @foreach($client_dropdown as $cd)
                                                    <option value="{{$cd["id"]}}">{{$cd["name"]}}</option>
                                                @endforeach
                                                </select></td>
                                            </tr>
                                        </table>
                                    @endif
                                    @foreach($v1 as $k1 =>$inputs)
                                        @if(isset($inputs["total_groups"]) && $inputs["total_groups"] > 0)
                                            <input type="hidden" class="max_group" value="{{$inputs['max_group']}}">
                                            <input type="hidden" class="total_groups" value="{{$inputs['total_groups']}}">
                                            @for($i=1;$i <= (int)$inputs['total_groups'];$i++)
                                                <div class="group-{{$i}}" style="{{($inputs['max_group'] != '' && $i <= $inputs['max_group']  ? '' : 'display:none;')}}">
                                                    <table class="table p-0 table-borderless w-100 client-edit">
                                                        @foreach($inputs["inputs"] as $input)
                                                            <tr>

                                                                @if($input['type']=='heading')
                                                                    <td colspan="2"><h4 style="width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' ? $input['color'] : ''}};padding:5px;">{{$input['name']}}</h4></td>
                                                                @elseif($input['type']=='subheading')
                                                                    <td colspan="2"><h5 style="width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' ? $input['color'] : ''}};padding:5px;">{{$input['name']}}</h5></td>
                                                                @else
                                                                    <td style="vertical-align: middle;width:250px;padding:7px 0px 0px;margin-bottom:0px;padding-left: calc(100% - {{$input['level']}}%) !important;background-color:{{$input['color'] != 'hsl(0,0%,0%)' && $input['color'] != null ? $input['color'] : ''}};">
                                                                        <span class="form-label" style="font-size: 14px;opacity:1;">{{$input['name']}}</span><small class="text-muted"> @if($input['kpi']==1) <span class="fa fa-asterisk" title="Activity is required for step completion" style="color:#FF0000"></span> @endif</small></td>
                                                                    <td>
                                                                    <td>
                                                                        @if($input['type'] == 'dropdown')
                                                                            @php

                                                                                $arr = (array)$input['dropdown_items'];
                                                                                $arr2 = (array)$input['dropdown_values'];

                                                                            @endphp
                                                                            <input type="hidden" id="old_{{$input['id']}}" name="old_{{$input['id']}}" value="{{(!empty($arr2) ? implode(',',$arr2) : old($input['id']))}}">
                                                                        @else
                                                                            <input type="hidden" id="old_{{$input['id']}}" name="old_{{$input['id']}}" value="{{old($input['id'])}}">
                                                                        @endif
                                                            @if($input['type']=='text')
                                                                {{Form::text($input['id'],old($input['id']),['class'=>'form-control form-control-sm '. ($input['kpi']==1 ? 'kpi' : ''),'placeholder'=>'Insert text...','spellcheck'=>'true'])}}
                                                            @endif

                                                            @if($input['type']=='percentage')
                                                                <div class="input-group input-group-sm">
                                                                    <input type="number" min="0" step="1" max="100" name="{{$input['id']}}" value="{{(isset($input['value']) ? $input['value'] : old($input['id']))}}" class="form-control form-control-sm {{($input['kpi']==1 ? 'kpi' : '')}}" spellcheck="true" />
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">&percnt;</span>
                                                                    </div>

                                                                </div>
                                                            @endif

                                                            @if($input['type']=='integer')
                                                                <input type="number" min="0" step="1" name="{{$input['id']}}" value="{{(isset($input['value']) ? $input['value'] : old($input['id']))}}" class="form-control form-control-sm {{($input['kpi']==1 ? 'kpi' : '')}}" spellcheck="true" />
                                                            @endif

                                                            @if($input['type']=='amount')
                                                                <div class="input-group input-group-sm">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">R</span>
                                                                    </div>
                                                                    <input type="number" min="0" step="1" name="{{$input['id']}}" value="{{(isset($input['value']) ? $input['value'] : old($input['id']))}}" class="form-control form-control-sm" spellcheck="true" />
                                                                </div>
                                                            @endif

                                                            @if($input['type']=='date')
                                                                <input name="{{$input['id']}}" type="date" min="1900-01-01" max="2030-12-30" value="{{old($input['id'])}}" class="form-control form-control-sm {{($input['kpi']==1 ? 'kpi' : '')}}" placeholder="Insert date..." />
                                                            @endif

                                                            @if($input['type']=='textarea')
                                                                <textarea spellcheck="true" rows="5" name="{{$input['id']}}" class="form-control form-control-sm text-area {{($input['kpi']==1 ? 'kpi' : '')}}"></textarea>
                                                            @endif

                                                            @if($input['type']=='boolean')
                                                                <div class="form-group">
                                                                    <label class="radio-inline"><input type="radio" name="{{$input["id"]}}" value="1" {{(isset($input["value"]) && $input["value"] == 1 ? 'checked' : '')}}><span class="ml-2">Yes</span></label>
                                                                    <label class="radio-inline ml-3"><input type="radio" name="{{$input["id"]}}" value="0" {{(isset($input["value"]) && $input["value"] == 0 ? 'checked' : '')}}><span class="ml-2">No</span></label>
                                                                    {{--{{Form::select($input['id'],[1=>'Yes',0=>'No'],old($input['id']),['class'=>'form-control form-control-sm','placeholder'=>'Please select...'])}}--}}
                                                                </div>
                                                            @endif
                                                            @if($input['type']=='dropdown')

                                                                <select multiple="multiple" name="{{$input["id"]}}[]" class="form-control form-control-sm chosen-select {{($input['kpi']==1 ? 'kpi' : '')}}">
                                                                    @php
                                                                        foreach((array) $arr as $key => $value){
                                                                            echo '<option value="'.$key.'" '.(in_array($key,$arr2) ? 'selected' : '').'>'.$value.'</option>';
                                                                        }
                                                                    @endphp
                                                                </select>
                                                                {{--<div>
                                                                    <small class="form-text text-muted">
                                                                        Search and select multiple entries
                                                                    </small>
                                                                </div>--}}

                                                            @endif
                                                                    </td>
                                                            </tr>
                                                @endif
                                            @endforeach
                                                    </table>
                                                </div>
                                            @endfor
                                                @if(isset($inputs["total_groups"]) && $inputs["total_groups"] > 0)
                                                    <div style="margin-top:10px;padding-bottom: 40px;">
                                                        <input type="button" class="btn btn-sm btn-secondary float-right" id="addGroup" value="Add More">
                                                    </div>
                                                @endif
                                        @else
                                        <table class="table p-0 table-borderless w-100 client-edit">
                                        @foreach($inputs["inputs"] as $input)
                                            <tr>

                                                @if($input['type']=='heading')
                                                    <td colspan="2"><h4 style="width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' ? $input['color'] : ''}};padding:5px;">{{$input['name']}}</h4></td>
                                                @elseif($input['type']=='subheading')
                                                    <td colspan="2"><h5 style="width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' ? $input['color'] : ''}};padding:5px;">{{$input['name']}}</h5></td>
                                                @else
                                                    <td style="vertical-align: middle;width:250px;padding:7px 0px 0px;margin-bottom:0px;padding-left: calc(100% - {{$input['level']}}%) !important;background-color:{{$input['color'] != 'hsl(0,0%,0%)' && $input['color'] != null ? $input['color'] : ''}};">
                                                        <span class="form-label" style="font-size: 14px;opacity:1;">{{$input['name']}}</span><small class="text-muted"> @if($input['kpi']==1) <span class="fa fa-asterisk" title="Activity is required for step completion" style="color:#FF0000"></span> @endif</small></td>
                                                    <td>
                                                    <td>
                                                        @if($input['type'] == 'dropdown')
                                                            @php

                                                                $arr = (array)$input['dropdown_items'];
                                                                $arr2 = (array)$input['dropdown_values'];

                                                            @endphp
                                                            <input type="hidden" id="old_{{$input['id']}}" name="old_{{$input['id']}}" value="{{(!empty($arr2) ? implode(',',$arr2) : old($input['id']))}}">
                                                        @else
                                                            <input type="hidden" id="old_{{$input['id']}}" name="old_{{$input['id']}}" value="{{old($input['id'])}}">
                                                        @endif
                                                        {{--<div style="float: right;margin-right:5px; display: inline-block;margin-top: -3px;padding-bottom: 3px;text-align: right;" class="form-inline clientbasket">
                                                            <input type="checkbox" class="form-check-input" name="add_to_basket[]" --}}{{--id="{{$input['id']}}"--}}{{-- value="{{$input['id']}}">
                                                            <label  for="{{$input['id']}}" class="form-check-label" style="font-weight:normal !important;"> </label>
                                                        </div>--}}
                                                        {{--<div class="clearfix"></div>--}}
                                                        @if($input['type']=='text')
                                                            {{Form::text($input['id'],old($input['id']),['class'=>'form-control form-control-sm '. ($input['kpi']==1 ? 'kpi' : ''),'placeholder'=>'Insert text...','spellcheck'=>'true'])}}
                                                        @endif

                                                        @if($input['type']=='percentage')
                                                            <div class="input-group input-group-sm">
                                                                    <input type="number" min="0" step="1" max="100" name="{{$input['id']}}" value="{{(isset($input['value']) ? $input['value'] : old($input['id']))}}" class="form-control form-control-sm {{($input['kpi']==1 ? 'kpi' : '')}}" spellcheck="true" />
                                                                    <div class="input-group-append">
                                                                        <span class="input-group-text">&percnt;</span>
                                                                    </div>

                                                            </div>
                                                        @endif

                                                        @if($input['type']=='integer')
                                                            <input type="number" min="0" step="1" name="{{$input['id']}}" value="{{(isset($input['value']) ? $input['value'] : old($input['id']))}}" class="form-control form-control-sm {{($input['kpi']==1 ? 'kpi' : '')}}" spellcheck="true" />
                                                        @endif

                                                        @if($input['type']=='amount')
                                                            <div class="input-group input-group-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">R</span>
                                                                </div>
                                                                <input type="number" min="0" step="1" name="{{$input['id']}}" value="{{(isset($input['value']) ? $input['value'] : old($input['id']))}}" class="form-control form-control-sm" spellcheck="true" />
                                                            </div>
                                                        @endif

                                                        @if($input['type']=='date')
                                                            <input name="{{$input['id']}}" type="date" min="1900-01-01" max="2030-12-30" value="{{old($input['id'])}}" class="form-control form-control-sm {{($input['kpi']==1 ? 'kpi' : '')}}" placeholder="Insert date..." />
                                                        @endif

                                                        @if($input['type']=='textarea')
                                                            <textarea spellcheck="true" rows="5" name="{{$input['id']}}" class="form-control form-control-sm text-area {{($input['kpi']==1 ? 'kpi' : '')}}"></textarea>
                                                        @endif

                                                        @if($input['type']=='boolean')
                                                            @if($input['type']=='boolean')
                                                                <div role="radiogroup">
                                                                    <input type="radio" value="1" name="{{$input["id"]}}" id="{{$input["id"]}}-enabled" {{((isset($input["value"]) && $input["value"] == 1) ? 'checked' : '')}}>
                                                                    <label for="{{$input["id"]}}-enabled">Yes</label><!-- remove whitespace
                                                                    --><input type="radio" value="0" name="{{$input["id"]}}" id="{{$input["id"]}}-disabled" {{((isset($input["value"]) && $input["value"] == 1) ? '' : 'checked')}}><!-- remove whitespace
                                                                    --><label for="{{$input["id"]}}-disabled">No</label>

                                                                    <span class="selection-indicator"></span>
                                                                </div>
                                                            @endif
                                                        @endif
                                                        @if($input['type']=='dropdown')

                                                            <select multiple="multiple" name="{{$input["id"]}}[]" class="form-control form-control-sm chosen-select {{($input['kpi']==1 ? 'kpi' : '')}}">
                                                                @php
                                                                    foreach((array) $arr as $key => $value){
                                                                        echo '<option value="'.$key.'" '.(in_array($key,$arr2) ? 'selected' : '').'>'.$value.'</option>';
                                                                    }
                                                                @endphp
                                                            </select>
                                                            {{--<div>
                                                                <small class="form-text text-muted">
                                                                    Search and select multiple entries
                                                                </small>
                                                            </div>--}}

                                                        @endif
                                                    </td>
                                            </tr>
                                            @endif
                                        @endforeach
                                            </table>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        @endforeach

                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalProcess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="row">
                        <div class="md-form col-sm-12 mb-3 text-left message">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-js')
    <script>
        /*$(function(){*/
            /*$(document).on('chosen:ready', function() {*/
        $(document).find('input[name ="client_type"]').on('click',function (){
            clientType();
        });
        $('.chosen-select').chosen().on('chosen:showing_dropdown', function () {
            $('select[name ="client_type"]').on('change',function (){
                clientType();
            });
        });
            /*});*/
        /*});*/

        $('#addGroup').on('click', function() {
            //var cur = $(this).attr('class').match(/\d+$/)[0];
            let cur = parseInt($("#max_group").val());
            let next = cur+1;
            $('.group-'+next).css('display','table');
            $("#max_group").val(next)
        });

        function clientType() {
            $('.client-capture-content').hide();
            $('#overlay').fadeIn();
            $('#clienttype').submit();
        }
    </script>
@endsection