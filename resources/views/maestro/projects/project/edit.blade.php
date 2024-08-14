@extends('maestro.layouts.default')
@section('title', 'Edit Project')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Project</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Home</a></li>
          <li class="breadcrumb-item active">Edit Project</li>
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
        <h3 class="card-title">Edit Project</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        {!!Form::model($project,array('method'=>'PUT','files'=>true,'route'=>array('projects.update',$project->id)))!!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('title')) ? 'has-error' : ''}}">
              {!! Form::label('title', 'Project Title', ['class' => 'control-label']) !!}
              {!! Form::text('title',$project->title, ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('title')}}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('user_id')) ? 'has-error' : ''}}">
              {!! Form::label('user_id', 'Project User', ['class' => 'control-label']) !!}
              {!! Form::select('user_id', $projectData['user'] ?? [], $project->user_id, ['class' => 'form-control','id'=>'userId']) !!}
              <span class="help-block">{{ $errors->first('user_id')}}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('challenge_id')) ? 'has-error' : ''}}">
              {!! Form::label('challenge_id', 'Project Challenge', ['class' => 'control-label']) !!}
              {{ Form::select('challenge_id', $projectData['project_challenge'] ?? [], $project->challenge_id, ['class' => 'form-control select2bs4','id' => 'associativeChallenge','required' => 'required']) }}
              <span style="color: #ea6c41 !important;" class="help-block lab_error">{{ $errors->first('challenge_id')}}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('lab_id')) ? 'has-error' : ''}}">
              {!! Form::label('lab_id', 'Project lab', ['class' => 'control-label']) !!}
              {{ Form::select('lab_id', $projectData['project_lab'] ?? [], $project->lab_id, ['class' => 'form-control select2bs4','id' => 'associativeLab']) }}
              <span style="color: #ea6c41 !important;" class="help-block lab_error">{{ $errors->first('lab_id')}}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('stage')) ? 'has-error' : ''}}">
              {!! Form::label('stage', 'Project Stage', ['class' => 'control-label']) !!}
              {!! Form::select('stage', $projectData['project_stage'] ?? [], $project->stage_id, ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('stage')}}</span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group {{($errors->has('type')) ? 'has-error' : ''}}">
              {!! Form::label('type', 'Project Type', ['class' => 'control-label']) !!}
              {!! Form::select('type', $projectData['project_type'] ?? [], $project->type_id, ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('type')}}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('status')) ? 'has-error' : ''}}">
              {!! Form::label('status', 'Project Status', ['class' => 'control-label']) !!}
              {!! Form::select('status', $projectData['project_status'] ?? [], $project->status_id, ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('status')}}</span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group {{($errors->has('industry')) ? 'has-error' : ''}}">
              {!! Form::label('industry', 'Project Industry', ['class' => 'control-label']) !!}
              {!! Form::select('industry', $projectData['project_industry'] ?? [], $project->industry_id, ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('industry')}}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('verticals')) ? 'has-error' : ''}}">
              {!! Form::label('verticals', 'Project Verticals', ['class' => 'control-label']) !!}
              {!! Form::select('verticals', $projectData['project_verticals'] ?? [], $project->vertical_id, ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('verticals')}}</span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group {{($errors->has('category')) ? 'has-error' : ''}}">
              {!! Form::label('category', 'Project Category', ['class' => 'control-label']) !!}
              {!! Form::select('category', $projectData['project_category'] ?? [], $project->category_id, ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('category')}}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <div class="form-group {{($errors->has('image')) ? 'has-error' : ''}}">
              {!! Form::label('image', 'Project Image', ['class' => 'control-label']) !!}</br>
              {!! Form::file('image') !!}
              <span class="help-block">{{ $errors->first('image')}}</span>
            </div>
          </div>

          <div class="col-md-3">
            <div class="form-group {{($errors->has('project_files')) ? 'has-error' : ''}}">
              {!! Form::label('project_files', 'Project Files', ['class' => 'control-label']) !!}</br>
              {!! Form::file('project_files[]', ['multiple'=>true]) !!}
              <span class="help-block">{{ $errors->first('project_files')}}</span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group">
              <label>Start Date</label>
              <div class="input-group date" id="reservationdate" data-target-input="nearest">
                <input type="text" class="form-control datetimepicker-input" data-target="#reservationdate" name="date" value="@isset($data){{$data->date}}@endif" />
                <div class="input-group-append" data-target="#reservationdate" data-toggle="datetimepicker">
                  <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('team')) ? 'has-error' : ''}}">
              {!! Form::label('team', 'Team Members', ['class' => 'control-label ']) !!}
              {!! Form::select('team[]',$projectData['selected_member'] ?? [],old('team'), ['class' => 'form-control select2bs4', 'id'=>'teamMemberId','required' => 'required','multiple'=>'multiple']) !!}
              <span style="color: #ea6c41 !important;" class="help-block team_error">{{ $errors->first('team')}}</span>
            </div>
          </div>

          <div class="col-md-6">
            <div class="form-group {{($errors->has('privacy')) ? 'has-error' : ''}}">
              {!! Form::label('privacy', 'Project Privacy', ['class' => 'control-label']) !!}
              {!! Form::select('privacy', $projectData['project_privacy'] ?? [], $project->privacy_id, ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('privacy')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group {{($errors->has('description')) ? 'has-error' : ''}}">
              {!! Form::label('description', 'Project Description', ['class' => 'control-label']) !!}
              {!! Form::textarea('description',$project->description, ['class' => 'form-control','rows'=>'6']) !!}
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
    $(document).ready(function () {
        getUsers();
        getLabs();
        getChallenges();
        getTeamMembers();
    });

    function getUsers(){
        $('#userId').select2({      
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
                      $('#userId').select2("close");
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

    function getTeamMembers(){
        $('#teamMemberId').select2({      
            placeholder: "Select team members",
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
                      $('#teamMemberId').select2("close");
                      $('.team_error').show();
                      $('.team_error').html(data.message);
                    } else {
                        $('.team_error').hide();
                        return {
                          results: data.result
                        };
                    }
                }
            }
        });
    }

    function getLabs(){
      $('#associativeLab').select2({
      placeholder: "Select lab",
      ajax: {
          url: '{{route("getLabsForProject")}}',
              cache: true,
              type: 'GET',
              dataType: 'json',
              data: function (params) {
                  return {
                      search: params.term,
                  };
              },
              processResults: function (data) {
                if(data.status == 'fail'){
                  $('#associativeLab').select2("close");
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

    function getChallenges(language){
      $('#associativeChallenge').select2({
        placeholder: "Select Challenge",
        ajax: {
            url: '{{route("getChallengesForProject")}}',
                cache: true,
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                    };
                },
                processResults: function (data) {
                  if(data.status == 'fail'){
                    $('#associativeChallenge').select2("close");
                    $('.challenge_error').show();
                    $('.challenge_error').html(data.message);
                  } else {
                      $('.challenge_error').hide();
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