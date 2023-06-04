<?php $class = $thread->isUnread(Auth::id()) ? 'alert-success' : 'btn-default'; ?>
<a href="{{ route('messages.show', $thread->id) }}" class="message-thread"><div class="col-sm-12">
<div class="alert {{ $class }}">
<table>
    <tr>
        <td><img src="{{route('avatar',['q'=>$thread->latestMessage->user->avatar])}}" alt="{{ $thread->latestMessage->user->first_name }}" class="direct-chat-img mr-3" height="40"></td>
        <td><h6 class="media-heading" style="display: block;text-decoration:none;color:#000000">
                {{--{{ $thread->userUnreadMessagesCount(Auth::id()) }}&nbsp;--}}

            </h6>
            <strong style="color:#000000">From: </strong>{{ $thread->latestMessage->user->first_name }} {{ $thread->latestMessage->user->last_name }}<br />
            <strong style="color:#000000">Subject: </strong>{{ $thread->subject }}<br />
            <strong style="color:#000000">Message: </strong><?php echo html_entity_decode($thread->latestMessage->body); ?>
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