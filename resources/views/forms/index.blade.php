@extends('flow.default')

@section('title') CRM @endsection

@section('header')
    <div class="container-fluid container-title">
        <h3>@yield('title')</h3>
        <div class="nav-btn-group">
            <form autocomplete="off">
                <div class="form-row">
                        <div class="form-group" style="display: inline-block">
                            <div class="input-group input-group-sm">
                                {{Form::search('q',old('query'),['class'=>'form-control form-control-sm search','placeholder'=>'Search...'])}}
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-sm btn-default" style="line-height: 1.35rem !important;"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    <div class="ml-2 mt-2">
                        <div class="btn-group">
                            <a href="{{route('forms.create')}}" class="btn btn-sm btn-primary btn-sm">CRM</a>
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
                            <th>Name</th>
                            <th>Created</th>
                            <th>Modified</th>
                            <th class="last">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($forms as $form)
                            <tr>
                                <td>{{$form->name}}</td>
                                <td>{{$form->created_at->diffForHumans()}}</td>
                                <td>{{$form->updated_at->diffForHumans()}}</td>
                                <td class="last">
                                    <a href="{{route('forms.show',$form)}}" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                                    @if(auth()->user()->can('admin'))
                                        <a href="{{route('forms.edit',$form)}}" class="btn btn-sm btn-success"><i class="fa fa-pencil-alt"></i></a>
                                        {{Form::open(['url' => route('forms.destroy',['form' => $form,'formid' => $form]), 'method' => 'delete', 'class' => 'd-inline'])}}
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
                                        {{Form::close()}}
                                    @else
                                        <button type="button" class="btn btn-sm btn-primary disabled" disabled title="You do not have permission to do that"><i class="fa fa-pencil"></i></button>
                                        <button type="button" class="btn btn-sm btn-danger disabled" disabled title="You do not have permission to do that"><i class="fa fa-trash"></i></button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center"><small class="text-muted">No Processes match those criteria.</small></td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            </div>
        </div>
    </div>
@endsection
