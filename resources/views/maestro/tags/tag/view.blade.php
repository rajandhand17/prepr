@extends('maestro.layouts.default')
@section('title', 'View Tag')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Tag Details</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Tag Details</li>
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
              <h3 class="card-title">Tag Details</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                <div class="table-responsive">
                <table class="table table-bordered m-0">
                                                        <tbody>
                                                        <tr>
                                                            <td style="width: 25%;"><code>Tag Name</code></td>
                                                            <td>{{$tag->title}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 25%;"><code>Category</code></td>
                                                            <td>{{$tag->components}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="width: 25%;"><code>Tag Image</code></td>
                                                            <td><img src="{{asset($tag->tag_image)}}" onerror="this.onerror=null;this.src='{{config(('site-settings.aws_url').'public/front/img/no-img.jpg')}}' " width='100px'
                                                                     height='100px'></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
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
