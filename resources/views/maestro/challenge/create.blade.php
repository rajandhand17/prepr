@extends('maestro.layouts.default')
@section('title', 'Create Challenge')
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
          <li class="breadcrumb-item active">Create Challenge</li>
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
        <h3 class="card-title">Create Challenge</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        {!!Form::open(array('method'=>'POST','route' => ['challenge.store'],'files'=>true))!!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('title')) ? 'has-error' : ''}}">
              {!! Form::label('title', 'Title', ['class' => 'control-label']) !!}
              {!! Form::text('title',null, ['class' => 'form-control','required' => 'required']) !!}
              <span style="color: #ea6c41 !important;" class="help-block title_error">{{ $errors->first('title')}}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('language')) ? 'has-error' : ''}}">
              {!! Form::label('language', 'Language', ['class' => 'control-label ']) !!}
              {!! Form::select('language', $languages, old('language'), ['class' => 'form-control','id' => 'languageId','required' => 'required']) !!}  
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('language')}}</span>
            </div>
          </div>
        </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group {{($errors->has('organization_id')) ? 'has-error' : ''}}">
            {!! Form::label('organization_id', 'Organization', ['class' => 'control-label']) !!}
            {{ Form::select('organization_id', [], old('organization_id'), ['class' => 'form-control select2bs4','required' => 'required','id' =>'organisationId','required' => 'required']) }}
            <span style="color: #ea6c41 !important;" class="help-block org_error">{{ $errors->first('organization_id')}}</span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group {{($errors->has('media')) ? 'has-error' : ''}}">
            {!! Form::label('media', 'Cover Image', ['class' => 'control-label']) !!}</br>
            {!! Form::file('media', ['class' => 'form-control']) !!}
            <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('media')}}</span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group {{($errors->has('category')) ? 'has-error' : ''}}">
            {!! Form::label('category', 'Category', ['class' => 'control-label']) !!}
            {{ Form::select('category', [], old('category'), ['class' => 'form-control select2bs4','id' => 'listCategory','required' => 'required']) }}
            <span style="color: #ea6c41 !important;" class="help-block category_error">{{ $errors->first('category')}}</span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group {{($errors->has('skills')) ? 'has-error' : ''}}">
            {!! Form::label('skills', 'Challenge Skills', ['class' => 'control-label']) !!}
            {{ Form::select('skills', [], old('skills'), ['class' => 'form-control select2bs4','id' => 'challengeSkills','required' => 'required']) }}
            <span style="color: #ea6c41 !important;" class="help-block skill_error">{{ $errors->first('skills')}}</span>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group {{($errors->has('level')) ? 'has-error' : ''}}">
            {!! Form::label('level', 'Challenge Level', ['class' => 'control-label']) !!}
            {{ Form::select('level', [], old('level'), ['class' => 'form-control select2bs4','id' => 'challengeLevels','required' => 'required']) }}
            <span style="color: #ea6c41 !important;" class="help-block level_error">{{ $errors->first('level')}}</span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group {{($errors->has('duration')) ? 'has-error' : ''}}">
            {!! Form::label('duration', 'Challenge Duration', ['class' => 'control-label']) !!}
            {{ Form::select('duration', [], old('duration'), ['class' => 'form-control select2bs4','id' => 'challengeDuration','required' => 'required']) }}
            <span style="color: #ea6c41 !important;" class="help-block duration_error">{{ $errors->first('duration')}}</span>
          </div>
        </div>
      </div>

        <div class="row">
          <div class="col-md-3">
            <div class="form-group {{($errors->has('is_open')) ? 'has-error' : ''}}">
              {!! Form::label('is_open', 'Privacy', ['class' => 'control-label']) !!}
              {!! Form::select('is_open', ['0' => 'Open', '1' => 'Close', '2' => 'Completed'], old('is_open'), ['class' => 'form-control']) !!}
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('is_open')}}</span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group {{($errors->has('status')) ? 'has-error' : ''}}">
              {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
              {!! Form::select('status', ['0' => 'Draft', '1' => 'Published','2' => 'Archive'], old('status'), ['class' => 'form-control']) !!}
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('status')}}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('user_id')) ? 'has-error' : ''}}">
              {!! Form::label('user_id', 'Challenge Creator', ['class' => 'control-label ']) !!}
              {!! Form::select('user_id',[],old('user_id'), ['class' => 'form-control select2bs4', 'id'=>'userId','required' => 'required']) !!}
              <span style="color: #ea6c41 !important;" class="help-block user_error">{{ $errors->first('user_id')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('project_privacy')) ? 'has-error' : ''}}">
              {!! Form::label('project_privacy', 'Privacy of submitted projects', ['class' => 'control-label']) !!}
             {!! Form::select('project_privacy', ['0' => 'Public', '1' => 'Private'], old('project_privacy'), ['class' => 'form-control']) !!}
              <span style="color: #ea6c41 !important;" class="help-block org_error">{{ $errors->first('project_privacy')}}</span>
            </div>
          </div>
  
          <div class="col-md-6">
            <div class="form-group {{($errors->has('published')) ? 'has-error' : ''}}">
              {!! Form::label('published', 'Publish Challenge', ['class' => 'control-label']) !!}
              {!! Form::select('published', ['0' => 'Publish' , '1' => 'Draft'], old('published'),['class' => 'form-control']) !!}
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('published')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('min_rank')) ? 'has-error' : ''}}">
              {!! Form::label('min_rank', 'Minimum Rank', ['class' => 'control-label']) !!}
              {{ Form::select('min_rank', [], old('min_rank'), ['class' => 'form-control select2bs4','id' => 'getMinRanks']) }}
              <span style="color: #ea6c41 !important;" class="help-block min_rank_error">{{ $errors->first('level')}}</span>
            </div>
          </div>
  
          <div class="col-md-6">
            <div class="form-group {{($errors->has('min_points')) ? 'has-error' : ''}}">
              {!! Form::label('min_points', 'Minimum Points', ['class' => 'control-label']) !!}
              {{ Form::select('min_points', ['100' => '0 - 100', '500' => '100-500', '1000' => '500-1000', '2000' => '1000 - 2000', '3000' => '2000 - 3000', '4000' => '3000 - 4000', '10000' => '4000 - 10000'], old('min_points'), ['class' => 'form-control select2bs4','id' => 'challengeMinPoint']) }}
              <span style="color: #ea6c41 !important;" class="help-block min_points_error">{{ $errors->first('min_points')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('associativeLab')) ? 'has-error' : ''}}">
              {!! Form::label('associativeLab', 'Associat Lab', ['class' => 'control-label']) !!}
              {{ Form::select('associativeLab', [], old('associativeLab'), ['class' => 'form-control select2bs4','id' => 'associativeLab','required' => 'required']) }}
              <span style="color: #ea6c41 !important;" class="help-block lab_error">{{ $errors->first('associativeLab')}}</span>
            </div>
          </div>
  
          <div class="col-md-6">
            <div class="form-group {{($errors->has('associativeResourceModule')) ? 'has-error' : ''}}">
              {!! Form::label('associativeResourceModule', 'Resource Module', ['class' => 'control-label']) !!}
              {{ Form::select('associativeResourceModule', [], old('associativeResourceModule'), ['class' => 'form-control select2bs4','id' => 'resourceModule','required' => 'required']) }}
              <span style="color: #ea6c41 !important;" class="help-block resource_module_error">{{ $errors->first('associativeResourceModule')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group {{($errors->has('agreement')) ? 'has-error' : ''}}">
              {!! Form::label('agreement', 'Agreement', ['class' => 'control-label']) !!}
              {!! Form::textarea('agreement',null, ['class' => 'form-control', 'rows'=>'10',"id"=>"agreement"]) !!}
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('agreement')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group {{($errors->has('description')) ? 'has-error' : ''}}">
              {!! Form::label('description', 'Description', ['class' => 'control-label']) !!}
              {!! Form::textarea('description',null, ['class' => 'form-control','rows'=>'6','required' => 'required']) !!}
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('description')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Open call date</label>
                <div class="input-group date" id="open_call_date" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#open_call_date" name="open_call_date" id="open_call_date" />
                    <div class="input-group-append" data-target="#open_call_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('open_call_date_description')) ? 'has-error' : ''}}">
              {!! Form::label('open_call_date_description', 'Open Call Date Description', ['class' => 'control-label']) !!}
              {!! Form::text('open_call_date_description',null, ['class' => 'form-control','required' => 'required']) !!}
              <span style="color: #ea6c41 !important;" class="help-block open_call_error">{{ $errors->first('open_call_date_description')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Last Registration Date</label>
                <div class="input-group date" id="last_call_date" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#last_call_date" name="last_call_date" id="last_call_date" />
                    <div class="input-group-append" data-target="#last_call_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('last_call_date_description')) ? 'has-error' : ''}}">
              {!! Form::label('last_call_date_description', 'Last Registration Date Description', ['class' => 'control-label']) !!}
              {!! Form::text('last_call_date_description',null, ['class' => 'form-control','required' => 'required']) !!}
              <span style="color: #ea6c41 !important;" class="help-block last_call_date_description_error">{{ $errors->first('last_call_date_description')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Application Deadline</label>
                <div class="input-group date" id="application_deadline_date" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#application_deadline_date" name="application_deadline_date" id="application_deadline_date" />
                    <div class="input-group-append" data-target="#application_deadline_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('application_deadline_date_description')) ? 'has-error' : ''}}">
              {!! Form::label('application_deadline_date_description', 'Application Deadline Description', ['class' => 'control-label']) !!}
              {!! Form::text('application_deadline_date_description',null, ['class' => 'form-control','required' => 'required']) !!}
              <span style="color: #ea6c41 !important;" class="help-block application_deadline_date_description_error">{{ $errors->first('application_deadline_date_description')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Submission Deadline</label>
                <div class="input-group date" id="submission_deadline_date" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#submission_deadline_date" name="submission_deadline_date" id="submission_deadline_date" />
                    <div class="input-group-append" data-target="#submission_deadline_date" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('submission_deadline_date_description')) ? 'has-error' : ''}}">
              {!! Form::label('submission_deadline_date_description', 'Submission Deadline Description', ['class' => 'control-label']) !!}
              {!! Form::text('submission_deadline_date_description',null, ['class' => 'form-control','required' => 'required']) !!}
              <span style="color: #ea6c41 !important;" class="help-block submission_deadline_date_description_error">{{ $errors->first('submission_deadline_date_description')}}</span>
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

@section('scripts')
<script>
    $(document).ready(function () {
        var language = $('#languageId').val();
        getOrganizations(language);
        getTags(language);
        getSkills(language);
        getCategories(language);
        getUsers();
        getLabs(language);
        getResourceModule(language);
        getLevels(language);
        getDuration(language);
        challengeMinPoint(language);
    });

    $("#languageId").change(function () {
        var language = $('#languageId').val();
        $('#organisationId').empty();
        $('#listCategory').empty();
        $('#tag').empty();
        $('#challengeSkills').empty();
        $('#challengeLevels').empty();
        $('#challengeDuration').empty();
        $('#associativeLab').empty();
        getOrganizations(language);
        getTags(language);
        getSkills(language);
        getCategories(language);
        getLabs(language);
        getResourceModule(language);
        getLevels(language);
        getDuration(language);
    });

    $("#organisationId").change(function(){
        $('.lab_error').hide();
        $('.resource_module_error').hide();
        $("#resourceModule").empty().trigger('change')
        $('#associativeLab').empty().trigger('change')
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

    /* This function for select categorys */
    function getCategories(language){
        // $("#listCategory").closest(".input-group").siblings(".help-block").text('');
        $('#listCategory').select2({           
            placeholder: "Select category",
            ajax: {
                url: '{{route('getCategories')}}',
                cache: true,
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        language: language,
                        component:'challenge'
                    };
                },
                processResults: function (data) {
                    if(data.status == 'fail'){
                      $('#listCategory').select2("close");
                      $('.category_error').show();
                      $('.category_error').html(data.message);
                    } else {
                        $('.category_error').hide();
                        return {
                          results: data.result
                        };
                    }
                }
            }
        });
    }

    /* This function for select tag */
    function getTags(language){
        $('#tag').select2({
            placeholder: "Select Tag",
            ajax: {
                url: "{{route('getLevels')}}",
                cache: true,
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        language : language
                    };
                },
                processResults: function (data) {
                    if(data.status == 'fail'){
                      $('#tag').select2("close");
                      $('.tag_error').show();
                      $('.tag_error').html(data.message);
                    } else {
                        $('.tag_error').hide();
                        return {
                          results: data.result
                        };
                    }
                }
            }
        });
    }

    // This function get skills
    function getSkills(language){
        $('#challengeSkills').select2({
            placeholder: "Select challenge skill",
            ajax: {
                url: '{{route('getSkills')}}',
                cache: true, 
                width: 'resolve', 
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        language : language
                    };
                },
                processResults: function (data) {
                      if(data.status == 'fail'){
                        $('#challengeSkills').select2("close");
                        $('.skill_error').show();
                        $('.skill_error').html(data.message);
                      } else {
                          $('.skill_error').hide();
                          return {
                            results: data.result
                          };
                      }
                }
            }
        });
    }

    // This function get level
    function getLevels(language){
      $('#challengeLevels').select2({
          placeholder: "Select challenge Level.",
          ajax: {
              url: '{{route('getLevels')}}',
              cache: true, 
              width: 'resolve', 
              type: 'GET',
              dataType: 'json',
              data: function (params) {
                  return {
                      search: params.term,
                      language : language
                  };
              },
              processResults: function (data) {
                    if(data.status == 'fail'){
                      $('#challengeLevels').select2("close");
                      $('.level_error').show();
                      $('.level_error').html(data.message);
                    } else {
                        $('.level_error').hide();
                        return {
                          results: data.result
                        };
                    }
              }
          }
      });
    }
      // This function get duration
    function getDuration(language){
      $('#challengeDuration').select2({
          placeholder: "Select challenge Duration",
          ajax: {
              url: '{{route('getDurations')}}',
              cache: true, 
              width: 'resolve', 
              type: 'GET',
              dataType: 'json',
              data: function (params) {
                  return {
                      search: params.term,
                      language : language
                  };
              },
              processResults: function (data) {
                    if(data.status == 'fail'){
                      $('#challengeDuration').select2("close");
                      $('.duration_error').show();
                      $('.duration_error').html(data.message);
                    } else {
                        $('.duration_error').hide();
                        return {
                          results: data.result
                        };
                    }
              }
          }
      });
    }

        // This function get labs
        function getLabs(language){
          $('#associativeLab').select2({
            placeholder: "Select lab",
            ajax: {
                url: '{{route("getLabs")}}',
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

      // This function get skills
      function getResourceModule(language){
        $('#resourceModule').select2({
            placeholder: "Select resource module",
            ajax: {
                url: '{{route('getResourceModules')}}',
                cache: true, 
                width: 'resolve', 
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
                    if(data.status == 'fail'){
                        $('#resourceModule').select2("close");
                        $('.resource_module_error').show();
                        $('.resource_module_error').html(data.message);
                    } else {
                        $('.resource_module_error').hide();
                        return {
                          results: data.result
                        };
                    }
                }
            }
        });
      }

    /* This function for select min rank */
    function challengeMinPoint(language){
        $('#getMinRanks').select2({      
            placeholder: "Select Minimum Ranks",
            ajax: {
                url: '{{route('getMinRanks')}}',
                cache: true,
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                        language: language,
                    };
                },
                processResults: function (data) {
                    if(data.status == 'fail'){
                      $('#getMinRanks').select2("close");
                      $('.min_ranks_error').show();
                      $('.min_ranks_error').html(data.message);
                    } else {
                        $('.min_ranks_error').hide();
                        return {
                          results: data.result
                        };
                    }
                }
            }
        });
    }

      $('#last_call_date').datetimepicker({
          format: 'L'
      });
      $('#open_call_date').datetimepicker({
          format: 'L'
      });
      $('#application_deadline_date').datetimepicker({
          format: 'L'
      });
      $('#submission_deadline_date').datetimepicker({
          format: 'L'
      });
        $('.deadline, .last_registration_date, .call_date, .application_deadline').blur(function(){
            var callDate = new Date($('#call_date').val());
            var deadline = new Date($(this).val());
            var lastDateToRegister = new Date($('.last_registration_date').val());
            var appDeadline = new Date($('.application_deadline').val());
            var calltoreg = datePop(callDate,lastDateToRegister);
            var calltoappdead = datePop(callDate,appDeadline);
            var regtosub = datePop(lastDateToRegister,deadline);
            var regtoappdead = datePop(lastDateToRegister,appDeadline);
            var appdeadtosub = datePop(appDeadline,deadline);
            var diffDays = datePop(callDate,deadline);

            if($.isNumeric(diffDays)){
                $('.challenge_length').val(diffDays);
                alert(diffDays);
            }

            if(diffDays < 0){
                alert('Invalid Date Selection, Submission deadline must be greater than Open call Date.');
                $('.deadline').val('');
                $('.challenge_length').val('');
            }
            if(calltoreg < 0){
                alert('Invalid Date Selection, Register team deadline must be greater than Open call Date.');
                $('.last_registration_date').val('');
                $('.challenge_length').val('');
            }
            if(calltoappdead < 0){
                alert('Invalid Date Selection, Application deadline must be greater than Open call Date.');
                $('application_deadline').val('');
                $('.challenge_length').val('');
            }
            if(regtosub < 0){
                alert('Invalid Date Selection, Submission deadline must be greater than Register team deadline.');
                $('.deadline').val('');
                $('.challenge_length').val('');
            }
            if(regtoappdead < 0){
                alert('Invalid Date Selection, Application deadline must be greater than Register team deadline.');
                $('.application_deadline').val('');
                $('.challenge_length').val('');
            }
            if(appdeadtosub < 0){
                alert('Invalid Date Selection, Submission deadline must be greater than Application deadline.');
                $('.application_deadline').val('');
                $('.challenge_length').val('');
            }
        });
        function datePop(date1,date2) {
            var one_day=1000*60*60*24;
            var date1_ms = date1.getTime();
            var date2_ms = date2.getTime();
            var difference_ms = date2_ms - date1_ms;
            difference_ms = difference_ms/1000;
            var seconds = Math.floor(difference_ms % 60);
            difference_ms = difference_ms/60;
            var minutes = Math.floor(difference_ms % 60);
            difference_ms = difference_ms/60;
            var hours = Math.floor(difference_ms % 24);
            var days = Math.floor(difference_ms/24);
            return days;
        }
</script>
@endsection