@extends('adminlte.default')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard v3</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        {{--<div class="row">
            <div class="col-md-3">
                <div class="card bg-danger-gradient">
                    <div class="card-body">
                        <div class="col-6">
                            <canvas id="expiredUsers" width="120" height="80"></canvas>
                        </div>
                        <div class="col-6"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning-gradient">
                    <div class="card-body">
                        <h5 class="card-title">Special title treatment</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success-gradient">
                    <div class="card-body">
                        <h5 class="card-title">Special title treatment</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info-gradient">
                    <div class="card-body">
                        <h5 class="card-title">Special title treatment</h5>
                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
        </div>--}}
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Utilization vs target per week</h3>
                            <a href="javascript:void(0);">View Report</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg">99%</span>
                                <span>MTD Utilization</span>
                            </p>
                            {{--<p class="ml-auto d-flex flex-column text-right">
                                <span class="text-success">
                                    <i class="fa fa-arrow-up"></i> 12.5%
                                </span>
                                <span class="text-muted">Since last week</span>
                            </p>--}}
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <canvas id="visitors-chart" height="200"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2">
                                <i class="fa fa-circle text-primary"></i> Target billable hours
                            </span>

                            <span>
                                <i class="fa fa-square" style="color:#20c997"></i> Actual Hours
                            </span>
                        </div>
                    </div>
                </div>
                <!-- /.card -->

                <div class="card">
                    <div class="card-header no-border">
                        <h3 class="card-title">Assignments</h3>
                        <div class="card-tools">
                            <a href="#" class="btn btn-tool btn-sm">
                                <i class="fa fa-download"></i>
                            </a>
                            <a href="#" class="btn btn-tool btn-sm">
                                <i class="fa fa-bars"></i>
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-valign-middle">
                            <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Value</th>
                                <th>YTD</th>
                                <th>More</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    ABB
                                </td>
                                <td>R3.5M</td>
                                <td>
                                    <small class="text-success mr-1">
                                        <i class="fa fa-arrow-up"></i>
                                        12%
                                    </small>
                                </td>
                                <td>
                                    <a href="#" class="text-muted">
                                        <i class="fa fa-search"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    SAP
                                </td>
                                <td>R2.7M</td>
                                <td>
                                    <small class="text-success mr-1">
                                        <i class="fa fa-arrow-up"></i>
                                        0.5%
                                    </small>
                                </td>
                                <td>
                                    <a href="#" class="text-muted">
                                        <i class="fa fa-search"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    RW
                                </td>
                                <td>R1.9M</td>
                                <td>
                                    <small class="text-success mr-1">
                                        <i class="fa fa-arrow-up"></i>
                                        3%
                                    </small>
                                </td>
                                <td>
                                    <a href="#" class="text-muted">
                                        <i class="fa fa-search"></i>
                                    </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col-md-6 -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Sales Value</h3>
                            <a href="javascript:void(0);">View Report</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg">YTD%</span>
                                {{--<span>Sales Over Time</span>--}}
                            </p>
                            <p class="ml-auto d-flex flex-column text-right">
                                <span class="text-bold text-lg">MTD%</span>
                                {{--<span class="text-muted">MTD</span>--}}
                            </p>
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <canvas id="sales-chart" height="200"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2">
                                <i class="fa fa-square text-primary"></i> This Year
                            </span>

                            <span class="mr-2">
                                <i class="fa fa-square" style="color: #ced4da"></i> Last Year
                            </span>

                            <span>
                                <i class="fa fa-square" style="color: #9954BB"></i> Last Last Year
                            </span>
                        </div>
                    </div>
                </div>
                <!-- /.card -->

                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Cashflow</h3>
                            <a href="javascript:void(0);">View Report</a>
                        </div>
                    </div>
                    <div class="card-body">
                    {{--<div class="d-flex">
                        <p class="d-flex flex-column">
                            <span class="text-bold text-lg">$18,230.00</span>
                            <span>Sales Over Time</span>
                        </p>
                        <p class="ml-auto d-flex flex-column text-right">
                            <span class="text-success">
                                <i class="fa fa-arrow-up"></i> 33.1%
                            </span>
                            <span class="text-muted">Since last month</span>
                        </p>
                    </div>--}}
                    <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <canvas id="util-chart" height="200"></canvas>
                        </div>

                        {{--<div class="d-flex flex-row justify-content-end">
                            <span class="mr-2">
                                <i class="fa fa-square text-primary"></i> Year 1
                            </span>

                            <span>
                                <i class="fa fa-square text-gray"></i> Year 2
                            </span>
                        </div>--}}
                    </div>
                </div>
            </div>
            <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</div>
<!-- /.content -->
@endsection
@section('extra-js')
<script src="{!! asset('adminlte/plugins/chart.js/Chart.min.js') !!}"></script>
<script src="{!! asset('adminlte/dist/js/pages/dashboard3.js') !!}"></script>
@endsection