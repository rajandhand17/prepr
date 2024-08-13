@extends('maestro.layouts.default')
@section('title', 'View User')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Project Details</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Home</a></li>
          <li class="breadcrumb-item active">Project Details</li>
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
        <h3 class="card-title">Project Details</h3>
        <a class="btn btn-danger mr-1" style='float:right' href="{{ route('projects.index') }}"><i
            class="icon-cross2"></i> Back</a>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Project Title</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>{{ ucfirst($project->title) ?? '' }} </P>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Project User</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>@if(isset($project->user_id)){{$project->getUser->username}} @else {{'Admin'}}@endif </P>
            </div>
          </div>
        </div>
        <hr>

        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Project Category</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>{{ $project->getCategory->title ?? ' - ' }}</P>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Project Privacy</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>{{ $project->privacy == '1' ? 'Private' : 'public' }} </P>
            </div>
          </div>
        </div>
        <hr>

        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Project Stage</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>{{ $project->getStage->title ?? ' - ' }}</P>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Project Status</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P> {{ $project->getStatus->title ?? ' - ' }} </P>
            </div>
          </div>
        </div>
        <hr>

        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Project Type</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>@if(isset($project->type_id)) {{$project->getType->title}} @endif </P>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Project Industry</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>@if(isset($project->industry_id)){{$project->getIndustry->title}}@endif </P>
            </div>
          </div>
        </div>
        <hr>

        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Project Verticals</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>@if(isset($project->vertical_id)){{$project->getVertical->title}}@endif </P>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Project Description</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>@if(isset($project->description)){!! $project->description !!}@endif </P>
            </div>
          </div>
        </div>
        <hr>

        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Project Image</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>@if(isset($project->image) && $project->image !="" )
                <img src="{{asset($project->image)}}" width="100" height="100"
                  onerror="this.onerror=null;this.src='{{asset(" front/img/logoNew.png")}}';">
                @endif
              </P>
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              <label for="first_name">Project Files</label>
            </div>
          </div>
          <div class="col-md-10">
            <div class="form-group">
              <P>
                @if(isset($project->getFiles) && count($project->getFiles) > 0)
                @foreach($project->getFiles as $key=>$file)
                <a href="{{asset($file->name)}}" download="Attachment{{$key+1}}">Attachment {{$key+1}}</a>
                &emsp;
                @endforeach
                @endif
              </P>
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