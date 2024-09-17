@extends('maestro.layouts.default')
@section('title', 'Edit Achievement')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Edit Pre Built Achievement</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Edit Achievement</li>
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
              <h3 class="card-title">Edit Achievement</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                  {!!Form::model($achievement,array('method'=>'PUT','files'=>true,'route'=>array('pre-built-achievement.update',$achievement->id),'files'=>true))!!}
                    <div class="row">
                      @if($languages->count() > 0)
                          @foreach($languages as $single)
                            @php
                                $titleColumName = \App\Helpers\UtilityHelper::getColumName($single->iso, 'title');
                                $titleLableName = \App\Helpers\UtilityHelper::getLabelName($single->name, 'Achievement Title');
                            @endphp
                              <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label($titleColumName, $titleLableName, ['class' => 'control-label']) !!}
                                    {!! Form::text($titleColumName,$achievement->$titleColumName, ['class' => 'form-control','required' => 'required']) !!}
                                </div>
                              </div>
                          @endforeach
                      @endif
                    </div>
                    <div class="row">
                      @if(empty($achievement->getRawOriginal('achievement_image')))
                        @php $notExist = 12;  $exist = 0; @endphp
                      @else 
                        @php $notExist = 10;  $exist = 2; @endphp
                      @endif
                      <div class="col-md-{{$notExist}}">
                        <div class="form-group {{($errors->has('image')) ? 'has-error' : ''}}">
                          {!! Form::label('image', 'Image', ['class' => 'control-label']) !!}</br>
                          {!! Form::file('image', ['class' => 'form-control']) !!}
                          <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('image')}}</span>
                        </div>
                      </div>
                      @if($exist > 0)
                        <div class="col-md-{{$exist}}" style="text-align: center; padding-top: 34px;">
                          <img src='{{$achievement->achievement_image}}' width='50px'>
                        </div>
                      @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                          <div class="form-group {{($errors->has('points')) ? 'has-error' : ''}}">
                              {!! Form::label('points', 'Points', ['class' => 'control-label']) !!}
                              {!! Form::number('points',$achievement->points, ['class' => 'form-control incentive_points','min'=>'0','required' => 'required']) !!}
                              <span class="help-block">{{ $errors->first('points')}}</span>
                          </div>
                        </div>

                        <div class="col-md-6">
                          <div class="form-group {{($errors->has('status')) ? 'has-error' : ''}}">
                              {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                              {!! Form::select('status', ['1' => 'Active', '0' => 'DeActive'], $achievement->status, ['class' => 'form-control']) !!}
                              <span class="help-block">{{ $errors->first('status')}}</span>
                          </div>
                        </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                          <b>Enable for the following components</b><br>
                          Achievement type is editable for Challenge only, for all other components, the default type is participation achievement.
                          <div class="enamble-following-heading" style="padding: 10px 0px 0px 0px;">
                            @php
                              $challenge = '';
                              $challengePath = '';
                              $lab = '';
                              $labProgram = '';
                              $resourceGroup = '';
                              $learningPath = '';
                              $compnentType = explode(',', $achievement->component_type);

                              if (!empty($compnentType)) {
                                if (in_array('challenge', $compnentType)) {
                                    $challenge = 'checked';
                                }

                                if (in_array('challenge_path', $compnentType)) {
                                    $challengePath = 'checked';
                                }

                                if (in_array('lab', $compnentType)) {
                                    $lab = 'checked';
                                }

                                if (in_array('lab_program', $compnentType)) {
                                    $labProgram = 'checked';
                                }

                                if (in_array('resource_group', $compnentType)) {
                                    $resourceGroup = 'checked';
                                }

                                if (in_array('learning_path', $compnentType)) {
                                    $learningPath = 'checked';
                                }
                              }
                            @endphp
                            <div>
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="challenge" name="challenge" {{ $challenge }} onclick="enableDesableRedio()"> 
                                <label for="challenge">Challenge</label>
                              </div>
                            </div>
                            <div style="padding-left:30px">
                              <b style="padding-right: 18px; padding-left: 18px;">Achivement Type:</b>
                              
                              <div class="radio radio-success d-inline" style="padding: 5px 5px 5px 0px;">
                                  <input type="hidden" value="{{ $achievement->achievement_type }}" id="isCheckEnable">
                                  <input type="radio" id="achievement_type_participation" value="participation" 
                                         class="achievement_type enable-disable" name="achievement_type" {{ $achievement->achievement_type == '1' ? 'checked' : '' }}>
                                  <label for="achievement_type_participation">Participation Achievement</label>
                              </div>
              
                              <div class="radio radio-success d-inline">
                                  <input type="radio" id="achievement_type_winner" value="winner" 
                                         class="achievement_type enable-disable" name="achievement_type" {{ $achievement->achievement_type == '2' ? 'checked' : '' }}>
                                  <label for="achievement_type_winner">Winner Achievement</label>
                              </div>
                            </div>

                            <div style="padding: 5px 5px 5px 0px;">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="challenge_path" name="challenge_path" {{ $challengePath }}> 
                                <label for="challenge_path">Challenge Path</label>
                              </div>
                            </div>
                            <div style="padding: 5px 5px 5px 0px;">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="lab" name="lab" {{ $lab }}> 
                                <label for="lab">lab</label>
                              </div>

                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="lab_program" name="lab_program" {{ $labProgram }}> 
                                <label for="lab_program">Lab Program</label>
                              </div>
                            </div>

                            <div style="padding: 5px 5px 5px 0px;">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="resource_group" name="resource_group" {{ $resourceGroup }}> 
                                <label for="resource_group">Resource Group </label>
                              </div>
                            </div>

                            <div style="padding: 5px 5px 2% 0px;">
                              <div class="icheck-primary d-inline">
                                <input type="checkbox" id="learning_path" name="learning_path" {{ $learningPath }}> 
                                <label for="learning_path">Learning Path </label>
                              </div>
                            </div>
                          </div>
                          </div>
                      </div>

                      <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a class="btn btn-danger mr-1" href="{{ route('pre-built-achievement.index') }}"><i class="icon-cross2"></i> Cancel</a>
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
    var challengeCheck = '{{ $challenge }}';
    if(challengeCheck == ''){
      $('.enable-disable').attr("disabled",true);
    }
    function enableDesableRedio() {
      if ($('#challenge').is(":checked"))
      {
        $('.enable-disable').attr("disabled",false);
        $("#achievement_type_participation").prop('checked', true);
      } else {
        $('.enable-disable').attr("disabled",true);
        $("#achievement_type_winner,#achievement_type_participation").prop('checked', false);
      }
    }
  </script>
@endsection
