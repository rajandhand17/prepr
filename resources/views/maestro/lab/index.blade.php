@extends('maestro.layouts.default')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Labs</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Lab</li>
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
                        <div class="row">
                            <div class="col-md-11">
                                <a class="btn btn-primary btn-rounded btn-small btn-icon left-icon" style="float: right;" href="{{route('lab.create')}}" role="menuitem">Create Lab</a>
                            </div>
                            <div class="col-md-1">
                                @include('maestro/common/language-switcher')
                            </div>
                        </div>
                        
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
        function deleteLab(url) {
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
                                'An error occurred while deleting the organization.',
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


        function ChallengeToLabTemplate(url) {
            var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to add this Lab to Lab Marketplace.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Add it.'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        success: function (result) {
                            if(result.status == 'success'){
                                Swal.fire(
                                    'Created!',
                                    result.message,
                                    'success'
                                );
                            } else if(result.status == 'fail'){
                                Swal.fire(
                                    'Error!',
                                    result.message,
                                    'fail'
                                );
                            }
                            setTimeout(
                                function () {
                                    window.location.reload(true);
                                }, 1500);
                        },
                        error: function (error) {
                            Swal.fire(
                                'Error!',
                                'An error occurred while adding this Lab to Lab Marketplace.',
                                'error'
                            );
                        }
                    });
                }else {
                    Swal.fire(
                        'Canceled!',
                        'This Lab will not be added to Marketplace',
                        'error'
                    );
                }
            });
        }
    </script>
@endsection
