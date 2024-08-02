@extends('maestro.layouts.default')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Role : @if(!empty($role->display_name)) {{$role->display_name}} @else {{$role->name}} @endif</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('role.index') }}">Home</a></li>
                <li class="breadcrumb-item active">@if(!empty($role->display_name)) {{$role->display_name}} @else {{$role->name}} @endif Role & Permissions</li>
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
              <h3 class="card-title">All Permissions For <b>@if(!empty($role->display_name)) {{$role->display_name}} @else {{$role->name}} @endif </b> Role</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                  {!!Form::model($role,array('method'=>'PUT','route'=>array('role.update',$role->id)))!!}
                  <input type="hidden" name="name" value="{{$role->name}}">
                  <div class="clearfix"></div>
                    <div class="col-md-12 permission {{($errors->has('roles')) ? 'has-error' : ''}}">
                      @php
                          $chunks = $permissions->chunk(ceil($permissions->count() / 4));
                      @endphp
                        <div class="row">
                          @if(!empty($chunks))
                            @foreach($chunks as $chunk)
                                <div class="col-sm-3">
                                    @foreach($chunk as $permission)
                                        <div class="form-group">
                                            <label class="text-uppercase">
                                                <input type="checkbox" name="permission[]" @if(isset($role) && in_array($permission->id, $role_permission)) checked @endif value="{{ $permission->id }}">
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
                            <hr style="margin-top:20px; margin-bottom: 10px" />
                            <br/>
                              {!!Form::submit('update',array('class'=>'btn btn-success mr-10'))!!}
                              <a class="btn btn-danger mr-1" href="{{ route('role.index') }}">
                                  <i class="icon-cross2"></i> Cancel
                              </a>
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
    
@endsection