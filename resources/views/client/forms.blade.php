@extends('client.show')

@section('title') Forms @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <a href="javascript:void(0)" onclick="startNewForm({{$client->id}},{{$client->process_id}})" class="btn btn-primary btn-sm">Start new Form</a>
        </div>
    </div>
@endsection

@section('tab-content')
    <div class="client-detail">
        <div class="content-container m-0 p-0">
            @yield('header')
            <div class="container-fluid container-content">
                <div class="col p-0 grid-items">
                    @if(count($client_processes) > 0)
                        <div class="card-group">
                            @for($i = 0;$i < count($client_processes);$i++)
                                <div class="col-md-6" style="{{($i % 2 == 0 || $i == 0 ? 'border-right:2px solid #eefafd' : '')}}">
                                    <div class="card p-0 m-0 h-100" style="border: 1px solid #ecf1f4;margin-bottom:0rem !important;">
                                        <div class="d-table" style="width: 100%;">
                                            <div class="grid-icon">
                                                <i class="far fa-file-alt"></i>
                                            </div>
                                            <div class="grid-text">
                                                <span class="grid-heading">{{$client_processes[$i]->process->name}}</span>
                                                Last Updated: {{\Carbon\Carbon::parse($client_processes[$i]->updated_at)->format('d/m/Y')}}
                                            </div>
                                            <div class="grid-btn">
                                                <a href="{{route('clients.progress',$client['id'])}}/{{$client_processes[$i]->process_id}}/{{$client_processes[$i]->step_id}}/1" class="btn btn-primary btn-block">View</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($i % 2 == 0)
                                @else
                        </div>
                        <div class="card-group">
                            @endif
                            @if($i == (count($client_processes)-1))
                        </div>
                    @endif
                    @endfor
                    @else
                        <div class="alert alert-info">There are currently no Forms in progress for this client.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-js')
    <script>

        $(document).ready(function(){

            $(".client-content").show();
            $('#overlay').fadeOut();
        });
    </script>
@endsection