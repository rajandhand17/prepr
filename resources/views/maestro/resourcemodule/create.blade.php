@extends('maestro.layouts.default')
@section('title', 'Create Resource Module')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Create Resource Module</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
          <li class="breadcrumb-item active">Create Resource Module</li>
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
        <h3 class="card-title">Create Resource Module</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        {!!Form::open(array('method'=>'POST','route' => ['resource-module.store'],'files'=>true))!!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('title')) ? 'has-error' : ''}}">
              {!! Form::label('title', 'Title', ['class' => 'control-label']) !!}
              {!! Form::text('title',null, ['class' => 'form-control','required' => 'required']) !!}
              <span class="help-block">{{ $errors->first('title')}}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('language')) ? 'has-error' : ''}}">
              {!! Form::label('language', 'Language', ['class' => 'control-label ']) !!}
              {!! Form::select('language', $languages, old('language'), ['class' => 'form-control','id' => 'languageId','required' => 'required']) !!}  
              <span class="help-block">{{ $errors->first('language')}}</span>
            </div>
          </div>
        </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group {{($errors->has('cover_image')) ? 'has-error' : ''}}">
            {!! Form::label('cover_image', 'Cover Image', ['class' => 'control-label']) !!}</br>
            {!! Form::file('cover_image', ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('cover_image')}}</span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group {{($errors->has('organization_id')) ? 'has-error' : ''}}">
            {!! Form::label('organization_id', 'Organization', ['class' => 'control-label ']) !!}
            {{ Form::select('organization_id', $organizations, old('organization_id'), ['class' => 'form-control','required' => 'required']) }}
            <span class="help-block">{{ $errors->first('organization_id')}}</span>
          </div>
        </div>
      </div>

        <div class="row">
          <div class="col-md-3">
            <div class="form-group {{($errors->has('privacy')) ? 'has-error' : ''}}">
              {!! Form::label('privacy', 'Privacy', ['class' => 'control-label']) !!}
              {!! Form::select('privacy', $privacy, old('privacy'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('privacy')}}</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group {{($errors->has('status')) ? 'has-error' : ''}}">
              {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
              {!! Form::select('status', $status, old('status'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('status')}}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('user_id')) ? 'has-error' : ''}}">
              {!! Form::label('user_id', 'User', ['class' => 'control-label ']) !!}
              {!! Form::select('user_id', $users,[], ['class' => 'form-control select2', 'id'=>'user_id','required' => 'required']) !!}
              <span class="help-block">{{ $errors->first('user_id')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group {{($errors->has('description')) ? 'has-error' : ''}}">
              {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
              {!! Form::textarea('description',null, ['class' => 'form-control','rows'=>'6','required' => 'required']) !!}
              <span class="help-block">{{ $errors->first('description')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Submit</button>
              <a class="btn btn-danger mr-1" href="{{ route('resource-module.index') }}"><i class="icon-cross2"></i> Cancel</a>
            </div>
          </div>
        </div>
      </div>
      {!!Form::close()!!}
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

@section('scripts')
<script>
    $(document).ready(function () {
        var language = $('#languageId').val();
        getOrganizations(language);
    });

    $("#languageId").change(function () {
        var language = $('#languageId').val();
        $('#organisationId').empty();
        getOrganizations(language);
    });

    /* This function for select Organization */
    function getOrganizations(language){
        $('#organisationId').select2({          
            placeholder: "Select organization data",
            ajax: {
                url: '{{route('getOrgData')}}',
                data: function (params) {
                    return {
                        search: params.term,
                        language: language
                    };
                },
                processResults: function (data) {
                    if(data.status=='fail'){
                            $('#source_org_error').show();
                            $('#source_org_error').html(data.message);
                    }else{
                        return {
                            results: data.result
                        };
                        $('#source_org_error').hide();
                    }
                }
            }
        });
    }
</script>
@endsection