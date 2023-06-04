@extends('client.show')

@section('tab-content')
    <div class="client-detail">
    <div class="content-container client-basket-content m-0 p-0">
        <h3 class="w-100">Client Basket for <span class="process-name">{{$client->process->name}}</span> <a href="javascript:void(0)" class="changebasket" onclick="changeBasket({{$client->id}})"><i class="fas fa-edit"></i></a></h3>
        <input type="hidden" id="process_id" value="{{$process_id}}">
        <input type="hidden" id="step_id" value="{{$step['id']}}">
        <div class="row h-100">
            <div class="col-md-6 application h-100">
                <h3 class="w-100">Application Details</h3>
                <div class="application-content">
                    <div style="height: 100%;margin:auto;width:100%">
                        <div class="spinner"></div>
                    </div>
                </div>

            </div>
            <div class="col-md-6 personal h-100">
                <h3 class="w-100">Personal Details</h3>
                <div class="personal-content">
                    <div style="height: 100%;margin:auto;width:100%">
                        <div class="spinner"></div>
                    </div>
                </div>
            </div>
            <div class="basket-btn-group">
                <a href="javascript:void(0)" onclick="sendClientEmail('{{$client->id}}','{!! $client->email !!}')" class="btn btn-primary float-right">Request client feedback</a>
            </div>
        </div>
    </div>
    </div>
    <div class="modal fade" id="confirmEmailModal">
        <div class="modal-dialog" style="width:550px !important;max-width:550px;">
            <div class="modal-content">
                <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                    <h5 class="modal-title">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="form-group text-left">
                        <input type="hidden" name="client_id" id="confirmEmailClient">
                        <label id='confirmEmailMessage'></label>

                    </div>
                    <div class="form-group text-left all-emails">
                        <ul id='confirmEmails'>

                        </ul>
                    </div>
                    <div class="form-group text-left">
                        {{Form::text('extra-email', '',['class'=>'confirmExtraEmail form-control form-control-sm','size'=>'50','placeholder'=>'Enter email address'])}}
                    </div>
                    <div class="form-group text-right">
                        <button type="button" class="btn btn-sm btn-danger" id="confirmEmailCancel">No</button>
                        <button type="button" class="btn btn-sm btn-success" id="confirmEmailOk">Yes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalChangeBasket">
        <div class="modal-dialog" style="width:550px !important;max-width:550px;">
            <div class="modal-content">
                <div class="modal-header text-center" style="border-bottom: 0px;padding:.5rem;">
                    <h5 class="modal-title">&nbsp;</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body mx-3">
                    <div class="form-group text-left">
                        <select class="chosen-select form-control form-control-sm float-left ml-3" id="changebasketdd">
                            @forelse($view_process_dropdown as $k=>$v)
                                <optgroup label="{{$k}}">
                                    @foreach($v as $key=>$value)
                                        <option value="{{$value['id']}}">{{$value["name"]}}</option>
                                    @endforeach
                                </optgroup>
                            @empty
                                <option value="">There are no applications available for this client.</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group text-right">
                        <button type="button" class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-sm btn-success" id="changeBasketOk">Ok</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-js')
    <script>
        $('#changeBasketOk').on('click',function(){

            let process_id = $('#modalChangeBasket').find('#changebasketdd').val();

            $('.process-name').html($('#modalChangeBasket').find('#changebasketdd option[value=' + process_id + ']').text());

            getBasket(process_id);

            $('#modalChangeBasket').modal('hide');
        });

        function changeBasket(clientid){
            let client_id = clientid;
            let process_id = $('#process_id').val();

            $('#modalChangeBasket').modal('show');
            $('#modalChangeBasket').find('#changebasketdd option[value=' + process_id + ']').attr("selected","selected");
            $('#modalChangeBasket').find('#changebasketdd').trigger("chosen:updated");
        }

        function getBasket(process_id){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                url: '/client/' + {{ $client->id }} +'/'+ process_id +'/clientbasket',
                type: "GET",
                dataType: "json",
                success: function (data) {
                    let activities = '';
                    let details = '';


                    $.each(data.client_basket_activities, function(key, value) {

                        $.each(value.body, function (k, v) {
                            activities = activities + '<div class="d-block pb-3 activity' + v.id + '" style="width: 85%;"><span style="color:#06496f;display: inline-block;width:calc(100% - 30px)">' + v.name + '</span><a href="javascript:void(0)" onclick="removeActivityFromBasket(' + {{ $client->id }} + ',' + v.id + ')" style="color:#b5c9d4;cursor: pointer;" class="float-right">x</a> </div>';
                        });
                    });

                    $.each(data.client_basket_details, function(key, value) {
                        $.each(value.body, function (k, v) {
                            details = details + '<div class="d-block pb-3 detail' + v.id + '" style="width: 85%;"><span style="color:#06496f;display: inline-block;width:calc(100% - 30px)">' + v.name + '</span><a href="javascript:void(0)" onclick="removeDetailFromBasket(' + {{ $client->id }} + ',' + v.id + ')" data-inputid="' + v.id + '" style="color:#b5c9d4;cursor: pointer;" class="float-right">x</a> </div>';
                        });
                    });

                    if(activities.length === 0){
                        activities = activities + '<div class="alert alert-info">There are currently no Activities included in the basket.</div>';
                    };

                    if(details.length === 0){
                        details = details + '<div class="alert alert-info">There are currently no Client Details included in the basket.</div>';
                    };

                    $('.application div:eq(0)').html(activities);
                    $('.personal div:eq(0)').html(details);
                }
            });
        }

        function removeDetailFromBasket(client_id,input){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var status = 0;
            var form_id = 2;
            var self=this;

            $.ajax({
                type: "POST",
                dataType: "json",
                url: '/forms/include-in-basket',
                data: {'status': status, 'input_id':input, 'client_id': client_id,'form_id':form_id},
                success: function(data){

                    toastr.success(data.success);

                    toastr.options.timeOut = 500;

                    $(document).find('.detail' + input).remove();
                }
            });
        }

        function removeActivityFromBasket(client_id,activity){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var status = 0;
            var process_id = $('#process_id').val();

            $.ajax({
                type: "GET",
                dataType: "json",
                url: '/activity/include-in-basket',
                data: {'status': status, 'activity_id':activity, 'client_id': client_id,'process_id':process_id},
                success: function(data){
                    toastr.success(data.success);

                    toastr.options.timeOut = 500;

                    $(document).find('.activity' + activity).remove();
                }
            });
        }

        $(function () {

            let process_id = $('#process_id').val();

            getBasket(process_id);
        });
    </script>
@endsection

