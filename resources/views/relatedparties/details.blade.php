@extends('relatedparties.show')

@section('tab-content2')
    <div class="col-lg-9">
        <div class="card">
            <div class="card-body">
                <ul>
                    <dt>
                        First Names
                    </dt>
                    <dd>
                        @if($related_party->first_name)
                            {{$related_party->first_name}}
                        @else
                            <small><i>No first name captured</i></small>
                        @endif
                    </dd>
                    <dt>
                        Surnames
                    </dt>
                    <dd>
                        @if($related_party->last_name)
                            {{$related_party->last_name}}
                        @else
                            <small><i>No surname captured</i></small>
                        @endif
                    </dd>
                    <dt>
                        Initials
                    </dt>
                    <dd>
                        @if($related_party->initials)
                            {{$related_party->initials}}
                        @else
                            <small><i>No initials captured</i></small>
                        @endif
                    </dd>
                    <dt>
                        ID/Passport number
                    </dt>
                    <dd>
                        @if($related_party->id_number)
                            {{$related_party->id_number}}
                        @else
                            <small><i>No ID/Passport number captured</i></small>
                        @endif
                    </dd>
                    <dt>
                        Email
                    </dt>
                    <dd>
                        @if($related_party->email != null)
                            <a href="mailto:{{$related_party->email}}">{{$related_party->email}}</a>
                        @else
                            <small><i>No email captured</i></small>
                        @endif
                    </dd>
                    <dt>
                        Contact number
                    </dt>
                    <dd>
                        @if($related_party->contact)
                            {{$related_party->contact}}
                        @else
                            <small><i>No contact number captured</i></small>
                        @endif
                    </dd>
                    <dt>
                        Office
                    </dt>
                    <dd>
                        @if($related_party->office_id != null)
                            {{$related_party->office->area->region->division->name}} / {{$related_party->office->area->region->name}} / {{$related_party->office->area->name}} / {{$related_party->office->name}}
                            @else
                            <small><i>No office captured</i></small>
                        @endif
                    </dd>
                    <dt>
                        Created
                    </dt>
                    <dd>
                        <p>
                            {{$related_party->created_at->diffForHumans()}} <span class="text-muted"><i> <i class="fa fa-clock-o"></i> {{$related_party->created_at}}</i></span>
                        </p>
                    </dd>
                    @if(isset($client->not_progressing_date) && $client->not_progressing_date != null)
                        <dt>
                            Moved to Not Progressing
                        </dt>
                        <dd>
                            <p>
                                {{\Illuminate\Support\Carbon::parse($client->not_progressing_date)->diffForHumans()}} <span class="text-muted"><i> <i class="fa fa-clock-o"></i> {{$client->not_progressing_date}}</i></span>
                            </p>
                        </dd>
                    @endif
                </ul>

                <div id="actual-times" class="mt-3" style="width: 100%"></div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Comments
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    @forelse($related_party->comments as $comment)
                        <tr>
                            <td>
                                <a href="{{route('profile',$comment->user_id)}}">
                                    <img src="{{route('avatar',['q'=>$comment->user->avatar])}}" class="blackboard-avatar blackboard-avatar-inline" alt="{{$comment->user->name()}}"/>{{$comment->user->name()}}
                                </a>
                                : {{$comment->comment}}
                                <small class="float-right text-muted"><i class="fa fa-calendar"></i> {{substr($comment->created_at,0,10)}}&nbsp;&nbsp;<i class="fa fa-clock-o"></i> {{substr($comment->created_at,11,19)}}</small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center">
                                <small><i>No comments added yet.</i></small>
                            </td>
                        </tr>
                    @endforelse
                    <tr>
                        <td>
                            {{Form::open(['url' => route('relatedparty.storecomment', ['client' => $client,'related_party' => $related_party]), 'method' => 'post'])}}
                            <div class="input-group">
                                {{Form::text('comment',old('comment'),['class'=>'form-control form-control-sm','placeholder'=>'Type a comment'])}}
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-info btn-sm"><i class="fa fa-paper-plane"></i></button>
                                </div>
                            </div>
                            {{Form::close()}}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card">
            <div class="card-header">
                Case Actions
            </div>
            <div class="card-body">
                @if(auth()->user()->can('maintain_client'))
                    @if(auth()->user()->is('qa') && $client->is_qa)

                    @else

                    @endif

                    {{Form::open(['url' => route('relatedparty.progressing',['client'=>$client,'related_party'=>$related_party]), 'method' => 'post'])}}
                    @if($client->user_id == auth()->id() || auth()->user()->can('admin'))
                        <a href="javascript:void(0)" onclick="editRelatedParty({{$client->id}},{{$related_party->id}},{{$related_party->related_part_parent_id}})" class="btn btn-sm btn-block btn-outline-primary"><i class="fa fa-pencil"></i> Edit</a>
                        {{--<a href="{{route('relatedparty.edit',['client'=>$client,'related_party'=>$related_party])}}" class="btn btn-sm btn-block btn-outline-primary"><i class="fa fa-pencil"></i> Edit</a>--}}

                        <br>
                        {{--@if($client->trashed())
                            <a href="{{route('relatedparty.restore',['client'=>$client,'related_party'=>$related_party])}}" class="btn btn-sm btn-block btn-outline-success"><i class="fa fa-undo"></i> Restore</a>
                        @else
                            <a href="{{route('relatedparty.delete',['client'=>$client,'related_party'=>$related_party["id"]])}}" id="deleterelatedparty" class="deleterelatedparty btn btn-sm btn-block btn-outline-danger"><i class="fa fa-trash"></i> Delete</a>
                        @endif--}}


                    @else
                        <a href="javascript:void(0)" onclick="editRelatedParty({{$client->id}},{{$related_party->id}},{{$related_party->related_part_parent_id}})" class="btn btn-sm btn-block btn-outline-primary"><i class="fa fa-pencil"></i> Edit</a>


                    @endif
                    {{Form::close()}}



                    @if($related_party->needs_approval)
                        <hr>

                        {{Form::open(['url' => route('relatedparty.approval',['client'=>$client,'related_party'=>$related_party]), 'method' => 'post'])}}
                        {{Form::hidden('status',true)}}
                        <button type="submit" class="btn btn-block btn-sm btn-outline-success"><i class="fa fa-check"></i> Approve lead</button>
                        {{Form::close()}}

                        <br>

                        {{Form::open(['url' => route('relatedparty.approval',['client'=>$client,'related_party'=>$related_party]), 'method' => 'post'])}}
                        {{Form::hidden('status',false)}}
                        <button type="submit" class="btn btn-block btn-sm btn-outline-danger"><i class="fa fa-times"></i> Decline lead</button>
                        {{Form::close()}}
                    @endif
                    <br />
                    {{Form::open(['url' => route('messages.relatedparty',['client_id'=>$client,'related_party_id' => $related_party]), 'method' => 'get'])}}
                    {{Form::hidden('status',true)}}
                    <button type="submit" class="btn btn-block btn-sm btn-success"><i class="fa fa-comment"></i> Send Message</button>
                    {{Form::close()}}

                @else
                    <button type="button" class="btn btn-block btn-sm btn-outline-primary disabled" disabled title="You do not have permission to do that"><i class="fa fa-star-o"></i> Follow</button>

                    <br>

                    <button type="button" class="btn btn-block btn-sm btn-outline-primary disabled" disabled title="You do not have permission to do that"><i class="fa fa-pencil"></i> Edit</button>
                @endif
                {{--@foreach($process_progress as $key => $step)
                    @foreach($step['activities'] as $activity)
                        {{$activity["name"]}}<br />
                    @endforeach
                @endforeach--}}

            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                Assigned users
            </div>
            <div class="card-body">
                <dt>
                    Captured by
                </dt>

                <dd>
                    @if($related_party->introducer_id != null)
                        <a href="{{route('profile',$related_party->introducer_id)}}"><img src="{{route('avatar',['q'=>$related_party->introducer->avatar])}}" class="blackboard-avatar blackboard-avatar-inline"/> {{$related_party->introducer->name()}}</a>
                    @endif
                </dd>
                <dt>
                    Completed by
                </dt>
                @if($client->completed_by > 0)
                    <dd>
                        <a href="{{route('profile',$client->completed_by)}}"><img src="{{route('avatar',['q'=>$client->completedby->avatar])}}" class="blackboard-avatar blackboard-avatar-inline"/> {{$client->completedby->name()}}</a>
                    </dd>
                @else
                    <dd>
                        <small><i>Not completed yet</i></small>
                    </dd>
                @endif
                {{--<dt>
                    Following users
                </dt>
                @forelse($related_party->users as $user)
                    <dd>
                        <a href="{{route('profile',$user->id)}}"><img src="{{route('avatar',['q'=>$user->avatar])}}" class="blackboard-avatar blackboard-avatar-inline"/> {{$user->name()}}</a>
                    </dd>
                @empty
                    <dd>
                        <small><i>No users tagged yet</i></small>
                    </dd>
                @endforelse--}}
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $(function () {
            $("#w_i_q_a").click(function () {
                axios.post('/related/work-item-qa/{{$client->id}}', {
                    client: '{{$client->id}}',
                })
                    .then(function (response) {
                        if(response.status == 200){
                            $('#w_i_q_a').prop('disabled', true);
                        }
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            })
        })
    </script>
@endsection
