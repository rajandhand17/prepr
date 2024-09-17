@extends('maestro.layouts.default')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Edit Skills</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Edit Skills</li>
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
              <h3 class="card-title">Edit Skills</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                {!!Form::model($data,array('method'=>'PUT','files'=>true,'route'=>array('skills.update',$data->id)))!!}
                   
                @include('maestro.skills.form')
                    <div class="form-actions mt-10">
                        {!!Form::submit('Update',array('class'=>'btn btn-primary mr-10'))!!}
                        <a class="btn btn-danger mr-1" href="{{ route('skills.index') }}">
                            <i class="icon-cross2"></i> Cancel
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