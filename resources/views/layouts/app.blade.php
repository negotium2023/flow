<!doctype html>
<html lang="{{app()->getLocale()}}" style="height: 100%;">
<head>
    <title>@yield('title')</title>
    <!--
    ______ _            _    _                         _
    | ___ \ |          | |  | |                       | |
    | |_/ / | __ _  ___| | _| |__   ___   __ _ _ __ __| |
    | ___ \ |/ _` |/ __| |/ / '_ \ / _ \ / _` | '__/ _` |
    | |_/ / | (_| | (__|   <| |_) | (_) | (_| | | | (_| |
    \____/|_|\__,_|\___|_|\_\_.__/ \___/ \__,_|_|  \__,_|

    -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth" content="{{ auth()->id() }}">

    <link rel="shortcut icon" href="{{asset('favicon.ico')}}" type="image/x-icon">
    <link rel="icon" href="{{asset('favicon.ico')}}" type="image/x-icon">

    <!-- CSS -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/absa.css') }}" rel="stylesheet">
    @yield('extra-css')
</head>
<body style="background:#FFF;height: 100%;">

<!-- Frontend wrapper -->
<div id="app-error" style="height: 100%;">


    {{--<div class="row">
        <div class="blackboard-hero p-3 pl-4 pr-4">
            @yield('header')
        </div>
    </div>--}}

    <div class="row ml-0 mr-0" style="height: 100%;">

        <div id="blackboard-content-div" class="col-lg-12">
            @yield('content')
        </div>
    </div>
</div>

</body>
</html>
