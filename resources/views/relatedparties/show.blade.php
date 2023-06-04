@extends('client.show')
@section('tab-content')

    @include('relatedparties.related_parties')

@if(isset($related_party->description) && $related_party->description != null)
    <div class="col-sm-12">
        <hr />
    </div>
    <div class="col-sm-12">
        <h3 id="{{$step['order']}}" class="d-inline">{{($related_party->first_name != null ? $related_party->first_name.' '.$related_party->last_name : $related_party->company)}}</h3>

        @include('relatedparties.related_party_process')
        <ul class="nav nav-tabs nav-fill mt-3">
            <li class="nav-item">
                <a class="nav-link {{active('relatedparty.show','active')}}" href="{{route('relatedparty.show',['client_id' => $client,'process_id' => $related_party->process_id,'step_id' => $r,'related_party_id'=>$related_party->id])}}">Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{(\Request::is('relatedparty/*/progress/*') ? 'active' : '')}}" href="{{route('relatedparty.stepprogress',['client_id' => $client,'process_id' => $related_party->process_id,'step_id' => $r,'related_party_id'=>$related_party->id])}}">Progress</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{active('relatedparty.documents','active')}}" href="{{route('relatedparty.documents',['client'=>$client,'related_party'=>$related_party])}}">Documents</a>
            </li>
            <li class="nav-item">
                {{--<a class="nav-link {{active('clients.actions','active')}}" href="{{route('clients.actions',$client)}}">Actions</a>--}}
                <a class="nav-link {{active('clients.actions','active')}}" href="javascript:void(0)" style="cursor: not-allowed;">Actions</a>
            </li>
        </ul>

        <div class="row m-0 pt-3 pb-5 @if(isset($related_party->description) && $related_party->description != null) border border-top-0 @endif">
            @yield('tab-content2')
        </div>

    </div>
@endif

@endsection
@section('extra-css')
    <link rel="stylesheet" href="{{asset('chosen/chosen.min.css')}}">

    <style>
        a:focus{
            outline:none !important;
            border:0px !important;
        }

        .activity a{
            color: rgba(0,0,0,0.5) !important;
        }

        .activity a.dropdown-item {
            color:#212529 !important;
        }

        .btn-comment{
            padding: .25rem .25rem;
            font-size: .575rem;
            line-height: 1;
            border-radius: .2rem;
        }

        .modal-dialog {
            max-width: 700px;
            margin: 1.75rem auto;
            min-width: 500px;
        }

        .chosen-container, .chosen-container-multi{
            width:100% !important;
        }

        .modal-open .modal{
            padding-right: 0px !important;
        }
    </style>
@endsection
@section('extra-js')

    <script>
        $(function(){
            $('#addGroup').on('click', function() {
                //var cur = $(this).attr('class').match(/\d+$/)[0];
                let cur = parseInt($("#max_group").val());
                let next = cur+1;
                $('.group-'+next).css('display','table');
                $("#max_group").val(next)
            });

            $("#first_name").change(function(){
                $('#related_party_id').val("");
            });

            $("#last_name").change(function(){
                $('#related_party_id').val("");
            });

            $('#related_party_id').on('change', function(){
                var related_party_id = $("select[name=related_party_id]").val();
                if(related_party_id == ''){
                    $('#first_name').val('');
                    $('#last_name').val('');
                    return 0;
                }
                axios.get('../getclient/'+related_party_id, {})
                    .then(function (data) {
                        $('#first_name').val(data.data.first_name);
                        $('#last_name').val(data.data.last_name);
                    })
                    .catch(function () {
                        console.log("An Error occurred!!!");
                    });
            });

        });

        $(document).find('textarea').each(function () {
            var offset = this.offsetHeight - this.clientHeight;

            $(this).on('keyup input focus', function () {
                $(this).css('height', 'auto').css('height', this.scrollHeight + offset);
            });

            $(this).trigger("input");
        });
    </script>
@endsection