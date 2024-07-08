@extends('maestro.layouts.default')
@section('title', 'Clone Lab')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Create Challenge</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Clone Lab</li>
                </ol>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
            <div class="card-header">
                <h3 class="card-title">Clone Lab</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                {!!Form::open(array('method'=>'POST','route' => ['clone-lab.store'],'files'=>true))!!}
                <h3><b>Clone Lab </b></h3>
                <hr>
                <div class="col-md-4">
                    <div class="form-group {{($errors->has('language')) ? 'has-error' : ''}}">
                        {!! Form::label('language', 'Language', ['class' => 'control-label ']) !!}
                        {!! Form::select('language', $languages, old('language'), ['class' => 'form-control','id' => 'languageId','required' => 'required']) !!}
                        <span class="help-block">{{ $errors->first('language')}}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group {{($errors->has('organization')) ? 'has-error' : ''}}">
                            {!! Form::label('organization', 'Organization', ['class' => 'control-label']) !!}
                            {{ Form::select('organization',$organizations, old('organization'), ['class' => 'form-control select2bs4','required' => 'required','id' =>'organisationId','required' => 'required']) }}
                            <span style="color: #ea6c41 !important;" class="help-block org_error">{{ $errors->first('organization')}}</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('lab', 'Lab', ['class' => 'control-label']) !!}
                            {{ Form::select('lab', [], old('lab'), ['class' => 'form-control select2bs4','id' => 'lab','required' => 'required','multiple' => 'multiple']) }}
                            <span style="color: #ea6c41 !important;" class="help-block lab_error">{{ $errors->first('lab')}}</span>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a class="btn btn-danger mr-1" href="{{ route('clone-lab.index') }}"><i class="icon-cross2"></i> Cancel</a>
                        </div>
                    </div>
                </div>
                {!!Form::close()!!}
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.card-body -->
    </div>
    <!-- /.card -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
@stop
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@section('scripts')


<script>
    @if(Session::has('success'))
        toastr.success("{{ Session::get('success') }}");
    @endif

    @if(Session::has('error'))
        toastr.error("{{ Session::get('error') }}");
    @endif
</script>
<script>
    $(document).ready(function () {
        var language = $('#languageId').val();
        getOrganizations(language);
        getLabs(language);
    });

    $("#languageId").change(function () {
        var language = $('#languageId').val();
        $('#organisationId').empty();
        $('#lab').empty();
        getOrganizations(language);
        getLabs(language);
    });

    $("#organisationId").change(function(){
        $('#lab').empty().trigger('change')
    });

    /* This function for select Organization */
    function getOrganizations(language){
        $('#organisationId').select2({
            placeholder: "Select organization",
            ajax: {
                url: '{{ route('getOrganizations') }}',
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        language: language
                    };
                },
                processResults: function (data) {
                    if(data.status == 'fail'){
                        $('#organisationId').select2("close");
                        $('.org_error').show();
                        $('.org_error').html(data.message);
                    } else {
                        $('.org_error').hide();
                        return {
                            results: data.result
                        };
                    }
                }
            }
        });
    }

    function getLabs(language){
        $('#lab').select2({
            placeholder: "Select lab",
            ajax: {
                url: '{{route("getLabsBasedOnOrganization")}}',
                cache: true,
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        language : language,
                        org_id : $('#organisationId').val(),
                    };
                },
                processResults: function (data) {
                    console.log(data);
                    if(data.status == 'fail'){
                        $('#lab').select2("close");
                        $('.lab_error').show();
                        $('.lab_error').html(data.message);
                    } else {
                        $('.lab_error').hide();
                        return {
                            results: data.result
                        };
                    }
                }
            }
        });
    }

</script>
@endsection
