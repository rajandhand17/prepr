@extends('maestro.layouts.default')
@section('title', 'Edit Setting')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Setting</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('setting.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Edit Setting</li>
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
                <h3 class="card-title">Edit Setting</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                {!!Form::open(array('method'=>'PUT','route' => ['setting.update', $setting->id],'files'=>'true', 'data-toggle'=>"validator",'role'=>"form",'novalidate'=>"true"))!!}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" name="code" disabled="disabled" class="form-control" value="{{ $setting->code }}" id="code" placeholder="Enter Code Name" required>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="value">Value</label>
                           @if($setting->module_type=='5')
                            <input type="file" name="value" class="form-control"  id="value" placeholder="Enter Value Name" required>

                            @else
                            <input type="text" name="value" class="form-control" value="{{ $setting->value }}" id="value" placeholder="Enter Value Name" required>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="label">Label</label>
                            <input type="text" name="label" class="form-control" id="label" value="{{ $setting->label }}" placeholder="Enter Module label" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Submit</button>
                            <a class="btn btn-danger mr-1" href="{{ route('setting.index') }}"><i class="icon-cross2"></i> Cancel</a>
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
