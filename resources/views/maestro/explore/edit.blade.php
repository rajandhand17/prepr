@extends('maestro.layouts.default')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Edit </h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('explore.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Edit</li>
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
              <h3 class="card-title">Edit </h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                {!!Form::model($component,array('method'=>'PUT','files'=>true,'route'=>array('explore.update',$component->id)))!!}
                    @include('maestro.explore.form')
                    <div class="form-actions mt-10">
                    
                        {!!Form::submit('Save',array('class'=>'btn btn-primary mr-10'))!!}
                        <a class="btn btn-danger mr-1" href="{{ route('explore.index') }}">
                            Cancel
                        </a>
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
    
@endsection