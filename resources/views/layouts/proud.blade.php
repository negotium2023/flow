<!doctype html>
<html lang="{{app()->getLocale()}}">
<head>
    <title>{{env('APP_NAME')}}</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{!! asset('storage/favicon.ico') !!}" type="image/x-icon"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}" >
    <link href="{{ asset('fontawesome/css/all.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Pacifico" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/proud.css')}}">
    <link rel="stylesheet" href="{{asset('css/absa.css')}}">
</head>

<body>
<div class="content p-3">
    <div class="card">
        <div class="card-body px-4 pb-4 pt-2">
            <div class="text-center">
                <a href="{{route('home')}}" class="header-link"><img src="{{asset('assets/uhy.jpg')}}" class="blackboard-proud-logo p-3" style="width:300px;" /></a>
            </div>
            <hr>
                @if(Session::has('flash_success'))
                    <div class="alert alert-success alert-dismissible blackboard-alert">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <strong>Success!</strong> {{Session::get('flash_success')}}
                    </div>
                @endif

                @if(Session::has('flash_info'))
                    <div class="alert alert-info alert-dismissible blackboard-alert">
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                        <strong>Notice.</strong> {{Session::get('flash_info')}}
                    </div>
                @endif

            @if(Session::has('flash_danger'))
                <div class="alert alert-danger alert-dismissible blackboard-alert">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <strong>Error!</strong> {{Session::get('flash_danger')}}
                </div>
            @endif

            @if(Session::has('flash_warning'))
                <div class="alert alert-warning alert-dismissible blackboard-alert">
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    <strong>Error!</strong> {{Session::get('flash_warning')}}
                </div>
            @endif

            @yield('proud-form')
        </div>
        @yield('proud-footer')
    </div>
    @yield('proud-extra')
</div>

<script src="{{ asset('js/jquery/jquery-3.2.1.slim.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap/bootstrap.min.js') }}"></script>
<script>
    $(document).ready(function() {

        if(localStorage.getItem('popState') === 'shown'){
            localStorage.setItem('popState','')
        }
    });
</script>
<script>
    function start_countdown()
    {

        //alert(timeout);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var counter=10;
        myVar= setInterval(function()
        {
            if(counter>=0)
            {

            }
            if(counter==0)
            {
                window.location.reload();
            }
            counter--;
        }, 1000)
    }
</script>
<script>
    let timeout1 = ({!! env('SESSION_LIFETIME') !!} * 60) * 1000;
    let timeout2 =  timeout1 ;

    setTimeout(function(){ start_countdown(); }, timeout2);
</script>
</body>
</html>