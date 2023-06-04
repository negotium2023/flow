@extends('client.show')

@section('tab-content')
    <div class="container-fluid">

@foreach($forms as $result)
            {{Form::open(['url' => route('forms.formupdate',['clientid'=>$client->id,'formid'=>$result->id]), 'method' => 'post','files'=>true])}}
        <div class="form-group mt-3">
            {{Form::label('form_type', 'Form Type')}}
            {{Form::select('form_type',["" => "Select","CRF Form"=>"CRF Form","Other" =>"Other"],$result->form_type,['class'=>'form_type form-control form-control-sm'. ($errors->has('form_type') ? ' is-invalid' : '')])}}
            @foreach($errors->get('form_type') as $error)
                <div class="invalid-feedback">
                    {{$error}}
                </div>
            @endforeach
        </div>

        <div class="form-group mt-3 form-name" @if($result->name == "CRF Form") style="display:none" @endif>
            {{Form::label('name', 'Name')}}
            {{Form::text('name',$result->name,['class'=>'form-name-val form-control form-control-sm'. ($errors->has('name') ? ' is-invalid' : ''),'placeholder'=>$result->name])}}
            @foreach($errors->get('name') as $error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endforeach
        </div>

        <div class="form-group">
            {{Form::label('file', 'Replace File (Optional)')}}
            {{Form::file('file',['class'=>'form-control'. ($errors->has('file') ? ' is-invalid' : ''),'placeholder'=>'File'])}}
            @foreach($errors->get('file') as $error)
                <div class="invalid-feedback">
                    {{ $error }}
                </div>
            @endforeach
        </div>

        @if(request()->has('client'))
            {{Form::hidden('client',request()->input('client'))}}
        @endif

        <div class="form-group">
            <button type="submit" class="btn btn-sm">Save</button>
        </div>
            {{Form::close()}}
@endforeach

    </div>
@endsection
@section('extra-js')
    <script>
        $(document).ready(function() {
            $(".form_type").on("change", function () {
                var ft = $(".form_type").val();
                if (ft == "CRF Form") {
                    $(".form-name").css("display","none");
                } else {
                    $(".form-name").css("display","block");
                    $(".form-name-val").val('');
                }
            })
        })
    </script>
@endsection