@extends('maestro.layouts.default')
@section('title', 'Lab Details')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Lab Details</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('lab.index') }}">Home</a></li>
          <li class="breadcrumb-item active">Lab Details</li>
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
        <h3 class="card-title">Lab Details</h3>
        <a class="btn btn-danger mr-1" style='float:right' href="{{ route('lab.index') }}"><i
            class="icon-cross2"></i> Back</a>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Lab Title</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>{{ ucfirst($lab->title) ?? '' }} </P>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Lab Creator</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>@if(isset($lab->user_id)){{$lab->getUser->username}} @else {{'Admin'}}@endif </P>
            </div>
          </div>
        </div>
        <hr>

        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Lab Category</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>{{ $lab->getCategory->title ?? ' - ' }}</P>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Lab Privacy</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>{{ $lab->privacy == '1' ? 'Private' : 'public' }} </P>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label for="first_name">Lab Organization</label>
              </div>
            </div>
            <div class="col-md-10">
              <div class="form-group">
                <P>{{ $lab->organization->title ?? ' - ' }} </P>
              </div>
            </div>
          </div>
          <hr>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Lab Description</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>@if(isset($lab->description)){!! $lab->description !!}@endif </P>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-2">
              <div class="form-group">
                <label for="first_name">Lab Image</label>
              </div>
            </div>
            <div class="col-md-10">
              <div class="form-group">
                <P>
                @if($lab->media_type =='image')
                    @if(isset($lab->media) && $lab->media !="" )
                        <img src="{{asset($lab->media)}}" width="100" height="100" onerror="this.onerror=null;this.src='{{asset('front/img/logoNew.png')}}'">
                    @endif
                @endif
                </P>
              </div>
            </div>
          </div>
          <hr>
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