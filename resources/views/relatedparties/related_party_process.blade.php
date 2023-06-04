<div class="col-sm-12 mt-3 mr-0 ml-0">
    <div class="text-center blackboard-steps">
        <ul class="progress-indicator">
            @if(isset($related_party_steps) && isset($client))
                @forelse($related_party_steps as $step)
                    <li class="completed{{$step["stage2"]}}"> <span class="bubble"></span><a href="{{route('relatedparty.stepprogress',[$client,$step['process_id'],$step['id'],(isset($related_party_id) ? $related_party_id : 0)])}}" title="{{$step['name']}}">{{$step['name']}}</a></li>
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
    <div class="row text-center blackboard-steps-sm">

        @if(isset($related_party_steps) && isset($client))
            <select class="step-dropdown form-control form-control-sm chosen-select">
                @forelse($related_party_steps as $step)
                    <option value="{{$step['id']}}" data-path="{{route('relatedparty.stepprogress',[$client,$step['process_id'],$step['id'],(isset($related_party_id) ? $related_party_id : 0)])}}" {{(isset($active) && $active["id"] == $step['id'] ? 'selected' : '')}}>{{$step['name']}}</option>
                @empty
                    {{--<option value="">There are no steps assigned to this process.</option>--}}
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
    </div>
</div>
<div class="blackboard-client-chev-big">
    <div class="row text-center blackboard-steps">
        @if(isset($steps) && isset($client))
            @forelse($steps as $step)
                {{--<div class="col-lg">
                    <div class="blackboard-block"><a style="width: 100%;" class="btn" href="{{route('clients.activityprogress',Array($client,$step['id']))}}"><span style="font-size: 42px;" class="fa fa-angle-down"></span></a></div>
                </div>--}}
            @empty
                <p>There are no steps assigned to this process.</p>
            @endforelse
        @else
            @forelse(auth()->user()->office()->processes->first()->steps as $step)
                <div class="col-lg blackboard-step-{{$step->id}}">
                    <div class="blackboard-block"><a style="width: 100%;" class="btn" onclick="showStep({{$step['id']}}})" href="{{route('clients.activityprogress',Array($client,$step['id']))}}"><span style="font-size: 42px;" class="fa fa-angle-down"></span></a></div>
                </div>
            @empty
                <p>There are no steps assigned to this process.</p>
            @endforelse
        @endif
    </div>
</div>