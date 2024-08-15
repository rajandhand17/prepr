@extends('maestro.layouts.default')
@section('title', 'View Vendor')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>View Vendor</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('vendor-management.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">View Vendor</li>
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
                            <input type="text" name="api" class="form-control" id="api" value="{{ $vendor->api_key ?? '' }}" placeholder="Enter api key" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="secret">Secret Key</label>
                            <input type="text" name="secret" class="form-control" id="secret" value="{{ $vendor->secret_key ?? '' }}" placeholder="Enter secret Key" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="secret">Is Active</label>
                            <Select name="active" class="form-control">
                                <option value="yes">Yes</option>
                                <option value="no">No</option>
                            </Select>
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
