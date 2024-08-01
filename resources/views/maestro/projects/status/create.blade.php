@extends('maestro.layouts.default')
@section('title', 'Create Project Status')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Create Project Status</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('projects-status.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Create Project Status</li>
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
              <h3 class="card-title">Create Project Status</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                  {!!Form::open(array('method'=>'POST','route'=>['projects-status.store']))!!}
                    <div class="row">
                      @if($languages->count() > 0)
                        @foreach($languages as $single)
                          @php
                            $titleColumName = \App\Helpers\UtilityHelper::getColumName($single->iso, 'title');
                            $titleLableName = \App\Helpers\UtilityHelper::getLabelName($single->name, 'Status Name');
                          @endphp
                          <div class="col-md-6">
                            <div class="form-group">
                              {!! Form::label($titleColumName, $titleLableName, ['class' => 'control-label']) !!}
                              {!! Form::text($titleColumName,old($titleColumName), ['class' => 'form-control','required' => 'required'])
                              !!}
                            </div>
                          </div>
                        @endforeach
                      @endif
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                          <div class="form-group {{($errors->has('status')) ? 'has-error' : ''}}">
                              {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                              {!! Form::select('status', ['1' => 'Active', '0' => 'InActive'], old('status'), ['class' => 'form-control']) !!}
                              <span class="help-block">{{ $errors->first('status')}}</span>
                          </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a class="btn btn-danger mr-1" href="{{ route('projects-status.index') }}"><i class="icon-cross2"></i> Cancel</a>
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
