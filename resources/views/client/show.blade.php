@extends('flow.default')

@section('title') {{($client->company == '' || $client->company == 'N/A' || $client->company == 'n/a' ? $client->first_name.' '.$client->last_name : $client->company )}} @endsection

@section('content')
    <div class="client-sidemenu elevation-3">
        <a href="javascript:void(0)" onclick="hideClientInfo()" id="client-sidebar-btn" style="position: absolute;right: 0px;color: #000;z-index: 999;background: #FFF;top: -1px;padding:6px;">
            <span class="fa fa-angle-left"></span>
        </a>
        <div class="client-info" style="padding: 1.25rem;
    overflow-x: hidden;
    height: 100%;">
            <img src="{{route('clients.avatar',$client)}}" class="client-avatar"/>
            <input type="hidden" id="clientSidebarId" value="{{$client["id"]}}">
            <dd class="seperator">
                &nbsp;
            </dd>
                @if($client->crm_id == 4)
                    <dt>
                        Company Name
                    </dt>
                    <dd>
                        {{($client->company == '' ? 'No company captured.' : $client->company)}}
                    </dd>
                    <dt>
                        Contact Full Names
                    </dt>
                    <dd>
                        {{($client->first_name == '' ? 'No full name captured.' : $client->first_name)}}
                    </dd>
                    <dt>
                        Contact Last Name
                    </dt>
                    <dd>
                        {{($client->last_name == '' ? 'No surname captured.' : $client->last_name)}}
                    </dd>
                    
                    <dd class="seperator">
                        &nbsp;
                    </dd>
                    <dt>
                        Contact Email Address
                    </dt>
                    <dd>
                        {{($client->email == '' ? 'No email captured.' : $client->email)}}
                    </dd>
                    <dt>
                        Contact Number
                    </dt>
                    <dd>
                        {{($client->contact == '' ? 'No contact number captured.' : $client->contact )}}
                    </dd>
                @endif()
                @if($client->crm_id == 2)
                    <dt>
                        First Names
                    </dt>
                    <dd>
                        {{($client->first_name == '' ? 'No full name captured.' : $client->first_name)}}
                    </dd>
                    <dt>
                        Last Name
                    </dt>
                    <dd>
                        {{($client->last_name == '' ? 'No surname captured.' : $client->last_name)}}
                    </dd>
                    {{-- <dt>
                        ID Number
                    </dt>
                    <dd>
                        {{($client->id_number == '' ? 'No id number captured.' : $client->id_number)}}
                    </dd> --}}
                    
                    <dd class="seperator">
                        &nbsp;
                    </dd>
                    <dt>
                        Email Address
                    </dt>
                    <dd>
                        {{($client->email == '' ? 'No email captured.' : $client->email)}}
                    </dd>
                    <dt>
                        Contact Number
                    </dt>
                    <dd>
                        {{($client->contact == '' ? 'No contact number captured.' : $client->contact )}}
                    </dd>
                @endif()
            <dd class="seperator">
                &nbsp;
            </dd>
            <dd>
                <a href="javascript:void(0)" onclick="composeMail({{$client['id']}})" class="btn btn-block btn-sm btn-success">Send Mail</a>
            </dd>
            {{--<dd>
                <a href="javascript:void(0)" onclick="composeWhatsapp({{$client['id']}})" class="btn btn-block btn-sm btn-success">Send Whatsapp</a>
            </dd>--}}
            <dd>
                <a href="javascript:void(0)" onclick="composeMessage({{$client['id']}},{{$client["process_id"]}},{{$client["step_id"]}})" class="btn btn-block btn-sm btn-success">Send Message</a>
            </dd>
            <dd>
                <a href="javascript:void(0)" onclick="toggelClientBasket()" class="btn btn-sm btn-block btn-primary">Add to client basket</a>
            </dd>
            {{-- <dd>
                <a href="javascript:void(0)" onclick="addressKYC({{$client->id}})" class="btn btn-sm btn-block btn-outline-danger">Request KYC via ID Pal</a>
            </dd>
            <dd>
                {{--<a href="javascript:void(0)" onclick="getApplicationDoc({{$client->id}},{{$process_id}})" class="btn btn-sm btn-block btn-outline-danger">Submit for Signatures</a>
                <a href="javascript:void(0)" class="btn btn-sm btn-block btn-outline-danger" onclick="submitForSignatures({{$client['id']}},{{$client["process_id"]}},{{$client["step_id"]}})" title="Submit an application for signatures">Submit for Signatures</a>
            </dd> --}}
            <dd>
                @if($client->trashed())
                    <a href="{{route('clients.delete',$client)}}" disabled class="btn btn-block btn-sm btn-danger deleteclient">Restore</a>
                @else
                    <a href="{{route('clients.delete',$client)}}" class="btn btn-block btn-sm btn-danger deleteclient">Delete</a>
                @endif
            </dd>
            <dd>
                @if ($client->is_progressing == 1)
                    <a href="javascript:void(0)" data-client="{{$client['id']}}" class="btn btn-sm isClientProgressing btn-block btn-outline-warning">Is Progressing</a>
                @else
                    <a href="javascript:void(0)" data-client="{{$client['id']}}" class="btn btn-sm isClientProgressing btn-block btn-outline-danger">Not Progressing</a>
                @endif
            </dd>
        </div>
    </div>
    <div class="client-basket elevation-3">
        <div class="client-basket-header">
            <span class="client-basket-title" style="display: inline-block;width: 80%;">Client Basket</span>
            <input type="checkbox" class="float-right pull-right client-basket-select-all-all" style="margin-right: 30px; margin-top:40px;" onclick="clientBasketGlobalSelectAll()" data-toggle="tooltip" data-html="true" title="Add all client details to client basket." />
        </div>
        <div class="client-basket-content">
            <form id="client-basket-form" onsubmit="return false">
                <div class="accordion"  id="accordion">
        @foreach($client_details as $data => $tab)
            @foreach($tab as $name => $sections)
                @foreach($sections["data"] as $key => $value)
                            <div class="card" data-name="{{strtolower(str_replace(' ','',$value["name"]))}}">
                                <div class="card-header" id="{{strtolower(str_replace(' ','',$value["name"]))}}-panel">
                                    <h2 class="mb-0">
                                        <button type="button" class="btn btn-link " data-toggle="collapse" data-target="#collapse-{{strtolower(str_replace(' ','',$value["name"]))}}" data-parent="#accordion" style="width: 14rem;">
                                            <span class="{{($data == 1000 ? 'extra' : '')}}">{{$value["name"]}}</span></button>
                                        <input type="checkbox" id="{{strtolower(str_replace(' ','',$value["name"]))}}"  class="float-right pull-right client-basket-select-all" style="margin-right:10px;position: relative;margin-top:14px;" data-toggle="tooltip" data-html="true" onclick="clientBasketSelectAll('{{strtolower(str_replace(' ','',$value["name"]))}}')" title="Add all {{$value["name"]}} activities to client basket." />
                                    </h2>

                                </div>
                                <div id="collapse-{{strtolower(str_replace(' ','',$value["name"]))}}" class="collapse" aria-labelledby="{{str_replace(' ','',$value["name"])}}-panel" data-parent="#accordionExample">
                                    <div class="card-body">
                                        @foreach($value["inputs"] as $input)
                                                <p><span style="display: inline-block;width: 13.45rem;">{{$input["name"]}}</span><input type="checkbox" class="select-this-{{strtolower(str_replace(' ','',$value["name"]))}}" style="position: relative;right:-10px;" onclick="clientBasketSelect('{{strtolower(str_replace(' ','',$value["name"]))}}')" name="add_to_basket[]" value="{{$input['id']}}" id="{{$input['id']}}" {{(in_array($input['id'],$in_details_basket) ? 'checked' : '')}}></p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                @endforeach
            @endforeach
        @endforeach
        </div>
            </form>
        </div>
        <div class="client-basket-footer">
            <div class="client-basket-footer-text">
                <a href="javascript:void(0)" onclick="toggelClientBasket()" class="btn btn-outline-primary">Cancel</a>
                <a href="javascript:void(0)" data-client="{{$client["id"]}}" id="client-basket-add" class="btn btn-success float-right">Add</a>
            </div>
        </div>
    </div>
    <div class="nav-client client" style="margin-left: 18rem;">
        <ul class="nav nav-tabs nav-fill">
            <li class="nav-item">
                <a class="nav-link {{active('clients.overview','active')}}" href="{{route('clients.overview',[$client,$process_id,$step["id"]])}}">Profile Overview</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{active('clients.details','active')}}" href="{{route('clients.details',[$client,$process_id,$step["id"],$is_form])}}">Client Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{active('clients.processes','active')}}" href="{{route('clients.processes',[$client,$process_id,$step["id"],0])}}">Processes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{active('clients.documents','active')}}" href="{{route('clients.documents',[$client,$process_id,$step["id"],$is_form])}}">Document Vault</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{active('client.basket','active')}}" href="{{route('client.basket',[$client,$process_id,$step["id"],$is_form])}}">Client Basket</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{active('clients.forms','active')}}" href="{{route('clients.forms',[$client,$process_id,$step["id"]])}}">Forms</a>
            </li>
            {{-- @if($client->crm_id == 4)
            <li class="nav-item">
                <a class="nav-link {{active('clients.members','active')}}" href="{{route('clients.members',[$client,$process_id,$step["id"]])}}">Members</a>
            </li>
            @endif --}}
            <li class="nav-item">
                <a class="nav-link {{active('clients.actions','active')}}" href="{{route('clients.actions',[$client,$process_id,$step["id"],0])}}">Actions</a>
            </li>
        </ul>
    </div>
    <div class="content-container client-content" style="display: none">
        <div class="col-md-12 p-0 h-100">
            @if($client->crm_id == 5)
                <div class="alert alert-info">Go back to <a href="{{route('clients.overview',[$client->parent->id,$client->parent->process_id,$client->parent->step_id])}}">{{$client->parent->name}}</a>.</div>
            @endif
        @yield('tab-content')
        </div>

    </div>
    @include('client.modals.index')
@endsection
@section('extra-js')
<script>
    
</script>
@endsection