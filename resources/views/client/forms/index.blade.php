@extends('client.show')

@section('tab-content')
    <div class="col-lg-12">

        <a href="{{route('forms.uploadforms',['client'=>$client->id])}}" class="btn btn-outline-primary btn-sm"><i class="fa fa-plus"></i> Upload Form</a>

        <div class="table-responsive mt-3">
            <table class="table table-bordered table-sm table-hover">
                <thead class="btn-dark">
                <tr>
                    <th>Name</th>
                    <th>Form Type</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Uploader</th>
                    <th>Signed</th>
                    <th>Signed Date</th>
                    <th>Added</th>
                    <th class="last">Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($list as $crf)
                    <tr>

                        <td><a href="{{route('crf_client',['q'=>$crf->file])}}" target="_blank">{{$crf->name}}</a></td>
                        <td>{{$crf->form_type}}</td>
                        <td>{{$crf->type()}}</td>
                        <td>{{$crf->size()}}</td>
                        <td><a href="{{route('profile',$crf->user)}}" title="{{$crf->user->name()}}"><img src="{{route('avatar',['q'=>$crf->user->avatar])}}" class="blackboard-avatar blackboard-avatar-inline" alt="{{$crf->user->name()}}"/></a></td>
                        <td>{{($crf->signed && $crf->signed != null ? ($crf->signed == 1 ? 'Yes' : 'No') : '')}}</td>
                        <td>{{($crf->signed_date && $crf->signed_date != null ? \Illuminate\Support\Carbon::parse($crf->signed_date)->format('Y-m-d') : '')}}</td>
                        <td>{{$crf->created_at->diffForHumans()}}</td>
                        <td class="last"><a href="{{route('forms.editforms',['clientid'=>$client->id,'formid'=>$crf->id])}}" class="btn btn-success btn-sm">Edit</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center">No forms match those criteria.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>

        </div>
    </div>
@endsection