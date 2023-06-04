<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('home') }}" class="brand-link">
        <span class="logo-mini"><img src="{!! asset('assets/uhy.jpg') !!}" alt="Blackboard Logo"></span>
        <span class="logo-lg"><img src="{!! asset('assets/uhy.jpg') !!}" alt="Blackboard Logo"></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar blackboard-scrollbar" style="height: calc(100% - 6rem);">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{route('avatar',['q'=>(null !== Auth::user()) ? Auth::user()->avatar : ''])}}" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="{{route('profile')}}" class="d-block">{{ Auth::check() ? Auth::user()->first_name.' '.Auth::user()->last_name : '' }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            @if(Auth::check())
                <ul class="nav nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->
                        <li class="nav-item has-treeview">
                        <a href="{{route('clients.create')}}" class="nav-link {{ (\Request::route()->getName() == 'clients.create') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-plus"></i>
                            <p>
                                Capture Client
                            </p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="{{route('recents')}}" class="nav-link {{ (\Request::route()->getName() == 'recents') ? 'active' : '' }}">
                            <i class="nav-icon far fa-clock"></i>
                            <p>
                                Recents
                            </p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="{{route('dashboard')}}" class="nav-link {{ (\Request::route()->getName() == 'dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-tachometer-alt"></i>
                            <p>
                                Dashboard
                            </p>
                        </a>
                    </li>
                    @permission('maintain_client')
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link {{ (\Request::route()->getName() == 'clients.index') ? 'active' : '' }}">
                          <i class="nav-icon far fa-address-card"></i>
                          <p>
                            Clients
                            <i class="right fa fa-angle-right"></i>
                          </p>
                        </a>
                        <ul class="nav nav-treeview">
                          @auth
                            {{--<li class="nav-item">
                              <a href="{{route('clients.index')}}" class="nav-link">
                                <i class="fas fa-circle nav-icon"></i>
                                <p>All</p>
                              </a>
                            </li>--}}
                            @if($sidebar_process_statuses)
                              @foreach($sidebar_process_statuses as $sidebar_process)
                                {{--<li class="nav-item">
                                  <a href="{{route('clients.index')}}?step={{$key}}" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>{{$value}}</p>
                                  </a>
                                </li>--}}
                                <li class="nav-item has-treeview">
                                  @if($sidebar_process["steps"] != null && count($sidebar_process["steps"]) > 0)
                                    <a href="#" class="nav-link">
                                      <i class="nav-icon fas fa-circle"></i>
                                      <p>
                                        <span style="white-space: break-spaces;display: block;width: 125px;padding-right:15px;">{{$sidebar_process["name"]}}</span>
                                        <i class="right fa fa-angle-right"></i>
                                      </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                      @auth
                                        @foreach($sidebar_process["steps"] as $key => $value)
                                          <li class="nav-item">
                                            <a href="{{route('clients.index')}}?c=no&step={{$key}}&p={{$sidebar_process["id"]}}" class="nav-link">
                                              <i class="far fa-circle nav-icon"></i>
                                              <p>{{$value}}</p>
                                            </a>
                                          </li>
                                        @endforeach
                                      @if($sidebar_process["default"] == 1)
                                          <li class="nav-item">
                                            <a href="{{route('clients.index')}}?c=yes&step=1000&p={{$sidebar_process["id"]}}" class="nav-link">
                                              <i class="far fa-circle nav-icon"></i>
                                              <p>Converted</p>
                                            </a>
                                          </li>
                                          <li class="nav-item">
                                            <a href="{{route('clients.index')}}?step=1001&p={{$sidebar_process["id"]}}" class="nav-link">
                                              <i class="far fa-circle nav-icon"></i>
                                              <p>Not Progressing</p>
                                            </a>
                                          </li>
                                        @endif
                                      @endauth
                                    </ul>
                                  @else
                                    <a href="{{route('processes.show',$sidebar_process["id"])}}" class="nav-link">
                                      <i class="nav-icon fas fa-circle"></i>
                                      <p>
                                        {{$sidebar_process["name"]}}
                                      </p>
                                    </a>
                                  @endif
                                </li>
                              @endforeach
                            @endif
                          @endauth
                        </ul>
                      </li>
                    @endpermission
                    

                    <li class="nav-item has-treeview">
                        <a href="{{route('workflows')}}" class="nav-link {{ (\Request::route()->getName() == 'workflows') ? 'active' : '' }}">
                            <i class="nav-icon fab fa-trello"></i>
                            <p>
                                Pipeline
                            </p>
                        </a>
                    </li>

                    <li class="nav-item has-treeview">
                        <a href="{{route('calendar.index')}}" class="nav-link {{ (\Request::route()->getName() == 'calendarevents.index') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-book"></i>
                            <p>
                                Calendar
                            </p>
                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="{{route('custom_report.index') }}" class="nav-link {{ (\Request::route()->getName() == 'custom_report.index') ? 'active' : '' }}">
                            <i class="nav-icon far fa-chart-bar"></i>
                            <p>
                                Custom Report
                            </p>

                        </a>
                    </li>
                    <li class="nav-item has-treeview">
                        <a href="{{route('reports.assigned_actions')}}" class="nav-link {{ (\Request::route()->getName() == 'reports.assigned_actions') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>
                                Action Report
                            </p>
                        </a>
                    </li>
                    @permission('maintain_document')
                    @role('manager')
                    <li class="nav-item has-treeview">
                        <a href="{{route('documents.index')}}" class="nav-link {{ (\Request::route()->getName() == 'documents.index') ? 'active' : '' }}">
                            <i class="nav-icon far fa-file-alt"></i>
                            <p>
                                Documents
                            </p>
                        </a>
                    </li>
                    @endrole
                    @endpermission
                        {{--<ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{route('reports.assigned_actions')}}" class="nav-link {{ (\Request::route()->getName() == 'reports.assigned_actions') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-circle"></i>
                                    <p>
                                        Action Report
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('reports.index')}}" class="nav-link {{ (\Request::route()->getName() == 'reports.index') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-circle"></i>
                                    <p>
                                        Activity Reports
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('custom_report.index')}}" class="nav-link {{ (\Request::route()->getName() == 'custom_report.index') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-circle"></i>
                                    <p>
                                        Custom Reports
                                    </p>
                                </a>
                            </li>
                            --}}{{--<li class="nav-item">
                                <a href="{{route('reports.audit_report')}}" class="nav-link {{ (\Request::route()->getName() == 'reports.audit_report') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-circle"></i>
                                    <p>
                                        Audit Report
                                    </p>
                                </a>
                            </li>--}}{{--
                        </ul>--}}
                    </li>

                    @permission('admin')
                    <li class="header"><a href="javascript:void(0)" id="admin-menu"><i class="nav-icon fa fa-briefcase"></i><p>ADMIN</p></a></li>
                    @permission('process_editor')
                    @if(auth()->user()->is('admin') || auth()->user()->is('manager'))
                    <li class="nav-item has-treeview admin-menu">
                        <a href="{{route('processesgroup.index')}}?f=0" class="nav-link {{ Request::get('f') == "0" && (\Request::route()->getName() == 'processesgroup.index') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-tasks"></i>
                            <p>
                                Processes
                            </p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->is('admin') || auth()->user()->is('manager'))
                    <li class="nav-item has-treeview admin-menu">
                        <a href="{{route('processesgroup.index')}}?f=1" class="nav-link {{ Request::get('f') == "1"  && (\Request::route()->getName() == 'processesgroup.index') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-file"></i>
                            <p>
                                Forms
                            </p>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->is('admin') || auth()->user()->is('manager'))
                    <li class="nav-item has-treeview admin-menu">
                        <a href="{{route('forms.index')}}" class="nav-link {{ (\Request::route()->getName() == 'forms.index') ? 'active' : '' }}">
                            
                            <i class="nav-icon fa fa-id-badge"></i>
                            <p>
                                CRM
                            </p>
                        </a>
                    </li>
                    @endif
                    @endpermission

                    @permission('maintain_template')
                    @if(auth()->user()->is('admin') || auth()->user()->is('manager'))
                    <li class="nav-item has-treeview admin-menu">
                        <a href="javascript:void(0)" class="nav-link {{ (\Request::route()->getName() == 'templates.index') ? 'active' : '' }}">
                            <i class="nav-icon far fa-file"></i>
                            <p>
                                Templates
                            </p>
                        </a>
                        <ul class="nav nav-treeview admin-menu">
                            <li class="nav-item has-treeview">
                                <a href="{{route('templates.index')}}" class="nav-link {{ (\Request::route()->getName() == 'templates.index') ? 'active' : '' }}">
                                    <i class="nav-icon fa fa-circle"></i>
                                    <p>
                                        Document Templates
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item has-treeview">
                                <a href="{{route('emailtemplates.index')}}" class="nav-link {{ (\Request::route()->getName() == 'emailtemplates.index') ? 'active' : '' }}">
                                    <i class="nav-icon fa fa-circle"></i>
                                    <p>
                                        Email Templates
                                    </p>
                                </a>
                            </li>
                            {{--<li class="nav-item has-treeview">
                                <a href="{{route('whatsapptemplates.index')}}" class="nav-link {{ (\Request::route()->getName() == 'whatsapptemplates.index') ? 'active' : '' }}">
                                    <i class="nav-icon fa fa-circle"></i>
                                    <p>
                                        Whatsapp Templates
                                    </p>
                                </a>
                            </li>--}}
                        </ul>
                    </li>
                    @endif
                    @endpermission
                    @permission('actions')
                    @if(auth()->user()->is('admin') || auth()->user()->is('manager'))
                    <li class="nav-item has-treeview admin-menu">
                        <a href="{{route('action.index')}}" class="nav-link {{ (\Request::route()->getName() == 'action.index') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-project-diagram"></i>
                            <p>
                                Actions
                            </p>
                        </a>
                    </li>
                    @endif
                    @endpermission
                    @if(auth()->user()->is('admin') || auth()->user()->is('manager'))
                    <li class="nav-item has-treeview admin-menu">
                        <a href="javascript:void(0)" class="nav-link">
                            <i class="nav-icon fa fa-globe"></i>
                            <p>
                                Master Data
                            </p>
                        </a>
                        <ul class="nav nav-treeview admin-menu">
                            <li class="nav-item has-treeview">
                                <a href="{{route('locations.index')}}" class="nav-link {{ (\Request::route()->getName() == 'locations.index') ? 'active' : '' }}">
                                    <i class="nav-icon fa fa-circle"></i>
                                    <p>
                                        Locations
                                    </p>
                                </a>
                            </li>
                            <li class="nav-item has-treeview">
                                <a href="{{route('task_types.index')}}" class="nav-link {{ (\Request::route()->getName() == 'task_types.index') ? 'active' : '' }}">
                                    <i class="nav-icon fa fa-circle"></i>
                                    <p>
                                        Task Types
                                    </p>
                                </a>
                            </li>
                            {{--<li class="nav-item has-treeview">
                                <a href="{{route('crm_categories.index')}}" class="nav-link {{ (\Request::route()->getName() == 'crm_categories.index') ? 'active' : '' }}">
                                    <i class="nav-icon fa fa-circle"></i>
                                    <p>
                                        CRM Categories
                                    </p>
                                </a>
                            </li>--}}
                        </ul>
                    </li>
                    @endif
                    <li class="nav-item has-treeview admin-menu">
                        <a href="{{ route('users.index')}}" class="nav-link {{ (\Request::route()->getName() == 'users.index') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-users"></i>
                            <p>
                                Users
                            </p>
                        </a>
                    </li>
                    @if(auth()->user()->is('admin') || auth()->user()->is('manager'))
                    <li class="nav-item admin-menu">
                        <a href="{{ route('roles.index') }}" class="nav-link {{ (\Request::route()->getName() == 'roles.index') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-cog"></i>
                            <p>
                                Roles
                            </p>
                        </a>
                    </li>
                    <li class="nav-item admin-menu">
                        <a href="{{route('configs.index')}}" class="nav-link {{ (\Request::route()->getName() == 'configs.index') ? 'active' : '' }}">
                            <i class="nav-icon fa fa-cogs"></i>
                            <p>
                                Configs
                            </p>
                        </a>
                    </li>
                    @endif
                    @endpermission

                    @endif
                </ul>
                <ul class="nav nav-sidebar support flex-column" role="menu" data-accordion="false" style="width:93%;position: absolute;bottom: 0;">
                    <!-- Add icons to the links using the .nav-icon class
                         with font-awesome or any other icon font library -->
                    <li class="nav-item has-treeview">
                        <a href="https://support.blackboardbs.com/requester/tickets/create" target="_blank" class="nav-link">
                            <i class="nav-icon fas fa-question-circle"></i>
                            <p>
                                Support
                            </p>
                        </a>
                    </li>
                </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
