@extends('layouts.app')

@section('title') View Email Signature @endsection

@section('header')
    <h1><i class="fa fa-pencil-square-o"></i> @yield('title')</h1>
@endsection

@section('content')
    @foreach($template as $result)
        <table class="table table-responsive mt-3">
            <tr>
                <td>Name:</td>
                <td>{{$result->name}}</td>
            </tr>
            <tr>
                <td>Signature Content</td>
                <td>@php echo $result->template_content @endphp</td>
            </tr>
        </table>

    @endforeach
@endsection
@section('extra-js')

@endsection