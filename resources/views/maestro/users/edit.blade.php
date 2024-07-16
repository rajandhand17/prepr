@extends('maestro.layouts.default')
@section('title', 'Edit User')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Edit User</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Edit User</li>
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
              <h3 class="card-title">Edit User</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                    {!!Form::open(array('method'=>'PUT','route' => ['users.update', $user->id],'files'=>'true','role'=>"form"))!!}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="{{ $user->first_name ?? '' }}" id="first_name" placeholder="Enter First Name" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="{{ $user->last_name ?? '' }}" id="last_name" placeholder="Enter Last Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">User Name</label>
                                    <input type="text" name="username" class="form-control" id="username" value="{{ $user->username ?? '' }}" placeholder="Enter User Name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">E-Mail Address</label>
                                    <input type="email" name="email" class="form-control" id="email" value="{{ $user->email ?? '' }}" placeholder="Enter E-Mail Address" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" class="form-control" value="" id="password" placeholder="Enter Password">
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group">
                                <label>Role</label>
                                <select name="roles[]" class="select2" multiple="multiple" data-placeholder="Select a Role" style="width: 100%;" required>
                                  @if(!empty($roles))
                                    @foreach($roles as $key => $role)
                                      <option value="{{ $role->name }}" @selected(in_array($role->name, $selected_role)) >{{ $role->display_name }}</option>
                                    @endforeach
                                  @endif
                                </select>
                              </div>
                            </div>
                          </div>
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
                                                    <input type="checkbox" name="permission[]" @if(isset($permission->id) && in_array($permission->id, $assigned_all_permission)) checked @endif value="{{ $permission->name }}">
                                                    {{ $permission->display_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                              @endif
                            </div>
                          <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a class="btn btn-danger mr-1" href="{{ route('users.index') }}"><i class="icon-cross2"></i> Cancel</a>
                                </div>
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
    
@endsection