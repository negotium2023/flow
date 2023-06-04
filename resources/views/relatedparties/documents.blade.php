@extends('relatedparties.show')

@section('tab-content2')
    <div class="col-lg-12">

        {{--<a href="{{route('documents.create',['client'=>$related_party->id])}}" class="btn btn-outline-primary btn-sm"><i class="fa fa-plus"></i> Document</a>--}}

        <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm table-hover">
                <thead class="btn-dark">
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
                @forelse($related_party->documents as $document)
                    <tr>
                        <td><a href="{{route('document',['q'=>$document->file])}}" target="_blank">{{$document->name}}</a></td>
                        <td>{{$document->type()}}</td>
                        <td>{{$document->size()}}</td>
                        <td><a href="{{route('profile',$document->user)}}" title="{{$document->user->name()}}"><img src="{{route('avatar',['q'=>$document->user->avatar])}}" class="blackboard-avatar blackboard-avatar-inline" alt="{{$document->user->name()}}"/></a></td>
                        <td>{{$document->created_at->diffForHumans()}}</td>
                        <td class="last">
                            <a href="{{route('documents.edit',$document)}}" class="btn btn-success btn-sm">Edit</a>
                            {{ Form::open(['id' => 'documentDelete','method' => 'DELETE','route' => ['documents.destroy','id'=>$document->id,'client_id' =>$document->client_id],'style'=>'display:inline']) }}
                            <a href="#" class="delete deleteDoc btn btn-danger btn-sm">Delete</a>
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
@endsection