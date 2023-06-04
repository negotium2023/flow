@extends('flow.default')

@section('title') Clients @endsection

@section('header')
    <div class="container-fluid container-title float-left clear" style="margin-top: 1%;margin-bottom: 10px;height:auto !important;">
        <table class="table table-borderless w-100">
            <tr>
                <td style="padding-right:40px !important;">
                    <h3 style="padding-bottom: 5px;">@yield('title')</h3>
                </td>
                <td>
                    <form class="form-inline" id="clientform">

                        <input type="hidden" value="{{isset($_GET['p']) ? $_GET['p'] : ''}}">
                        <div class="col-md-3 mt-0 mb-0 pt-0 pb-0">
                            <small class="text-muted">Referrer</small>
                        <select name="r" class="form-control form-control-sm w-100 chosen-select">
                            <option value="all">Referrer</option>
                            @foreach($referrer_dd as $r)
                                @if($r->name != '')
                                    <option value="{{$r->name}}" {{isset($_GET['r']) && $_GET['r'] == $r->name ? 'selected' : ''}}>{{$r->name}}</option>
                                @endif
                            @endforeach
                        </select>
                        </div>
                        <div class="col-md-3 mt-0 mb-0 pt-0 pb-0">
                            <small class="text-muted">Relationship Lead</small>
                        <select name="rl" class="form-control form-control-sm w-100 chosen-select">
                            <option value="all">Relationship Lead</option>
                            @foreach($relationship_lead as $rl)
                                @if($rl->name != '')
                                    <option value="{{$rl->name}}" {{isset($_GET['rl']) && $_GET['rl'] == $rl->name ? 'selected' : ''}}>{{$rl->name}}</option>
                                @endif
                            @endforeach
                        </select>
                        </div>
                        <div class="col-md-3 mt-0 mb-0 pt-0 pb-0">
                            <small class="text-muted">Onboarding Lead</small>
                        <select name="ol" class="form-control form-control-sm w-100 chosen-select">
                            <option value="all">Onboarding Lead</option>
                            @foreach($onboarding_lead as $ol)
                                @if($ol->name != '')
                                    <option value="{{$ol->name}}" {{isset($_GET['ol']) && $_GET['ol'] == $ol->name ? 'selected' : ''}}>{{$ol->name}}</option>
                                @endif
                            @endforeach
                        </select>
                        </div>
                        <div class="col-md-3 mt-0 mb-0 pt-0 pb-0">
                            <small class="text-muted">Onboarding Member</small>
                        <select name="om" class="form-control form-control-sm w-100 chosen-select">
                            <option value="all">Onboarding Member</option>
                            @foreach($onboarding_member as $om)
                                @if($om->name != '')
                                    <option value="{{$om->name}}" {{isset($_GET['om']) && $_GET['om'] == $om->name ? 'selected' : ''}}>{{$om->name}}</option>
                                @endif
                            @endforeach
                        </select>
                        </div>
                        <div class="col-md-3 mt-0 mb-0 pt-0 pb-0">
                            <small class="text-muted">From</small>
                        {{Form::date('f',old('f'),['class'=>'form-control form-control-sm w-100 mt-0 mb-0'])}}
                        </div>
                        <div class="col-md-3 mt-0 mb-0 pt-0 pb-0">
                            <small class="text-muted">To</small>
                        {{Form::date('t',old('t'),['class'=>'form-control form-control-sm w-100 mt-0 mb-0'])}}
                        </div>
                        <div class="col-md-3 mt-0 mb-0 pt-0 pb-0">
                            <small class="text-muted">Search</small>
                        <div class="input-group input-group-sm mt-0 mb-0">
                            <div class="input-group-prepend mt-0">
                                <div class="input-group-text">
                                    <i class="fa fa-search"></i>
                                </div>
                            </div>
                            {{Form::text('q',old('query'),['class'=>'form-control form-control-sm mt-0 mb-0','placeholder'=>'Search...'])}}
                        </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <small class="text-muted d-block w-100">&nbsp;</small>
                        <button type="submit" class="btn btn-sm btn-secondary" style="margin-right: 2%;width:47%"><i class="fa fa-search"></i> Search</button>
                        <a href="{{route('clients.index')}}" class="btn btn-sm btn-info" style="width:47%"><i class="fa fa-eraser"></i> Clear</a>
                        </div>
                    </form>
                </td>
            </tr>
        </table>
    </div>
@endsection

@section('content')

        <div class="content-container page-content">
            <div class="row col-md-12 h-100 pr-0">
                <div class="container-fluid index-container-content h-100">
                    <div class="table-responsive d-inline-block pl-0" style="height: 17%;width:100%;">
                        <table class="table table-borderless table-sm table-fixed billboard-table">
                            <thead>
                            <tr>
                                <th nowrap style="box-shadow: none;"><h4>Practice Billboard</h4></th>
                                <th class="last" nowrap style="box-shadow: none;vertical-align: top;">
                                    <a href="javascript:void(0)" onclick="composeBillboardMessage()" class="btn btn-sm btn-primary submit-btn" style="line-height: 1rem !important;">Add a message</a>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($messages as $message)
                                <tr class="message-{{$message["id"]}}">
                                    <td class="billboard" colspan="100%"><a href="javascript:void(0)" style="display: block;" onclick="showBillboardMessage({{$message["id"]}})"></a>
                                        <span class="pull-right clickable close-icon" onclick="completeBillboardMessage({{$message["id"]}})" data-effect="fadeOut"><i class="fas fa-trash" style="color:#f06072"></i></span>
                                        <div class="card-block">
                                            <blockquote class="card-blockquote">
                                                <div class="blockquote-body" onclick="showBillboardMessage({{$message["id"]}})">@if($message["heading"] != null) <strong>{{$message["heading"]}}</strong><br />{{$message["message"]}} @else  {{$message["message"]}} @endif</div>
                                            </blockquote>
                                        </div>
                                    </td>
                                </tr >
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center"><small class="alert alert-info w-100 d-block text-muted">There are no messages to display.</small></td>
                                </tr>
                            @endforelse
                            </tbody>

                        </table>
                    </div>
                    @yield('header')
                    <div class="table-responsive w-100 float-left client-index" style="height: calc(54% - 5px);position:relative;margin-bottom:5px;border-bottom:1px solid #eee;clear:both;">
                            <table class="table table-bordered table-hover table-sm table-fixed" style="max-height: calc(100% - 30px);">
                                <thead>
                                <tr>
                                    <th style="vertical-align: middle;" nowrap>@sortablelink('company', 'Name')</th>
                                    <th style="vertical-align: middle;">@sortablelink('referrer','Referrer')</th>
                                    <th style="vertical-align: middle;word-break:break-word;">@sortablelink('director','Relationship Lead')</th>
                                    <th style="vertical-align: middle;">@sortablelink('onboardingl','Onboarding Lead')</th>
                                    <th style="vertical-align: middle;">@sortablelink('onboardingm','Onboarding Member In Charge')</th>
                                    <th style="vertical-align: middle;" nowrap>@sortablelink('email', 'Email')</th>
                                    <th style="vertical-align: middle;" nowrap>@sortablelink('cell', 'Cellphone Number')</th>
                                    <th style="vertical-align: middle;" nowrap>@sortablelink('cell', 'Converted')</th>
                                    <th style="vertical-align: middle;" nowrap>@sortablelink('step', 'Step')</th>
                                    <th style="vertical-align: middle;" nowrap>@sortablelink('user', 'User')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($clients['results'] as $client)
                                    <tr>
                                        <td><a href="{{route('clients.overview',[$client["id"],$client["process_id"],$client["step_id"]])}}">{{(isset($client["company"] ) && $client["company"] != ' ' ? $client["company"]  : 'Not Captured')}}</a></td>
                                        <td>{{(isset($client["referrer"]) ? $client["referrer"] : '')}}</td>
                                        <td>{{(isset($client["director"]) ? $client["director"] : '')}}</td>
                                        <td>{{(isset($client["onboardingl"]) ? $client["onboardingl"] : '')}}</td>
                                        <td>{{(isset($client["onboardingm"]) ? $client["onboardingm"] : '')}}</td>
                                        <td>{{!is_null($client["email"]) ? $client["email"] : ''}}</td>
                                        <td>{{!is_null($client["contact"]) ? $client["contact"] : ''}}</td>
                                        <td>{{!is_null($client["completed_at"]) ? $client["completed_at"] : ''}}</td>
                                        <td>{{!is_null($client["step"]) ? $client["step"] : ''}}</td>
                                        <td><a href="{{route('profile',$client["introducer"])}}"><img src="{{route('avatar',['q'=>$client["avatar"] ])}}" class="blackboard-avatar blackboard-avatar-inline blackboard-avatar-navbar-img" alt="Avatar"/></a></td>
                                    </tr >
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center"><small class="text-muted">No clients match those criteria.</small></td>
                                    </tr>
                                @endforelse
                                </tbody>

                            </table>

                    </div>
                    <div style="position: relative;float:left;bottom: 0px;left: 0px;height:40px;width: 100%;text-align: right;">@include('client.pagination', ['paginator' => $clients['results'], 'link_limit' => 5])</div>
                    {{-- <div style="position: relative;float:left;bottom: 0px;left: 0px;height:40px;width: 30%;text-align: right;">Displaying <strong>{{$clients->firstItem()}}</strong> - <strong>{{$clients->lastItem()}}</strong> of <strong>{{$clients->total()}}</strong> Clients</div> --}}
                 </div>

                  @include('client.modals.index')
            </div>
         </div>

@endsection
