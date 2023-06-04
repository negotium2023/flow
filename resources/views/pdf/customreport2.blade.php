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
            <th>Name</th>
            <th>Process Stage</th>
            @foreach($fields as $key => $val)
                @if($val != null)
                    <th>{{$val}}</th>
                @endif
            @endforeach
        </tr>
        </thead>
        <tbody>
        @forelse($clients as $client)
            @if(isset($client['id']))
                <tr>
                    <td class="table100-firstcol"><a href="{{route('clients.show',$client['id'])}}">{{$client['company']}}</a></td>
                    @foreach ($step_names as $step_name)
                        @if ($step_name->id == $client['step_id'])
                            <td>{{$step_name->name}}</td>
                            @break
                        @endif
                    @endforeach
                    @foreach($client["data"] as $key => $val)
                        <td><a href="{{route('clients.show',$client["id"])}}">@if($val != strip_tags($val)) {!! $val !!} @else {{$val}} @endif</a></td>
                    @endforeach
                </tr>
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