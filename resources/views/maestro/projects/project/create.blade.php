@extends('maestro.layouts.default')
@section('title', 'Create Project')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Create Project</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Home</a></li>
          <li class="breadcrumb-item active">Create Project</li>
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
        <h3 class="card-title">Create Project</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        {!!Form::open(array('method'=>'POST','route' => ['projects.store'],'files'=>true))!!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('title')) ? 'has-error' : ''}}">
              {!! Form::label('title', 'Project Title', ['class' => 'control-label']) !!}
              {!! Form::text('title',null, ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('title')}}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('user_id')) ? 'has-error' : ''}}">
              {!! Form::label('user_id', 'Project User', ['class' => 'control-label']) !!}
              {!! Form::select('user_id', $project_user, old('user_id'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('user_id')}}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('challenge_id')) ? 'has-error' : ''}}">
              {!! Form::label('challenge_id', 'Project Challenge', ['class' => 'control-label']) !!}
              {!! Form::select('challenge_id', $project_challenge, old('challenge_id'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('challenge_id')}}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('lab_id')) ? 'has-error' : ''}}">
              {!! Form::label('lab_id', 'Project lab', ['class' => 'control-label']) !!}
              {!! Form::select('lab_id',$project_lab, old('lab_id') ,['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('lab_id')}}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('stage')) ? 'has-error' : ''}}">
              {!! Form::label('stage', 'Project Stage', ['class' => 'control-label']) !!}
              {!! Form::select('stage', $project_stage, old('stage'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('stage')}}</span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group {{($errors->has('type')) ? 'has-error' : ''}}">
              {!! Form::label('type', 'Project Type', ['class' => 'control-label']) !!}
              {!! Form::select('type', $project_type, old('type'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('type')}}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('status')) ? 'has-error' : ''}}">
              {!! Form::label('status', 'Project Status', ['class' => 'control-label']) !!}
              {!! Form::select('status', $project_status, old('status'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('status')}}</span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group {{($errors->has('industry')) ? 'has-error' : ''}}">
              {!! Form::label('industry', 'Project Industry', ['class' => 'control-label']) !!}
              {!! Form::select('industry', $project_industry, old('industry'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('industry')}}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('verticals')) ? 'has-error' : ''}}">
              {!! Form::label('verticals', 'Project Verticals', ['class' => 'control-label']) !!}
              {!! Form::select('verticals', $project_verticals, old('verticals'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('verticals')}}</span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group {{($errors->has('category')) ? 'has-error' : ''}}">
              {!! Form::label('category', 'Project Category', ['class' => 'control-label']) !!}
              {!! Form::select('category', $project_category, old('category'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('category')}}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('image')) ? 'has-error' : ''}}">
              {!! Form::label('image', 'Project Image', ['class' => 'control-label']) !!}</br>
              {!! Form::file('image', ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('image')}}</span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group {{($errors->has('project_files')) ? 'has-error' : ''}}">
              {!! Form::label('project_files', 'Project Files', ['class' => 'control-label']) !!}</br>
              {!! Form::file('project_files[]', ['multiple'=>true,'class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('project_files')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('team')) ? 'has-error' : ''}}">
              {!! Form::label('team', 'Team Members', ['class' => 'control-label ']) !!}
              {!! Form::select('team[]', $project_team, [], ['class' => 'form-control
              select2','multiple'=>'multiple', 'id'=>'projectMembers']) !!}
              <span class="help-block">{{ $errors->first('team')}}</span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group {{($errors->has('privacy')) ? 'has-error' : ''}}">
              {!! Form::label('privacy', 'Project Privacy', ['class' => 'control-label']) !!}
              {!! Form::select('privacy', $project_privacy, old('privacy'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('privacy')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group {{($errors->has('description')) ? 'has-error' : ''}}">
              {!! Form::label('description', 'Project Description', ['class' => 'control-label']) !!}
              {!! Form::textarea('description',null, ['class' => 'form-control','rows'=>'6']) !!}
              <span class="help-block">{{ $errors->first('description')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Submit</button>
              <a class="btn btn-danger mr-1" href="{{ route('projects.index') }}"><i class="icon-cross2"></i> Cancel</a>
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

</script>
@endsection