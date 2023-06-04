@extends('flow.default')

@section('title') View Email Template @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="{{route('emailtemplates.index')}}" class="btn btn-outline-primary btn-sm mt-3"><i class="fa fa-caret-left"></i> Back</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content w-100">
                <div class="table-responsive h-100 w-100">
                    @foreach($template as $result)
                        <table class="table table-responsive table-bordered mt-3 w-100">
                            <tbody class="w-100 d-table">
                            <tr class="w-100">
                                <td class="w-25">Name:</td>
                                <td class="w-75">{{$result->name}}</td>
                            </tr>
                            <tr>
                                <td>Email Subject:</td>
                                <td>{{$result->email_subject}}</td>
                            </tr>
                            <tr>
                                <td>Email Body</td>
                                <td>@php echo $result->email_content @endphp</td>
                            </tr>
                            </tbody>
                        </table>

                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection