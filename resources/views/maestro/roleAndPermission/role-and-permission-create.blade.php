@extends('maestro.layouts.default')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Create Role & Permissions</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('role.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Role & Permissions</li>
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
              <h3 class="card-title">Create Role & Permissions</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                    {!!Form::open(array('method'=>'POST','route'=>'role.store'))!!}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="display_name">Role Name</label>
                                    <input type="text" name="display_name" class="form-control" id="display_name" placeholder="Please Enter Role Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Role Description</label>
                                    <input type="text" name="description" class="form-control" id="description" placeholder="Please Enter Role Description" required>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr style="margin-top:20px; margin-bottom: 10px" /></br>
                        <div class="row">
                            @php
                                $chunks = $permissions->chunk(ceil($permissions->count() / 4));
                            @endphp
                            @if(!empty($chunks))
                                @foreach($chunks as $chunk)
                                    <div class="col-sm-3">
                                        @foreach($chunk as $permission)
                                            <div class="form-group">
                                                <label class="text-uppercase">
                                                    <input type="checkbox" name="permission[]" value="{{ $permission->id }}">
                                                    {{ $permission->display_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <hr style="margin-top:20px; margin-bottom: 10px" /><br/>
                                {!!Form::submit('create',array('class'=>'btn btn-success mr-10'))!!}
                                <a class="btn btn-danger mr-1" href="{{ route('role.index') }}"><i class="icon-cross2"></i> Cancel </a>
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
    
@endsection