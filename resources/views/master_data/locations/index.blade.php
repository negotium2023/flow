@extends('flow.default')

@section('title') Locations @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <form autocomplete="off">
                <div class="form-row">
                    <div class="mr-2 mt-3">
                        <div class="btn-group mr-2">
                            <a href="{{route('divisions.create')}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Division</a>
                        </div>
                        <div class="btn-group mr-2">
                            <a href="{{route('regions.create')}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Region</a>
                        </div>
                        <div class="btn-group mr-2">
                            <a href="{{route('areas.create')}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Area</a>
                        </div>
                        <div class="btn-group">
                            <a href="{{route('offices.create')}}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Office</a>
                        </div>
                    </div>
                    <div class="form-group mt-2">
                        <div class="input-group">
                            {{Form::search('q',old('query'),['class'=>'form-control search','placeholder'=>'Search...'])}}
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('content')
    <div class="content-container page-content">
        <div class="row col-md-12 h-100 pr-0">
            @yield('header')
            <div class="container-fluid index-container-content">
                <div class="table-responsive h-100">
                    <table class="table table-bordered table-sm table-hover">
                        <thead>
                        <tr>
                            <th>Division</th>
                            <th>Region</th>
                            <th>Area</th>
                            <th>Office</th>
                            <th class="last">Action</th>
                        </tr>
                        </thead>
                        <tbody class="blackboard-locations">
                        @forelse($divisions as $division)
                            <tr>
                                <td colspan="4">{{$division->name}}</td>
                                <td class="last">
                                    <a href="{{route('divisions.edit',$division)}}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                    <a href="" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @forelse($division->regions->sortBy('name') as $region)
                                <tr>
                                    <td></td>
                                    <td colspan="3">{{$region->name}}</td>
                                    <td class="last">
                                        <a href="{{route('regions.edit',$region)}}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                        <a href="" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                @forelse($region->areas->sortBy('name') as $area)
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td colspan="2">{{$area->name}}</td>
                                        <td class="last">
                                            <a href="{{route('areas.edit',$area)}}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    @forelse($area->offices->sortBy('name') as $office)
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{$office->name}}</td>
                                            <td class="last">
                                                <a href="{{route('offices.edit',$office)}}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                                <a href="" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="100%" class="text-center">No Offices assigned to this area.</td>
                                        </tr>
                                    @endforelse
                                @empty
                                    <tr>
                                        <td colspan="100%" class="text-center">No Areas assigned to this region.</td>
                                    </tr>
                                @endforelse
                            @empty
                                <tr>
                                    <td colspan="100%" class="text-center">No Regions assigned to this division.</td>
                                </tr>

                            @endforelse
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">No locations match those criteria.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
