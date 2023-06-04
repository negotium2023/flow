<table class="table table-bordered table-sm table-hover" style="border: 1px solid #dee2e6;display: table;border-collapse: collapse">
    <thead>
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
                <td class="table100-firstcol">{{$client['company']}}</td>
                @foreach ($step_names as $step_name)
                    @if ($step_name->id == $client['step_id'])
                        <td>{{$step_name->name}}</td>
                        @break
                    @endif
                @endforeach
                @foreach($client["data"] as $key => $val)
                    <td>@if($val != strip_tags($val)) {!! htmlspecialchars($val) !!} @else {{htmlspecialchars($val)}} @endif</td>
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