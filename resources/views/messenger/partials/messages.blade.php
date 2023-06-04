@if($message->user->id == auth()->id())
    <div class="direct-chat-msg">
        <div class="direct-chat-info clearfix">
            <span class="direct-chat-name pull-left">{{--{{ $message->user->roles[0]->name }}--}}{{ $message->user->first_name }} {{ $message->user->last_name }}</span>
            <span class="direct-chat-timestamp pull-right">{{ $message->created_at->diffForHumans() }}</span> </div>
        <img src="{{route('avatar',['q'=>$message->user->avatar])}}"
             alt="{{ $message->user->first_name }}" class="direct-chat-img" height="40">
        <div class="direct-chat-text">
            <p><?php echo html_entity_decode($message->body); ?></p>
        </div>
    </div>
        @else
    <div class="direct-chat-msg right">
        <div class="direct-chat-info clearfix">
            <span class="direct-chat-name pull-right">{{--{{ $message->user->roles[0]->name }}--}}{{ $message->user->first_name }} {{ $message->user->last_name }}</span>
            <span class="direct-chat-timestamp pull-left">{{ $message->created_at->diffForHumans() }}</span> </div>
        <img src="{{route('avatar',['q'=>$message->user->avatar])}}"
             alt="{{ $message->user->first_name }}" class="direct-chat-img" height="40">
        <div class="direct-chat-text">
            <p><?php echo html_entity_decode($message->body); ?></p>
        </div>
    </div>
        @endif


