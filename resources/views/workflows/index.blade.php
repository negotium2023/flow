@extends('flow.default')
@section('content')
    <board-component :board_id_global="{{$board_id}}" parameters="{{json_encode($vueParameters)}}"></board-component>
@endsection
