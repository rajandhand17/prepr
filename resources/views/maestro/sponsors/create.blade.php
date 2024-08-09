@extends('maestro.layouts.default')
@section('title', 'Create Sponsor')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Create Sponsor</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('sponsors.index') }}">Home</a></li>
          <li class="breadcrumb-item active">Create Sponsor</li>
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
        <h3 class="card-title">Create Sponsor</h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        {!!Form::open(array('method'=>'POST','route' => ['sponsors.store'],'files'=>true))!!}
        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('title')) ? 'has-error' : ''}}">
              {!! Form::label('title', 'Sponsor Title', ['class' => 'control-label']) !!}
              {!! Form::text('title',null, ['class' => 'form-control','required' => 'required']) !!}
              <span class="help-block">{{ $errors->first('title')}}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('link')) ? 'has-error' : ''}}">
              {!! Form::label('link', 'Sponsor Link', ['class' => 'control-label']) !!}
              {!! Form::url('link',null, ['class' => 'form-control','required' => 'required']) !!}
              <span class="help-block">{{ $errors->first('link')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group {{($errors->has('image')) ? 'has-error' : ''}}">
              {!! Form::label('image', 'Sponsor Image', ['class' => 'control-label']) !!}</br>
              {!! Form::file('image', ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('image')}}</span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group {{($errors->has('status')) ? 'has-error' : ''}}">
              {!! Form::label('status', 'Sponsor Status', ['class' => 'control-label']) !!}
              {!! Form::select('status', ['1' => 'Active', '0' => 'Not Active'], old('status'), ['class' => 'form-control']) !!}
              <span class="help-block">{{ $errors->first('status')}}</span>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Submit</button>
              <a class="btn btn-danger mr-1" href="{{ route('sponsors.index') }}"><i class="icon-cross2"></i> Cancel</a>
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