@extends('maestro.layouts.default')
@section('title', 'Edit Resource Module')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Resource Module</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
          <li class="breadcrumb-item active">Edit Resource Module</li>
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
        <h3 class="card-title">Edit Resource Module</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        {!!Form::model($resourceModule,array('method'=>'PUT','files'=>true,'id'=>"fileupload",'route'=>array('resource-module.update',$resourceModule->id)))!!}
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
              {!! Form::select('language', $languages, $resourceModule->language, ['class' => 'form-control','id' => 'languageId','disabled' => 'disabled']) !!}  
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
            {{ Form::select('organization_id', $organization, $resourceModule->organization_id, ['class' => 'form-control','id' => 'organisationId']) }}
            <span class="help-block">{{ $errors->first('organization_id')}}</span>
          </div>
        </div>
      </div>
        <div class="row">
          <div class="col-md-3">
            <div class="form-group {{($errors->has('privacy')) ? 'has-error' : ''}}">
              {!! Form::label('privacy', 'Privacy', ['class' => 'control-label']) !!}
              {!! Form::select('privacy', ['0' => 'Not available globally', '1' => 'Available globally'], $resourceModule->privacy, ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('privacy')}}</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group {{($errors->has('status')) ? 'has-error' : ''}}">
              {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
              {!! Form::select('status', ['0' => 'Draft', '1' => 'Published', '2' => 'Archive'], $resourceModule->status, ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('status')}}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('user_id')) ? 'has-error' : ''}}">
              {!! Form::label('user_id', 'User', ['class' => 'control-label ']) !!}
              {!! Form::select('user_id', $user ?? [],$resourceModule->user_id, ['class' => 'form-control select2', 'id'=>'user_id']) !!}
              <span class="help-block">{{ $errors->first('user_id')}}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group {{($errors->has('description')) ? 'has-error' : ''}}">
              {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
              {!! Form::textarea('description',$resourceModule->description, ['class' => 'form-control','rows'=>'6','required' => 'required']) !!}
              <span class="help-block">{{ $errors->first('description')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Update</button>
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
      getUsers();
  });

  $("#languageId").change(function () {
      var language = $('#languageId').val();
      $('#organisationId').empty();
      getOrganizations(language);
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
  
  function getUsers(){
      $('#user_id').select2({      
          placeholder: "Select User",
          ajax: {
              url: '{{ route('getUsers') }}',
              type: 'GET',
              dataType: 'json',
              data: function (params) {
                  return {
                      search: params.term,
                  };
              },
              processResults: function (data) {
                  if(data.status == 'fail'){
                    $('#user_id').select2("close");
                    $('.user_error').show();
                    $('.user_error').html(data.message);
                  } else {
                      $('.user_error').hide();
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