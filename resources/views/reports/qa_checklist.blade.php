@extends('adminlte.default')

@section('title') Checklist Report @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <hr />
        <div class="table-responsive mt-3">
            {{--<table class="table table-bordered table-sm table-striped">
                <thead class="btn-dark">
                <tr>
                    <th>QA Item</th>
                    <th class="text-right">Pass</th>
                    <th class="text-right">Fail</th>
                    <th class="text-right">Not Reviewed</th>
                </tr>
                </thead>
                <tbody>
                @forelse($data as $key => $checklist)

                        <tr>
                            <td>{{$key}}</td>
                            <td class="text-right">{{$checklist["pass"]}}</td>
                            <td class="text-right">{{$checklist["fail"]}}</td>
                            <td class="text-right">{{$checklist["not_reviewed"]}}</td>
                        </tr>

                @empty
                    <tr><td colspan="5">No Reports were found.</td></tr>
                @endforelse
                </tbody>
            </table>--}}
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header text-center">
                            <h5>Checklist Graph</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="checklist" width="600" height="400"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section("extra-js")
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js" integrity="sha256-R4pqcOYV8lt7snxMQO/HSbVCFRPMdrhAFMH+vr9giYI=" crossorigin="anonymous"></script>
    <script>
        var checklistCanvas = document.getElementById("checklist");

        var passed = {
            label: 'Passed',
            data: {{collect($data)->map(function ($checklist){return $checklist["pass"];})->values()}},
            backgroundColor: 'rgba(77, 152, 219, 1)',
            borderWidth: 0
        };

        var failed = {
            label: 'Failed',
            data: {{collect($data)->map(function ($checklist){return $checklist["fail"];})->values()}},
            backgroundColor: 'rgba(251, 119, 0, 1)',
            borderWidth: 0
        };
        var notReviewed = {
            label: 'Not Reviewed',
            data: {{collect($data)->map(function ($checklist){return $checklist["not_reviewed"];})->values()}},
            backgroundColor: 'rgba(165, 165, 165, 1)',
            borderWidth: 0
        };

        var checklistData = {
            labels: {!! collect($data)->keys() !!},
            datasets: [passed, failed, notReviewed]
        };

        var checlistOptions = {
            maintainAspectRatio: false,
            scales: {
                xAxes: [{
                    gridLines: {
                        display:false
                    },
                    ticks: {
                        autoSkip: false
                    }
                }],
                yAxes: []
            },
            legend: {
                position: 'bottom',
            }
        };

        var barChart = new Chart(checklistCanvas, {
            type: 'bar',
            data: checklistData,
            options: checlistOptions
        });
    </script>
@endsection