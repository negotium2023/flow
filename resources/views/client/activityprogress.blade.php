@extends('client.show')

@section('tab-content')
    <div class="col">
        <div class="process_step" id="activity_progress_{{$step->id}}">
            {{--<h3 id="{{$step->id}}" class="p-2" style="background-color: {{$client->process->getStageHex($step->name)}};width:100%;">{{$step->name}}</h3>--}}
            <h3 id="{{$step->id}}" class="p-2" style="width:100%;">{{$activity_progress_name}}</h3>
            <div style="padding: 0.5rem"><strong>Completed activities:</strong>
            <ul>
                {!! $completed !!}
            </ul>
                <strong>Not completed activities:</strong>
            <ul>
                {!! $not_completed !!}
            </ul>
            <a href="{{route('clients.progress', $client->id)}}/{{$step->process_id}}/{{$step->id}}">Back to form</a></div>
        </div>
    </div>
@endsection
@section('extra-js')
    {{--<script>
        showActivityProgress({{$id}});
        
        function showActivityProgress(id){
            $("#activity_progress_"+id).show();
        }
    </script>--}}
@endsection