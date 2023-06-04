@extends('client.show')

@section('tab-content')
    <div class="client-detail">
        <div class="container-fluid detail-nav">
            <nav class="tabbable">
                <div class="nav nav-tabs" id="client-tabs">
                    @if($client->crm_id == '4')
                    <a class="nav-link" id="personal_detail-tab" data-toggle="tab" href="#personal_detail-pt" role="tab" aria-controls="default" aria-selected="false">Default</a>
                    @else
                        <a class="nav-link" id="personal_detail-tab" data-toggle="tab" href="#personal_detail-pt" role="tab" aria-controls="default" aria-selected="false">Default</a>
                    @endif
                    @forelse($client_details as $data => $tab)
                        @foreach($tab as $name => $input)

                            <a class="nav-link" id="{{strtolower(str_replace(' ','_',str_replace('&','',$name)))}}-tab" data-toggle="tab" href="#{{strtolower(str_replace(' ','_',str_replace('&','',$name)))}}-t" role="tab" aria-controls="default" aria-selected="false">{{$name}}</a>
                        @endforeach
                    @empty
                        {{-- <a class="nav-link" id="personal_details-tab" data-toggle="tab" href="#personal_details" role="tab" aria-controls="default" aria-selected="false">Personal Details</a> --}}
                    @endforelse
                </div>
            </nav>
            <div class="nav-btn-group" style="top:6rem;">
                <a href="{{route('clients.edit',[$client,$process_id,$step['id'],$is_form])}}" class="btn btn-primary float-right">Edit Details</a>
            </div>
        </div>
        <div class="tab-content" id="myTabContent" style="margin-top: 4rem;">
        <div class="tab-pane fade p-3" id="personal_detail-pt" role="tabpanel" aria-labelledby="personal_detail-tab">
                                @if($client->crm_id == '2')
                                    <table class="table p-0 table-borderless w-100 client-edit mb-0">
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">First Names</span></td>
                                            <td></td>
                                            <td>{{$client->first_name}}
                                            </td>
                                        </tr>
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Last Name</span></td>
                                            <td></td>
                                            <td>{{$client->last_name}}
                                            </td>
                                        </tr>
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">ID/Passport Number</span></td>
                                            <td></td>
                                            <td>{{$client->id_number}}
                                            </td>
                                        </tr>
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Email</span></td>
                                            <td></td>
                                            <td>{{$client->email}}
                                            </td>
                                        </tr>
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Mobile Number</span></td>
                                            <td></td>
                                            <td>{{$client->contact}}
                                            </td>
                                        </tr>
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Office Number</span></td>
                                            <td></td>
                                            <td>{{$client->contact_office}}
                                            </td>
                                        </tr>
                                    </table>
                                @else
                                    <table class="table p-0 table-borderless w-100 client-edit mb-0">
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Company Name</span></td>
                                            <td></td>
                                            <td>{{$client->company}}</td>
                                        </tr>
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Company Registration Number</span></td>
                                        <td></td>
                                        <td>{{$client->company_registration_number}}</td>
                                        </tr>
                                        <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Contact Firstname</span></td>
                                            <td></td>
                                            <td>{{$client->first_name}}</td>
                                        </tr>
                                    <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Contact Last Name</span></td>
                                        <td></td>
                                        <td>{{$client->last_name}}</td>
                                    </tr>
                                    <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Contact Email</span></td>
                                    <td></td>
                                    <td>{{$client->email}}</td>
                                    </tr>
                                    <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Contact Mobile Number</span></td>
                                    <td></td>
                                    <td>{{$client->contact}}</td>
                                    </tr>
                                    <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Contact Office Number</span></td>
                                    <td></td>
                                    <td>{{$client->contact_office}}</td>
                                    </tr>
                                    <tr><td style="width:250px;vertical-align: middle;"><span class="form-label" style="font-size: 14px;opacity:1;">Contact Role</span></td>
                                    <td></td>
                                    <td>{{$client->contact_role}}</td>
                                    </tr>
                                    </table>
                                @endif
</div>
            @forelse($client_details as $data => $tab)
                @foreach($tab as $name => $sections)
                    <div class="tab-pane fade p-3" id="{{strtolower(str_replace(' ','_',str_replace('&','',$name)))}}-t" role="tabpanel" aria-labelledby="{{strtolower(str_replace(' ','_',str_replace('&','',$name)))}}-tab">
                        <div class="row grid-items">
                            
                                
                            @foreach($sections["data"] as $key => $value)
                                @if($data == '1000')

                                    <div class="col-md-6 float-left">
                                        <div class="card p-0 m-0" style="height: 100px;min-height: 100px;border: 1px solid #ecf1f4;margin-bottom:0.75rem;">
                                            <div class="d-table" style="width: 100%;">
                                                <div class="grid-icon">
                                                    <i class="far fa-file-alt"></i>
                                                </div>
                                                <div class="grid-text">
                                                    <span class="grid-heading">{{$value["name"]}}</span>
                                                    Last Updated: 00/00/0000
                                                </div>
                                                <div class="grid-btn">
                                                    <a href="javascript:void(0)" class="btn btn-outline-primary btn-block" data-toggle="modal" data-target="#my{{strtolower(str_replace(' ','_',str_replace('&','',$value["name"])))}}Modal">View</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal" id="my{{strtolower(str_replace(' ','_',str_replace('&','',$value["name"])))}}Modal">
                                        <div class="modal-dialog">
                                            <div class="modal-content">

                                                <!-- Modal Header -->
                                                <div class="modal-header">
                                                    <h4 class="modal-title">{{$value["name"]}}</h4>
                                                    <button type="button" class="close" data-dismiss="modal">×</button>
                                                </div>

                                                <!-- Modal body -->
                                                <div class="modal-body">
                                                    @foreach($value["inputs"] as $input)
                                                        @if($input['type']=='heading')
                                                            <h4 style="display:inline-block;width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' ? $input['color'] : ''}};padding:5px;">{{$input['name']}}</h4>
                                                        @elseif($input['type']=='subheading')
                                                            <h5 style="display:inline-block;width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' ? $input['color'] : ''}};padding:5px;">{{$input['name']}}</h5>
                                                        @else
                                                            <div style="display:inline-block;padding:7px;margin-bottom:7px;width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' && $input['color'] != null ? $input['color'] : ''}};">
                                                                <div>
                                                                    <span class="form-label">{{$input['name']}}</span>
                                                                </div>
                                                                <div class="form-text">
                                                                    @if(isset($input['value']))
                                                                        @if($input['type'] == 'dropdown')
                                                                            @php

                                                                                $arr = (array)$input['dropdown_items'];
                                                                                $arr2 = (array)$input['dropdown_values'];

                                                                            @endphp
                                                                            @php
                                                                                foreach((array) $arr as $key => $value){
                                                                                    if(in_array($key,$arr2)){
                                                                                        echo $value.'<br />';
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                        @elseif($input['type'] == 'boolean')
                                                                            {{($input['value'] == '1' ? 'Yes' : 'No')}}
                                                                        @else
                                                                            {{$input['value']}}
                                                                        @endif
                                                                    @else
                                                                        <small><i>No value captured.</i></small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>

                                                <!-- Modal footer -->
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Close</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                @else

                                    @if(isset($value["grouping"]))
                                        <input type="hidden" class="max_group" value="{{$value['max_group']}}">
                                        <input type="hidden" class="total_groups" value="{{$value['total_groups']}}">
                                        @for($i=1;$i <= (int)$value['total_groups'];$i++)
                                            <div class="col-md-12 float-left mb-1 group-{{$i}}" style="{{($i % 3 == 0 ? '' : 'border-right:1px solid #eefafd;')}}{{($value['max_group'] != '' && $i <= $value['max_group']  ? '' : 'display:none;')}}">

                                                <h5>{{$value['group_label']}} {{$number_to_word[$i]}}</h5>
                                                @if($i <= $value['max_group'])
                                                    @foreach($value["grouping"][$i]["inputs"] as $input)
                                                        @if($input['type']=='heading')
                                                            <h4 style="display:inline-block;width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' ? $input['color'] : ''}};padding:5px;">{{$input['name']}}</h4>
                                                        @elseif($input['type']=='subheading')
                                                            <h5 style="display:inline-block;width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' ? $input['color'] : ''}};padding:5px;">{{$input['name']}}</h5>
                                                        @else
                                                            <div class="col-md-6 float-left" style="display:inline-block;padding:7px;margin-bottom:7px;width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' && $input['color'] != null ? $input['color'] : ''}};">
                                                                <div>
                                                                    <span class="form-label">{{$input['name']}}</span>
                                                                </div>
                                                                <div class="form-text">
                                                                    @if(isset($input['value']))
                                                                        @if($input['type'] == 'dropdown')
                                                                            @php

                                                                                $arr = (array)$input['dropdown_items'];
                                                                                $arr2 = (array)$input['dropdown_values'];

                                                                            @endphp
                                                                            @php
                                                                                foreach((array) $arr as $key2 => $value2){
                                                                                    if(in_array($key2,$arr2)){
                                                                                        echo $value2.'<br />';
                                                                                    }
                                                                                }
                                                                            @endphp
                                                                        @elseif($input['type'] == 'boolean')
                                                                            {{($input['value'] == '1' ? 'Yes' : 'No')}}
                                                                        @else
                                                                            {{$input['value']}}
                                                                        @endif
                                                                    @else
                                                                        <small><i>No value captured.</i></small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    {{Form::open(['url' => route('clients.savedetail', $client), 'method' => 'post','autocomplete'=>'off','class'=>'clientdetailsform'])}}
                                                    @foreach($value["grouping"][$i]["inputs"] as $input)
                                                        @if($input['type'] == 'dropdown')
                                                            @php

                                                                $arr3 = (array)$input['dropdown_items'];
                                                                $arr23 = (array)$input['dropdown_values'];

                                                            @endphp
                                                            <input type="hidden" id="old_{{$input['id']}}" name="old_{{$input['id']}}" value="{{(!empty($arr23) ? implode(',',$arr23) : old($input['id']))}}">
                                                        @else
                                                            <input type="hidden" id="old_{{$input['id']}}" name="old_{{$input['id']}}" value="{{old($input['id'])}}">
                                                        @endif
                                                        @if($input['type']=='heading')
                                                            <h4 style="width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' ? $input['color'] : ''}};padding:5px;">{{$input['name']}}</h4>
                                                        @elseif($input['type']=='subheading')
                                                            <h5 style="width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' ? $input['color'] : ''}};padding:5px;">{{$input['name']}}</h5>
                                                        @else
                                                            <div style="display:block;width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' && $input['color'] != null ? $input['color'] : ''}};">
                                                                <div style="display: inline-block;width: 100%;">
                                                                        <span class="form-label" style="width:88%;float: left;display:block;">
                                                                        {{$input["name"]}}
                                                                        </span>
                                                                    @if($input['type']=='text')
                                                                        {{Form::text($input['id'],(isset($input['value'])?$input['value']:old($input['id'])),['class'=>'form-control form-control-sm','placeholder'=>'Insert text...','spellcheck'=>'true'])}}
                                                                    @endif

                                                                    @if($input['type']=='percentage')
                                                                        <input type="number" min="0" step="1" max="100" name="{{$input['id']}}" value="{{(isset($input['value']) ? $input['value'] : old($input['id']))}}" class="form-control form-control-sm" spellcheck="true" />
                                                                    @endif

                                                                    @if($input['type']=='integer')
                                                                        <input type="number" min="0" step="1" name="{{$input['id']}}" value="{{(isset($input['value']) ? $input['value'] : old($input['id']))}}" class="form-control form-control-sm" spellcheck="true" />
                                                                    @endif

                                                                    @if($input['type']=='amount')
                                                                        <input type="number" min="0" step="1" name="{{$input['id']}}" value="{{(isset($input['value']) ? $input['value'] : old($input['id']))}}" class="form-control form-control-sm" spellcheck="true" />
                                                                    @endif

                                                                    @if($input['type']=='date')
                                                                        <input name="{{$input['id']}}" type="date" min="1900-01-01" max="2030-12-30" value="{{(isset($input['value'])?$input['value']:old($input['id']))}}" class="form-control form-control-sm" placeholder="Insert date..." />
                                                                    @endif

                                                                    @if($input['type']=='textarea')
                                                                        <textarea spellcheck="true" rows="5" name="{{$input['id']}}" class="form-control form-control-sm text-area">{{(isset($input['value'])?$input['value']:old($input['id']))}}</textarea>
                                                                    @endif

                                                                    @if($input['type']=='boolean')
                                                                        <div role="radiogroup">
                                                                            <input type="radio" value="1" name="{{$input["id"]}}" id="{{$input["id"]}}-enabled" {{(isset($input["value"]) && $input["value"] == 1 ? 'checked' : '')}}>
                                                                            <label for="{{$input["id"]}}-enabled">Yes</label><!-- remove whitespace
                                                                    --><input type="radio" value="0" name="{{$input["id"]}}" id="{{$input["id"]}}-disabled" {{(isset($input["value"]) && $input["value"] == 1 ? '' : 'checked')}}><!-- remove whitespace
                                                                    --><label for="{{$input["id"]}}-disabled">No</label>

                                                                            <span class="selection-indicator"></span>
                                                                        </div>{{--
                                                                            {{Form::select($input['id'],[1=>'Yes',0=>'No'],(isset($input['value'])?$input['value']:old($input['id'])),['class'=>'form-control form-control-sm','placeholder'=>'Please select...'])}}--}}
                                                                    @endif

                                                                    @if($input['type']=='dropdown')

                                                                        <select multiple="multiple" name="{{$input["id"]}}[]" class="form-control form-control-sm chosen-select">
                                                                            @php
                                                                                foreach((array) $arr3 as $key3 => $value3){
                                                                                    echo '<option value="'.$key3.'" '.(in_array($key3,$arr23) ? 'selected' : '').'>'.$value3.'</option>';
                                                                                }
                                                                            @endphp
                                                                        </select>

                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                    <input type="submit" class="btn btn-primary float-right" value="Add">
                                                    {{Form::close()}}
                                                @endif
                                            </div>
                                        @endfor
                                        <div class="col-md-4 float-left" style="display:inherit;min-height:250px;text-align:center;vertical-align:middle;padding:7px;margin-bottom:7px;width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' && $input['color'] != null ? $input['color'] : ''}};">
                                            <a href="javascript:void(0)" class="btn btn-outline-primary m-auto addGroup" style="{{($value['max_group'] == $value['total_groups'] ? 'display:none;' : '')}}">Add {{$value['group_label']}}</a>
                                        </div>
                                    @else
                                        <div class="col-md-12">
                                            @if($value["show_name_in_tabs"] == 1)
                                                <h5>{{$value["name"]}}</h5>
                                            @endif
                                            <table class="table p-0 table-borderless w-100">
                                            @foreach($value["inputs"] as $input)
                                                <tr>
                                                @if($input['type']=='heading')
                                                        <td colspan="2"><h4 style="display:inline-block;width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' ? $input['color'] : ''}};padding:10px 0px 0px;">{{$input['name']}}</h4></td>
                                                @elseif($input['type']=='subheading')
                                                        <td colspan="2"><h5 style="display:inline-block;width:{{$input['level']}}%;margin-left: calc(100% - {{$input['level']}}%);background-color:{{$input['color'] != 'hsl(0,0%,0%)' ? $input['color'] : ''}};padding:10px 0px 0px;">{{$input['name']}}</h5></td>
                                                @else
                                                    <td style="padding:7px 0px 0px;margin-bottom:0px;width:40%;max-width:40%;min-width:40%;padding-left: calc(100% - {{$input['level']}}%) !important;background-color:{{$input['color'] != 'hsl(0,0%,0%)' && $input['color'] != null ? $input['color'] : ''}};">
                                                        <span class="form-label" style="font-size: 14px;opacity:1;">{{$input['name']}}</span></td>
                                                                <td>
                                                            @if(isset($input['value']))
                                                                @if($input['type'] == 'dropdown')
                                                                    @php

                                                                        $arr = (array)$input['dropdown_items'];
                                                                        $arr2 = (array)$input['dropdown_values'];

                                                                    @endphp
                                                                    @php
                                                                        foreach((array) $arr as $key => $value){
                                                                            if(in_array($key,$arr2)){
                                                                                echo $value.'<br />';
                                                                            }
                                                                        }
                                                                    @endphp
                                                                @elseif($input['type'] == 'boolean')
                                                                    {{($input['value'] == '1' ? 'Yes' : 'No')}}
                                                                @else
                                                                    {{$input['value']}}
                                                                @endif
                                                            @else
                                                                <small><i>No value captured.</i></small>
                                                            @endif
                                                                </td>
                                                @endif
                                                </tr>
                                            @endforeach
                                            </table>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @empty
                <div class="tab-pane fade p-3" id="personal_detail" role="tabpanel" aria-labelledby="personal_detail-tab">
                    <div class="row grid-items">
                        <div class="col-md-4">
                            <h5>Client Details</h5>
                            @if($client->crm_id == 2)
                            <div style="display:inline-block;padding:7px;margin-bottom:7px;width: 100%;">
                                <div>
                                    <span class="form-label">First Names</span>
                                </div>
                                <div class="form-text">
                                    {{$client->first_name}}
                                </div>
                            </div>
                            <div style="display:inline-block;padding:7px;margin-bottom:7px;width: 100%;">
                                <div>
                                    <span class="form-label">Last Name</span>
                                </div>
                                <div class="form-text">
                                    {{$client->last_name}}
                                </div>
                            </div>
                            <div style="display:inline-block;padding:7px;margin-bottom:7px;width: 100%;">
                                <div>
                                    <span class="form-label">Initials</span>
                                </div>
                                <div class="form-text">
                                    {{$client->initials}}
                                </div>
                            </div>
                            <div style="display:inline-block;padding:7px;margin-bottom:7px;width: 100%;">
                                <div>
                                    <span class="form-label">Known As</span>
                                </div>
                                <div class="form-text">
                                    {{$client->known_as}}
                                </div>
                            </div>
                            <div style="display:inline-block;padding:7px;margin-bottom:7px;width: 100%;">
                                <div>
                                    <span class="form-label">ID/Passport Number</span>
                                </div>
                                <div class="form-text">
                                    {{$client->id_number}}
                                </div>
                            </div>
                            <div style="display:inline-block;padding:7px;margin-bottom:7px;width: 100%;">
                                <div>
                                    <span class="form-label">Email</span>
                                </div>
                                <div class="form-text">
                                    {{$client->email}}
                                </div>
                            </div>
                            <div style="display:inline-block;padding:7px;margin-bottom:7px;width: 100%;">
                                <div>
                                    <span class="form-label">Cellphone Number</span>
                                </div>
                                <div class="form-text">
                                    {{$client->contact}}
                                </div>
                            </div>
                            @else
                            @endif
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
@section('extra-js')
    <script>
        $(function(){

            $('.nav-tabs').children('a').first().addClass('active').addClass('show');
            $('.tab-content').children('div').first().addClass('active').addClass('show');

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                let total = $('.active .select-this').length;
                let total_selected = $('.active .select-this:checked').length;

                if(total === total_selected){
                    $(".select-all").prop('checked',true);
                } else {
                    $(".select-all").prop('checked',false);
                }
            });

            $('.addGroup').on('click', function() {
                //var cur = $(this).attr('class').match(/\d+$/)[0];
                let cur = parseInt($(".max_group").val());
                let total = parseInt($(".total_groups").val());
                let next = cur+1;
                    $('.addGroup').css('display','none');

                $('.group-'+next).css('display','block');
                $(".max_group").val(next)

            });

            $('a[data-toggle="tab"]').on('show.bs.tab', function(e) {
                localStorage.setItem('activeTab', $(e.target).attr('href'));
                localStorage.setItem('activeUrl', $(location).attr('href'));
            });
            var activeTab = localStorage.getItem('activeTab');
            var activeUrl = localStorage.getItem('activeUrl');
            if(activeTab && activeUrl === $(location).attr('href')){
                $('#client-tabs a[href="' + activeTab + '"]').tab('show');
                //localStorage.removeItem('activeTab');
            }
        })
    </script>
@endsection
