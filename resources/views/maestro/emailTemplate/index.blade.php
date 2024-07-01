@extends('maestro.layouts.default')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Emai Templates</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Emai Template</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- /.card -->

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"></h3>
                        <a class="btn btn-primary btn-rounded btn-small btn-icon left-icon" style="float: right;"
                            href="{{route('emailTemplates.create')}}" role="menuitem">Create Emai Template</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered data-table">
                            {!! $html->table() !!}
                        </table>
                    </div>
               
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
@stop
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

@section('scripts')
    {!! $html->scripts() !!}

    <script type="text/javascript">
        /* Delete Organisation Function */
        function deleteEmailTemplate(url) {
            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        success: function (result) {
                            Swal.fire(
                                'Deleted!',
                                result.message,
                                'success'
                            );
                            setTimeout(
                            function () {
                                window.location.reload(true);
                            }, 1500);
                        },
                        error: function (error) {
                            Swal.fire(
                                'Error!',
                                'An error occurred while deleting the Email Template.',
                                'error'
                            );
                        }
                    });
                }else {
                    Swal.fire(
                        'Canceled!',
                        'You are safe , Record is not deleted!',
                        'error'
                    );
                }
            });
        }

        setTimeout(function () {
            $('#dataTableBuilder_length').addClass('pull-left');
            $('#dataTableBuilder_info').addClass('pull-left');
        }, 200);

        
        @if(Session::has('success'))
              toastr.success("{{ Session::get('success') }}");
                @endif

        @if(Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif
    </script>
@endsection