<?php $class = $thread->userUnreadMessagesCount(Auth::id()) > 0 ? ' background:rgba(153,204,153,0.4)' : ''; ?>
<a href="{{ route('messages.show', $thread->id) }}" class="message-thread"><div class="col-sm-12">
        <div >
            <table class="table table-borderless">
                <tr {{$class}}>
                    <td class="last" style="{{$class}}"><img src="{{route('avatar',['q'=>$thread->latestMessage->user->avatar])}}" alt="{{ $thread->latestMessage->user->first_name }}" class="direct-chat-img mr-3" height="40" style="margin-top:8px;"></td>
                    <td style="border-bottom:1px solid #efefef;{{$class}}" >
                        <strong style="color:#000000">{{ $thread->latestMessage->user->first_name }} {{ $thread->latestMessage->user->last_name }}</strong><br />
                        <?php echo $thread->latestMessage->body; ?>
                    </td>
                    <td style="border-bottom:1px solid #efefef;{{$class}}" class="last">
                        <span class="badge badge-pill badge-success">{{ ($thread->userUnreadMessagesCount(Auth::id()) > 0 ? $thread->userUnreadMessagesCount(Auth::id()) : '') }}</span>
                    </td>
                </tr>
            </table>
        </div>
        {{--<p>
            <small><strong>Creator:</strong> {{ $thread->creator()->first_name }} {{ $thread->creator()->last_name }}</small>
        </p>
        <p>
            <small><strong>Participants:</strong> {{ $thread->participantsString(Auth::id(),['first_name','last_name']) }}</small>
        </p>--}}
     </div></a>