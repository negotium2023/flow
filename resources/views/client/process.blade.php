<input type="hidden" id="active_step_id" value="{{$step['id']??null}}">
<div class="col-md-12" style="padding: 0px;margin: 0px;">
    <div class="alert alert-info" style="line-height: 2rem;font-size: 19px;">
        {{$step->process->name}}
        @if(isset($is_form) && $is_form == 1)
        <a href="javascript:void(0)" onclick="startNewForm({{$client->id}},{{$client->process_id}})" class="btn btn-white float-right" style="text-decoration: none;font-size: 14px;line-height: 24px;border-radius: 1.5rem !important;">Start New Form</a>
        @else
        <a href="javascript:void(0)" onclick="startNewApplication({{$client->id}},{{$client->process_id}})" class="btn btn-white float-right" style="text-decoration: none;font-size: 14px;line-height: 24px;border-radius: 1.5rem !important;">Start New Process</a>
        @endif
    </div>
</div>
@if(isset($is_form) && $is_form == 1)
    {{--<a href="javascript:void(0)" onclick="startNewForm({{$client->id}},{{$client->process_id}})" class="float-right d-inline-block btn btn-outline-primary" style="font-size: 14px;line-height: 24px;border-radius: 1.5rem !important;margin-bottom: -10px;position: relative;"><i class="fa fa-plus"></i>Start new form</a>--}}
    <br />
    <div class="process-slider" style="position: relative;">
        <button class="nav-prev" style="position: absolute;left: -15px;top:7px;z-index: 1;height:70px;width:35px;border-radius:0 50px 50px 0;background:#FFF;-webkit-box-shadow: 0px 0px 8px 2px rgba(0,0,0,0.28);
-moz-box-shadow: 0px 0px 8px 2px rgba(0,0,0,0.28);
box-shadow: 0px 0px 8px 2px rgba(0,0,0,0.28);border:0px;display: none;">
            <i class="fas fa-angle-left" style="padding-right: 10px;
    font-size: 40px;"></i>
        </button>
        <div class="js-pscroll scrolling-wrapper cata-sub-nav" style="position: relative" id="scrolling-wrapper">

            <div class="text-center blackboard-steps" id="blackboard-steps">
                <div class="progress-indicator" id="progress-indicator" style="min-height: 100px;">
                    @if(isset($steps) && isset($client))
                        @forelse($steps as $step)
                            <div class="card" id="step_{{$step['id']}}" style="{{(in_array($step['id'],$step_invisibil) ? 'display:none;' : 'display:table;')}}height: 100%;">
                                <div class="completed{{$step["stage"]}}" style="height: 100%;"> <span class="bubble">
                            <a href="{{route('clients.progress',$client)}}/{{$step['process_id']}}/{{$step['id']}}/{{$is_form}}" title="{{$step['name']}}"><i class="far fa-file-alt completed{{$step["stage"]}}" style="font-size: 2em;line-height: 1.6em;font-weight:regular;"></i></a>
                        </span>
                                    <div style="min-height:2em;display: table-cell;vertical-align: middle;text-align: center;min-width: 200px;">
                                        <a href="{{route('clients.progress',$client)}}/{{$step['process_id']}}/{{$step['id']}}/{{$is_form}}" title="{{$step['name']}}">{{$step['name']}}</a>
                                        <input type="hidden" class="step-cnt-{{$step['id']}}" value="{{(in_array($step['id'],$step_invisibil) ? 0 : 1)}}">
                                    </div>
                                    {{--<div style="position: absolute;bottom:0;left:41%;">
                                        <div class="blackboard-block" style="position: relative;"><a style="width: 100%;position: absolute;bottom: 0px;" href="{{route('clients.activityprogress',Array($client,$process_id,$step['id']))}}"><span style="font-size: 24px;" class="fa fa-angle-down"></span></a></div>
                                    </div>--}}</div>
                            </div>
                        @empty
                            <p>There are no steps assigned to this process.</p>
                        @endforelse
                    @else
                        @forelse(auth()->user()->office()->processes->first()->steps as $step)
                            <div class="col-lg blackboard-step-{{$step->id}}">
                                {{$step->name}}
                            </div>
                        @empty
                            <p>There are no steps assigned to this process.</p>
                        @endforelse
                    @endif
                </div>
            </div>
        </div><button class="nav-next" style="position: absolute;right: -15px;top:7px;z-index: 1;height:70px;width:35px;border-radius:50px 0 0 50px;background:#FFF;-webkit-box-shadow: 0px 0px 8px 2px rgba(0,0,0,0.28);
-moz-box-shadow: 0px 0px 8px 2px rgba(0,0,0,0.28);
box-shadow: 0px 0px 8px 2px rgba(0,0,0,0.28);border: 0px;">
            <i class="fas fa-angle-right" style="padding-left: 10px;
    font-size: 40px;"></i>
        </button>
    </div>
@else
    <div class="mt-3 mr-0 ml-0">
        <div class="text-center blackboard-steps">
            <ul class="progress-indicator">
                @if(isset($steps) && isset($client))
                    @forelse($steps as $step)
                        <li class="completed{{$step["stage"]}}"> <span class="bubble"></span><a href="{{route('clients.progress',$client)}}/{{$step['process_id']}}/{{$step['id']}}/0" title="{{$step['name']}}">{{$step['name']}}</a></li>
                    @empty
                        <p>There are no steps assigned to this process.</p>
                    @endforelse
                @else
                    @forelse(auth()->user()->office()->processes->first()->steps as $step)
                        <div class="col-lg blackboard-step-{{$step->id}}">
                            {{$step->name}}
                        </div>
                    @empty
                        <p>There are no steps assigned to this process.</p>
                    @endforelse
                @endif
            </ul>
        </div>
        {{--<div class="row text-center blackboard-steps">

            @if(isset($steps) && isset($client))
                @forelse($steps as $step)
                    <div id="chevron-{{$step['id']}}" class="col-lg" style="background-color: {{$step['progress_color']}}">
                        <div class="blackboard-before" style="border-top-color: {{$step['progress_color']}}; border-bottom-color: {{$step['progress_color']}}"></div>
                        <div class="blackboard-block"><a href="{{route('clients.progress',$client)}}/{{$step['process_id']}}/{{$step['id']}}/{{$is_form}}">{{$step['name']}}</a></div>
                        <div class="blackboard-after" style="border-left-color: {{$step['progress_color']}}"></div>
                    </div>
                    --}}{{--<div class="col-lg" style="background-color: {{$step['progress_color']}}">
                        <div class="blackboard-before" style="border-top-color: {{$step['progress_color']}}; border-bottom-color: {{$step['progress_color']}}"></div>
                        <div class="blackboard-block"><a href="{{route('clients.progress',$client)}}/{{$step['id']}}">{{$step['name']}}</a></div>
                        <div class="blackboard-after" style="border-left-color: {{$step['progress_color']}}"></div>
                    </div>--}}{{--
                    --}}{{--<div class="blackboard-client-chev-small col-lg">
                        <div class="blackboard-block"><a style="width: 100%;" class="btn" href="{{route('clients.activityprogress',Array($client,$step['process_id'],$step['id']))}}"><span style="font-size: 42px;" class="fa fa-angle-down"></span></a></div>
                    </div>--}}{{--
                @empty
                    --}}{{--<p>There are no steps assigned to this process.</p>--}}{{--
                @endforelse
            @else
                @forelse(auth()->user()->office()->processes->first()->steps as $step)
                    <div class="col-lg blackboard-step-{{$step->id}}">
                        {{$step->name}}
                    </div>
                @empty
                    <p>There are no steps assigned to this process.</p>
                @endforelse
            @endif
        </div>--}}
        {{--<div class="row text-center blackboard-steps-sm">

            @if(isset($steps) && isset($client))
                <select class="step-dropdown form-control form-control-sm chosen-select">
                    @forelse($steps as $step)
                        <option value="{{$step['id']}}" data-path="{{route('clients.progress',$client)}}/{{$step['process_id']}}/{{$step['id']}}" {{(isset($active) && $active["id"] == $step['id'] ? 'selected' : '')}}>{{$step['name']}}</option>
                    @empty
                        --}}{{--<option value="">There are no steps assigned to this process.</option>--}}{{--
                    @endforelse
                </select>
            @else
                @forelse(auth()->user()->office()->processes->first()->steps as $step)
                    <div class="col-lg blackboard-step-{{$step->id}}">
                        {{$step->name}}
                    </div>
                @empty
                    <p>There are no steps assigned to this process.</p>
                @endforelse
            @endif
        </div>--}}
    </div>
    {{--<div class="blackboard-client-chev-big">
        <div class="row text-center blackboard-steps">
            @if(isset($steps) && isset($client))
                @forelse($steps as $step)
                    <div class="col-lg">
                        <div class="blackboard-block"><a style="width: 100%;" class="btn" href="{{route('clients.activityprogress',Array($client,$step['process_id'],$step['id']))}}"><span style="font-size: 42px;" class="fa fa-angle-down"></span></a></div>
                    </div>
                @empty
                    <p>There are no steps assigned to this process.</p>
                @endforelse
            @else
                --}}{{--@forelse(auth()->user()->office()->processes->first()->steps as $step)
                    <div class="col-lg blackboard-step-{{$step->id}}">
                        <div class="blackboard-block"><a style="width: 100%;" class="btn" onclick="showStep({{$step['id']}}})" href="{{route('clients.activityprogress',Array($client,$step['process_id'],$step['id']))}}"><span style="font-size: 42px;" class="fa fa-angle-down"></span></a></div>
                    </div>
                @empty
                    <p>There are no steps assigned to this process.</p>
                @endforelse--}}{{--
            @endif
        </div>
    </div>--}}
@endif