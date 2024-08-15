@extends('maestro.layouts.default')
@section('title', 'Edit Social Link')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Edit Social Link</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('social-links.index') }}">Home</a></li>
          <li class="breadcrumb-item active">Edit Social Link</li>
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
        <h3 class="card-title">Edit Social Link</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        {!!Form::model($socialLink,array('method'=>'PUT','files'=>true,'route'=>array('social-links.update',$socialLink->id)))!!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('title')) ? 'has-error' : ''}}">
              {!! Form::label('title', 'Social Media Name', ['class' => 'control-label']) !!}
              {!! Form::text('title',$socialLink->title, ['class' => 'form-control','required' => 'required']) !!}
              <span class="help-block">{{ $errors->first('title')}}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('icon')) ? 'has-error' : ''}}">
              {!! Form::label('icon', 'Social Icon', ['class' => 'control-label']) !!}</br>
              {!! Form::file('icon', ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('icon')}}</span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Submit</button>
              <a class="btn btn-danger mr-1" href="{{ route('social-links.index') }}"><i class="icon-cross2"></i> Cancel</a>
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

</script>
@endsection