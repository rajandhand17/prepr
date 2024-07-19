@extends('maestro.layouts.default')
@section('title', 'Create Rank')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Create Rank</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Create Rank</li>
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
              <h3 class="card-title">Create Rank</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                  {!!Form::open(array('method'=>'POST','route'=>['ranks.store'] ,'files'=>true))!!}
                    <div class="row">
                      @if($languages->count() > 0)
                          @foreach($languages as $single)
                              @php
                                  if ($single->iso == 'en') {
                                      $lableName = 'English Rank Title';
                                      $inputName = 'title';
                                  } else {
                                        $columName = $single->iso;
                                        $lableName = $single->name . ' Rank Title';
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
                                    {!! Form::text($inputName,null, ['class' => 'form-control','required' => 'required']) !!}
                                </div>
                              </div>
                          @endforeach

                          @foreach($languages as $single)
                              @php
                                  if ($single->iso == 'en') {
                                      $lableName = 'English Description';
                                      $inputName = 'description';
                                  } else {
                                        $columName = $single->iso;
                                        $lableName = $single->name . ' Description';
                                        if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                                            $columName = str_replace(' ', '_', $columName);
                                        }
                                        if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                                            $columName = str_replace('-', '_', $columName);
                                        }

                                      $inputName = $columName . '_description';
                                    }
                              @endphp
                              <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label($inputName, $lableName, ['class' => 'control-label']) !!}
                                    {!! Form::textarea($inputName,null, ['class' => 'form-control','rows'=>'6','required' => 'required']) !!}
                                </div>
                              </div>
                          @endforeach
                      @endif
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                          <div class="form-group {{($errors->has('status')) ? 'has-error' : ''}}">
                              {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                              {!! Form::select('status', $status, old('status'), ['class' => 'form-control']) !!}
                              <span class="help-block">{{ $errors->first('status')}}</span>
                          </div>
                        </div>
                        <div class="col-md-3">
                          <div class="form-group {{($errors->has('point')) ? 'has-error' : ''}}">
                              {!! Form::label('point', 'Point', ['class' => 'control-label']) !!}
                              {!! Form::number('point',null, ['class' => 'form-control incentive_point','min'=>'0','required' => 'required']) !!}
                              <span class="help-block">{{ $errors->first('point')}}</span>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group {{($errors->has('image')) ? 'has-error' : ''}}">
                            {!! Form::label('image', 'Image', ['class' => 'control-label']) !!}</br>
                            {!! Form::file('image', ['class' => 'form-control']) !!}
                            <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('image')}}</span>
                          </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a class="btn btn-danger mr-1" href="{{ route('ranks.index') }}"><i class="icon-cross2"></i> Cancel</a>
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
