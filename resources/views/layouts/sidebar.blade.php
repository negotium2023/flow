<ul class="list-group blackboard-sidebar">
    <a href="{{route('clients.create')}}" class="list-group-item list-group-item-action {{active('clients.create','active')}}"><i class="fa fa-plus"></i> Capture Client</a>
    <a href="{{route('recents')}}" class="list-group-item list-group-item-action {{active('recents','active')}}"><i class="fa fa-clock-o"></i> Recents</a>
    <a href="{{route('dashboard')}}" class="list-group-item list-group-item-action {{active('dashboard','active')}}"><i class="fa fa-tachometer"></i> Dashboard</a>
    @permission('admin')
    {{--<a style="cursor: pointer;" id="sidebar_report_dropdown" class="parent_2 list-group-item list-group-item-action"><i class="fa fa-bar-chart"></i> Business Reports <i class="pull-right fa fa-angle-right"></i></a>
    <div style="display: none;" class="child_2">
        <a style="padding-left: 45px !important;" href="{{route('graphs.newclients')}}" class="child_item list-group-item list-group-item-action {{active('graphs.newclients','active')}}">New Clients Added to CCH</a>
        <a style="padding-left: 45px !important;" href="{{route('graphs.targetdata')}}" class="child_item list-group-item list-group-item-action {{active('graphs.targetdata','active')}}">Client Target Data</a>
        <a style="padding-left: 45px !important;" href="{{route('graphs.yearlycomparison')}}" class="child_item list-group-item list-group-item-action {{active('graphs.yearlycomparison','active')}}">Yearly Comparison</a>
        <a style="padding-left: 45px !important;" href="{{route('reports.converted')}}" class="child_item list-group-item list-group-item-action {{active('reports.converted','active')}}">Converted</a>
        <a style="padding-left: 45px !important;" href="{{route('reports.fees')}}" class="child_item list-group-item list-group-item-action {{active('reports.fees','active')}}">Fees generated</a>
        <a style="padding-left: 45px !important;" href="{{route('reports.conversion')}}" class="child_item list-group-item list-group-item-action {{active('reports.conversion','active')}}">Conversion</a>
        <a style="padding-left: 45px !important;" href="{{route('reports.feeproposalsent')}}" class="child_item list-group-item list-group-item-action {{active('reports.feeproposalsent','active')}}">Fee Proposal Sent</a>
    </div>
    <a style="cursor: pointer;" id="sidebar_report_dropdown_3" class="parent_3 list-group-item list-group-item-action"><i class="fa fa-bar-chart"></i> Client Reports <i class="pull-right fa fa-angle-right"></i></a>
    <div style="display: none;" class="child_3">
        <a style="padding-left: 45px !important;" href="{{route('reports.referrer')}}" class="child_item list-group-item list-group-item-action {{active('reports.referrer','active')}}">Referrer</a>
    </div>--}}
    <!-- <a href="{{route('reports.clientreports')}}" class="list-group-item list-group-item-action {{active('reports.clientreports','active')}}"><i class="fa fa-bar-chart"></i> Client Reports</a> -->
    <a href="{{route('reports.index')}}" class="list-group-item list-group-item-action {{active('reports.index','active')}}"><i class="fa fa-bar-chart"></i> Custom Reports</a>
    @endpermission
    {{--<a href="{{route('calendarevents.index')}}" class="list-group-item list-group-item-action {{active('calendar','active')}}"><i class="fa fa-calendar-o"></i> Calendar</a>--}}
    @permission('maintain_client')
    <a href="{{route('clients.index')}}" class="list-group-item list-group-item-action {{active('clients.index','active')}}">
        <i class="fa fa-address-card-o"></i> Clients
        @auth
            {{Form::open(['url' => route('clients.index'), 'method' => 'get'])}}
            {{Form::select('step',$sidebar_process_statuses,null,['class'=>'form-control form-control-sm','id'=>'sidebar_process_status','placeholder'=>'Select step'])}}
            {{Form::close()}}
        @endauth
    </a>
    @endpermission
    {{--<a href="{{route('referrers.index')}}" class="list-group-item list-group-item-action {{active('referrers.index','active')}}"><i class="fa fa-address-book-o"></i> Referrers</a>--}}
    <a href="{{route('documents.index')}}" class="list-group-item list-group-item-action {{active('documents.index','active')}}"><i class="fa fa-file-text-o"></i> Documents</a>
    <a href="{{route('templates.index')}}" class="list-group-item list-group-item-action {{active('templates.index','active')}}"><i class="fa fa-file-o"></i> Templates</a>
    {{--<a href="{{route('emailsignatures.index')}}" class="list-group-item list-group-item-action {{active('emailsignatures.index','active')}}"><i class="fa fa-pencil-square-o"></i> Email Signatures</a>--}}
    <a href="{{route('insight.index')}}" class="list-group-item list-group-item-action {{active('insight.index','active')}}"><i class="fa fa-superpowers"></i> Insight</a>
   
    @permission('admin')
    <li class="list-group-item bg-light">Admin</li>
    <a href="{{route('processes.index')}}" class="list-group-item list-group-item-action {{active('processes.index','active')}}"><i class="fa fa-tasks"></i> Processes</a>
    <a href="{{route('users.index')}}" class="list-group-item list-group-item-action {{active('users.index','active')}}"><i class="fa fa-users"></i> Users</a>
    <a href="{{route('locations.index')}}" class="list-group-item list-group-item-action {{active('locations.index','active')}}"><i class="fa fa-globe"></i> Locations</a>
    <a href="{{route('roles.index')}}" class="list-group-item list-group-item-action {{active('roles.index','active')}}"><i class="fa fa-shield"></i> Roles</a>
    <a href="{{route('emailtemplates.index')}}" class="list-group-item list-group-item-action {{active('emailtemplates.index','active')}}"><i class="fa fa-at"></i> Email Templates</a>
    <a href="{{route('configs.index')}}" class="list-group-item list-group-item-action {{active('configs.index','active')}}"><i class="fa fa-cogs"></i> Configs</a>
    @endpermission
</ul>