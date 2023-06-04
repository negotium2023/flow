<html>
<head>
    <title>Custom Report</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        html,body{
            width: 100%;
        }
        table{
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: 1px solid black;
            padding: 5px;
            font-size:12px;
            word-break: break-all;
            overflow-wrap: break-word;
        }

        th{
            background:#ccc;
        }

        table tr td, table tr th { page-break-inside: avoid; }
    </style>
</head>
<body>
    <div class="table-responsive">
        <table class="table table-bordered table-sm table-hover" style="border: 1px solid #dee2e6;display: table;border-collapse: collapse">
            <thead class="btn-dark">
            <tr>
                <th>P/R</th>
                <th>Name</th>
                <th>Case Number</th>
                <th>CIF Code</th>
                <th>Committee</th>
                <th>Trigger Type</th>
                <th>Activity Name: {{$activity->name}}</th>
                {{--{!! $activity->actionable_type == "App\ActionableText" || $activity->actionable_type == "App\ActionableDate" || $activity->actionable_type == "App\ActionableDropdown"/*|| $activity->actionable_type == "App\ActionableDocument"*/ ? '<th>Activity Value</th>' : '' !!}--}}
                <th>Instruction Date</th>
                <th>Assigned User</th>

            </tr>
            </thead>
            <tbody>
            @forelse($clients as $client)
                @if(isset($client['id']))
                    <tr>
                        <td class="table100-firstcol"><a href="{{route('clients.show',$client['id'])}}">{{$client['type']}}</a></td>
                        <td><a href="{{route('clients.show',$client['id'])}}">{{$client['company']}}</a></td>
                        <td><a href="{{route('clients.show',$client['id'])}}">{{$client['case_nr']}}</a></td>
                        <td><a href="{{route('clients.show',$client['id'])}}">{{$client['cif_code']}}</a></td>
                        <td><a href="{{route('clients.show',$client['id'])}}">{{$client['committee']}}</a></td>
                        <td><a href="{{route('clients.show',$client['id'])}}">{{$client['trigger']}}</a></td>

                        {!! $activity->actionable_type == "App\ActionableDropdown" ? '<td><a href="'.route('clients.show',$client['id']).'">'.$client["activity_data"].'</a></td>' : '' !!}
                        {!! $activity->actionable_type == "App\ActionableDocument" || $activity->actionable_type == "App\ActionableBoolean" ? '<td><a href="'.route('clients.show',$client['id']).'">'.$client['completed_yn'].'</a></td>' : '' !!}
                        {!! $activity->actionable_type == "App\ActionableText" || $activity->actionable_type == "App\ActionableDate" ? '<td><a href="'.route('clients.show',$client['id']).'">'.$client['data_value'].'</a></td>' : '' !!}
                        <td><a href="{{route('clients.show',$client["id"])}}">{{$client['instruction_date']}}</a></td>
                        <td><a href="{{route('clients.show',$client["id"])}}">{{$client['consultant']}}</a></td>
                    </tr>
                @endif
                @if(isset($client['rp']) && count($client['rp']) > 0)
                    @foreach($client['rp'] as $rp)
                        <tr class="bg-gray-light">
                            <td>{{$rp['type']}}</td>
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">{{$rp['company']}}</a></td>
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">{{$rp['case_nr']}}</a></td>
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">{{$rp['cif_code']}}</a></td>
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">{{$rp['committee']}}</a></td>
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">{{$rp['trigger']}}</a></td>
                            @foreach($rp["data"] as $key => $val)
                                <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">@if($val != strip_tags($val)) {!! $val !!} @else {{$val}} @endif</a></td>
                            @endforeach
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">{{$rp['instruction_date']}}</a></td>
                            <td><a href="{{route('relatedparty.show',['client_id' => $rp["client_id"],'process_id' => $rp["process"],'step_id' => $rp["step"],'related_party_id'=>$rp["id"]])}}">@if($rp['consultant']['consultant'] != null){{$rp['consultant']['consultant']->first_name}} {{$rp['consultant']['consultant']->last_name}} @endif</a></td>
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr>
                    <td colspan="100%" class="text-center"><small class="text-muted">No clients match those criteria.</small></td></td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>