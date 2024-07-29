@extends('maestro.layouts.default')
@section('title', 'Edit Vendor')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Vendor</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('vendor-management.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Edit Vendor</li>
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
                <h3 class="card-title">Edit Vendor</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                {!!Form::open(array('method'=>'PUT','route' => ['vendor-management.update', $vendor->id],'files'=>'true','role'=>"form"))!!}
                <b>User Information</b> <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="username">Name</label>
                            <input type="text" name="name" class="form-control" id="name" value="{{ $vendor->name ?? '' }}" placeholder="Enter Vendor Name" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">E-Mail Address</label>
                            <input type="email" name="email" class="form-control" id="email" value="{{ $vendor->email ?? '' }}" placeholder="Enter E-Mail Address" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="api">Api Key</label>
                            <input type="text" name="api_key" class="form-control" id="api_key" value="{{ $vendor->api_key ?? '' }}" placeholder="Enter api key" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="secret">Secret Key</label>
                            <input type="text" name="secret_key" class="form-control" id="secret_key" value="{{ $vendor->secret_key ?? '' }}" placeholder="Enter secret Key" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="secret">Is Active</label>
                            <select name="is_active" class="form-control" id="is_active" >
                                     <option {{ $vendor->is_active == '1' ? 'selected' : '' }} value="yes">Yes</option>
                                     <option {{ $vendor->is_active == '0' ? 'selected' : '' }} value="no">No</option>
                            </select>
                        </div>
                    </div>
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
