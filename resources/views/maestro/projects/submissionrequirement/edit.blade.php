@extends('maestro.layouts.default')
@section('title', 'Edit Project Submission Requirement')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Edit Project Submission Requirement</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('projects-submission-requirement.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Edit Project Submission Requirement</li>
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
              <h3 class="card-title">Edit Project Submission Requirement</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                  {!!Form::model($submissionRequirement,array('method'=>'PUT','files'=>true,'route'=>array('projects-submission-requirement.update',$submissionRequirement->id),'files'=>true))!!}
                    <div class="row">
                      @if($languages->count() > 0)
                          @foreach($languages as $single)
                              @php
                                  if ($single->iso == 'en') {
                                      $lableName = 'English Project Submission Requirement Title';
                                      $inputName = 'title';
                                  } else {
                                        $columName = $single->iso;
                                        $lableName = $single->name . ' Project Submission Requirement Title';
                                        if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                                            $columName = str_replace(' ', '_', $columName);
                                        }
                                        if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                                            $columName = str_replace('-', '_', $columName);
                                        }

                                      $inputName = $columName . '_title';
                                    }
                              @endphp
                              <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label($inputName, $lableName, ['class' => 'control-label']) !!}
                                    {!! Form::text($inputName,null, ['class' => 'form-control']) !!}
                                </div>
                              </div>
                          @endforeach
                      @endif
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group {{($errors->has('status')) ? 'has-error' : ''}}">
                            {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                            {!! Form::select('status', $status, $submissionRequirement->status, ['class' => 'form-control']) !!}
                            <span class="help-block">{{ $errors->first('status')}}</span>
                        </div>
                      </div>
                  </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a class="btn btn-danger mr-1" href="{{ route('projects-submission-requirement.index') }}"><i class="icon-cross2"></i> Cancel</a>
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
