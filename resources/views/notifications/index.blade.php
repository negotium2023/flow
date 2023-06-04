@extends('flow.default')

@section('title') Notification History @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
        <table class="table table-bordered table-sm">
            <thead class="btn-dark">
                <tr>
                    <th>Notification</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
            @foreach($notifications as $result)
                <tr @if($result->seen_at != null) @else class="btn-light" @endif>
                    <td><a href="javascript:void(0)" onclick="notify({{$result->sid}})" data-link="{{$result->link}}" class="notlink">{{$result->name}}</a></td>
                    <td>{{\Carbon\Carbon::parse($result->created_at)->format('Y-m-d')}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
                </div>
            </div>
    </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $("a.notlink").on('click',function(e){
            e.preventDefault();
        });

        function notify(id){
            var data = '';
            var links = $("a:focus").attr('data-link');
            data = {
                id: id
            };

            axios.post('/readnotificationshistory',data).then(response => {
               console.log(links);
                window.location.href = links;
            }).catch(error => {
                // todo handle error
            });
        }
    </script>
@endsection
