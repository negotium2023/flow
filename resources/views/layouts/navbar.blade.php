<nav class="navbar fixed-top navbar-expand-lg navbar-light bg-light">
    <a title="Go home" class="navbar-brand" href="{{route('home')}}"><img src="{{asset('assets/logo-sm.jpg')}}" class="blackboard-navbar-logo"/></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarCollapse">

        <!-- Nav left -->
        <ul class="navbar-nav mr-auto mt-2 mt-lg-0">

        </ul>

        <!-- Nav right -->
        <ul class="navbar-nav my-2 my-lg-0">
            @guest
                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
            @else
                <blackboard-search></blackboard-search>

                @if($navbar_config_enable_support)
                    <li title="Submit help request" class="nav-item mr-3 p-0 pt-1"><a class="nav-link p-0 blackboard-avatar-navbar-dropdown" href="{{route('help.create',['page'=>url()->full()])}}"><i class="fa fa-lg fa-life-bouy"></i></a></li>
                @endif

                <li title="Change current location" class="nav-item dropdown mr-3 p-0 pt-1">
                    <a class="nav-link dropdown-toggle p-0 blackboard-avatar-navbar-dropdown" href="#" data-toggle="dropdown">
                        <i class="fa fa-lg fa-building"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#">Offices would go here</a>
                    </div>
                </li>

                @auth
                    <blackboard-notifications black-user="{{auth()->id()}}"></blackboard-notifications>
                @endauth

                <li title="View profile, change settings or logout" class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle p-0 blackboard-avatar-navbar-dropdown" href="#" data-toggle="dropdown">
                        <img src="{{route('avatar',['q'=>Auth::user()->avatar])}}" class="blackboard-avatar blackboard-avatar-navbar-img"/>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="{{route('profile')}}">My account</a>
                        <a class="dropdown-item" href="{{route('settings')}}">Settings</a>
                        {{--<a class="dropdown-item" href="{{route('emailsignatures.index')}}">Email Signature</a>--}}
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </li>
            @endguest
        </ul>
    </div>
</nav>