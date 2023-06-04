<!-- Navbar -->
<nav class="main-header navbar navbar-expand bg-white navbar-light border-bottom">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fa fa-bars"></i></a>
        </li>
    </ul>

    <!-- SEARCH FORM -->
    <form class="form-inline ml-3">
        <div class="input-group input-group-sm">
          @auth
            <blackboard-search black-user="{{auth()->id()}}"></blackboard-search>
          @endauth
          {{--<input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-navbar" type="submit">
              <i class="fa fa-search"></i>
            </button>
          </div>--}}
        </div>
      </form>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        @if($navbar_config_enable_support)
            <li title="Submit help request" class="nav-item dropdown"><a class="nav-link blackboard-avatar-navbar-dropdown" href="{{route('help.create',['page'=>url()->full()])}}"><i class="fas fa-life-ring"></i></a></li>
        @endif
        @if(Auth::check())
            @auth
                <blackboard-messages black-user="{{auth()->id()}}"></blackboard-messages>
            @endauth
        @endif
    <!-- Notifications Dropdown Menu -->
        @if(Auth::check())

            @auth
                <blackboard-notifications black-user="{{auth()->id()}}"></blackboard-notifications>
            @endauth

        <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown user">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <img src="{{route('avatar',['q'=> (null !== Auth::user()) ? Auth::user()->avatar : ''])}}" class="blackboard-avatar blackboard-avatar-navbar-img"/>
                </a>
                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                    <a href="{{route('profile')}}" class="dropdown-item">
                        User profile
                    </a>
                    <a href="{{route('settings')}}" class="dropdown-item">
                        Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </li>
        @endif
    </ul>
</nav>
<!-- /.navbar -->
