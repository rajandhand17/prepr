@extends('maestro.layouts.default')
@section('title', 'View Pre Built Achievement')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>View Pre Built Achievement</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Pre Built Achievement Details</li>
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
              <h3 class="card-title">Pre Built Achievement Details</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="title">Achievement Title</label>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <P>{{ $achievement->title ?? '' }} </P>
                                </div>
                            </div>
                        </div>
                    <hr>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="first_name">Achievement Points</label>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <P>{{ $achievement->points ?? '' }} </P>
                                </div>
                            </div>
                        </div>
                    <hr>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="first_name">Achievement Components</label>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <P>{{ ucwords(str_replace('_', ' ', $achievement->component_type)) ?? '' }} </P>
                                </div>
                            </div>
                        </div>
                    <hr>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="first_name">Achievement Image</label>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <img src='{{ $achievement->achievement_image }}' width='60px' onerror=this.onerror=null;this.src="asset('no-img.jpg')">
                                </div>
                            </div>
                        </div>
                    <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <a class="btn btn-danger mr-1" href="{{ route('pre-built-achievement.index') }}"><i class="icon-cross2"></i> Back</a>
                                </div>
                            </div>
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
