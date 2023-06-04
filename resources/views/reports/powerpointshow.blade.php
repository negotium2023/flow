@extends('adminlte.default')
@section('title') Powerpoint Report @endsection
@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <a href="{{route('reports.index')}}" class="btn btn-dark btn-sm float-right"><i class="fa fa-caret-left"></i> Back</a>
    </div>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-9">
                <form id="customreportsform" class="form-inline mt-3">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="fa fa-search"></i>
                            </div>
                        </div>
                        {{Form::text('s',old('s'),['class'=>'form-control form-control-sm','placeholder'=>'Search...'])}}
                    </div>
                    <button type="submit" class="btn btn-sm btn-secondary ml-2 mr-2"><i class="fa fa-search"></i> Search</button>
                </form>
            </div>
        </div>
        <hr>
        <div>
            {{Form::open(['url' => route('reports.powerpointexport'), 'method' => 'post'])}}
            <div class="form-group ml-3">
                {{Form::label('select_template', 'Template')}}
                <div class="input-group">
                    <td>{{Form::select('template_id',$templates,old('template_id'),['class'=>'form-control form-control-sm','id'=>'template_id'])}}</td>
                    @foreach($errors->get('template_id') as $error)
                        <div class="invalid-feedback">
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group ml-3 activity_div">
                {{Form::label('client_search', 'Client')}}
                <div class="input-group form-inline mb-2">
                    {{Form::text('client_search', old('client_search'), ['class'=>'form-control form-control-sm col-sm-2', 'id' => 'client_search'])}}
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fa fa-search"></i> </span>
                    </div>
                    &nbsp;
                    <a href="javascript:void(0)" class="btn btn-sm btn-info form-inline" id="clear_search" /><i class="fa fa-eraser"></i> Clear</a>
                </div>
                <div class="input-group table-responsive"style="max-height:350px;height:350px;">
                    <table class="table table-bordered table-clients" style="width: 100%;border-bottom:1px solid #dee2e6;max-height:350px;height:350px;">
                        <thead style="width: 100%;">
                        <tr class="bg-dark">
                            <th class="last">Select</th>
                            <th>Client</th>
                            <th>ID Number</th>
                        </tr>
                        </thead>
                        <tbody style="width: 100%;" id="report_client">
                        <tr style="width: 100%;">
                                <td colspan="3" class="loader" align="center" style="border-bottom: 0px;">
                                </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="form-group ml-3 mb-3">
                <button type="submit" class="btn btn-default btn-md form-inline" id="view_report">View</button>
            </div>
            {{Form::close()}}
        </div>
    </div>

@endsection
@section('extra-js')
    <script>
        $(function(){

            getClients();

            var spinner = '<svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"\n' +
                '     width="40px" height="40px" preserveAspectRatio="xMaxYMax meet" viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;">\n' +
                '  <path fill="#fff" d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">\n' +
                '    <animateTransform attributeType="xml"\n' +
                '      attributeName="transform"\n' +
                '      type="rotate"\n' +
                '      from="0 25 25"\n' +
                '      to="360 25 25"\n' +
                '      dur="1s"\n' +
                '      repeatCount="indefinite"/>\n' +
                '    </path>\n' +
                '  </svg>';

            var xTriggered = 0;

            $('#client_search').on('keyup',function (event){

                let search = $('#client_search').val();

                if($('#client_search').val().length > 0) {
                    if (event.which == 13) {
                        event.preventDefault();
                    }
                    xTriggered++;

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        dataType: 'json',
                        url: '/search_clients/' + search,
                        type: 'GET',
                        beforeSend: function () {
                            $('.loader').html('<tr><td colspan="2">' + spinner + '</td></tr>');
                        },
                        success: function (data) {
                            let rows = '';
                            $.each(data, function (key, value) {
                                rows = rows + '<tr><td align="center"><input type="radio" name="client_id[]" class="client_id" value="' + value.id + '" /></td><td>' + value.name + '</td><td>' + value.id_number + '</td></tr>';
                            });
                            //alert(rows);
                            $("#report_client").html(rows);
                        }
                    });
                } else {
                    getClients()
                }
            });

            $('#clear_search').on('click',function(){
                $('#client_search').val('');
                getClients()
            });

            function getClients() {

                let err = 0;
                if(err === 0) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.ajax({
                        dataType: 'json',
                        url: '/get_clients',
                        type: 'GET',
                        beforeSend: function () {
                            $('.loader').html(spinner);
                        },
                        success: function (data) {
                            let rows = '';
                            $.each(data, function (key, value) {
                                    rows = rows + '<tr><td align="center"><input type="radio" name="client_id[]" class="client_id" value="' + value.id + '" /></td><td>' + value.name + '</td><td>' + value.id_number + '</td></tr>';
                            });
                            //alert(rows);
                            $(".table-clients").addClass('table-striped');
                            $("#report_client").html(rows);
                        }
                    });
                }
            }


        });



    </script>
@endsection