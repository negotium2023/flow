@extends('flow.default')

@section('title') Documents @endsection

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
                        <a href="{{route('documents.create')}}" class="btn btn-primary btn-sm float-right">Add Document</a>
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
                            <th>Type</th>
                            <th>Size</th>
                            <th>Uploader</th>
                            <th>Added</th>
                            <th class="last">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($documents as $document)
                            <tr>
                                <td><a href="{{route('document',['q'=>$document->file])}}" target="_blank" download="{{$document->name}}.{{$document->type()}}">{{$document->name}}</a></td>
                                <td>{{$document->type()}}</td>
                                <td>{{$document->size()}}</td>
                                <td><a href="{{route('profile',$document->user)}}" title="{{$document->user->name()}}"><img src="{{route('avatar',['q'=>$document->user->avatar])}}" class="blackboard-avatar blackboard-avatar-inline" alt="{{$document->user->name()}}"/></a></td>
                                <td>{{$document->created_at->diffForHumans()}}</td>
                                <td class="last">
                                    <a href="{{route('documents.edit',$document)}}" class="btn btn-success btn-sm"><i class="fas fa-pencil-alt"></i></a>
                                    {{ Form::open(['method' => 'DELETE','route' => ['documents.destroy','id'=>$document,'client_id' => ($document->client_id != null ? $document->client_id : 0),'process_id'=>0,'step_id'=>0],'style'=>'display:inline']) }}
                                    <a href="#" class="delete deleteDoc btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                                    {{Form::close() }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">No documents match those criteria.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
