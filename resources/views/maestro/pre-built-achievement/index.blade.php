@extends('maestro.layouts.default')
@section('title', 'Pre-built Achievement')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Create New Pre-Built Achievement</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <a class="btn btn-primary btn-rounded btn-small btn-icon left-icon" style="float: right;" href="{{route('pre-built-achievement.create')}}" role="menuitem">Create New</a>
                <button id="deleteSelected" class="btn btn-danger" style="float: right; margin-right: 21px;">Delete Selected</button>
                        
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
                        {{-- <select id="component_type_filter" class="form-control">
                            <option value="">All Components</option>
                            <option value="lab">Lab</option>
                            <option value="lab_program">Lab Program</option>
                            <option value="challenge">Challenge</option>
                            <option value="challenge_path">Challenge Path</option>
                        </select> --}}
                        
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table class="table table-bordered data-table">
                            {!! $html->table([],true) !!}
                        </table>
                    </div>
                    <!-- /.card-body -->
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
    
    <script>
        @if(Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif

        @if(Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif
    </script>

    <script>
        function deleteAchievement(url) {
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
                            if(result.status == 'success'){
                                Swal.fire(
                                    'Deleted!',
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
                                'An error occurred while deleting the Pre Built Achievement.',
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

        // $('#component_type_filter').on('change', function() {
        //     $('#dataTableBuilder_wrapper').DataTable().ajax.reload();
        // });

        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#achievementTable').DataTable();

            // Select/Deselect all checkboxes
            $('#select_all').on('click', function() {
                var rows = table.rows({ 'search': 'applied' }).nodes();
                $('input[type="checkbox"]', rows).prop('checked', this.checked);
            });

            // Handle the deletion of selected records
            $('#deleteSelected').on('click', function() {
                var selectedIds = [];
                $('.select_ticket:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    if (confirm('Are you sure you want to delete the selected records?')) {
                        $.ajax({
                            url: "{{ route('pre-built-achievement.bulk-delete') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: selectedIds
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    );
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        result.message,
                                        'fail'
                                    );
                                    alert('Something went wrong, please try again.');
                                }
                                setTimeout(
                                function () {
                                    window.location.reload(true);
                                }, 1500);
                            },
                            error: function(xhr) {
                                toastr.error(xhr.responseText, 'Error!');
                            }
                        });
                    }
                } else {
                    toastr.error('Please select at least one record.', 'Error!');
                }
            });
        });

        var complaintIds = [];
        setTimeout(function() {
            $('#dataTableBuilder_length').addClass('pull-left');
            $('#dataTableBuilder_info').addClass('pull-left');
        }, 200);
       
        /***
         * Select all challenges at once
         */
         function toggle(source) {
            complaintIds = []
            checkboxes = document.getElementsByName('check');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = source.checked;
                if (source.checked == true) {
                    complaintIds.push(parseInt(checkboxes[i].getAttribute('data-id')));
                } else {
                    complaintIds.pop(parseInt(checkboxes[i].getAttribute('data-id')));
                }
            }
        }

        /***
         * Select a checkbox
         */
        function select_tickets(el) {
            $(".take_tickets").css('display', 'block');
            var id = $(el).data("id");
            if (el.checked) {
                complaintIds.push(id);
            } else {
                complaintIds.pop(id);
            }
        }

        $('body').on('change', '.select_ticket', function() {
            select_tickets(this)
        });

        /***
         * update checkboxes on page refresh
         */
        $('#dataTableBuilder').on('draw.dt', function() {
            checkboxes1 = document.getElementsByName('check');
            for (var i = 0, n = checkboxes1.length; i < n; i++) {
                var num = parseInt(checkboxes1[i].getAttribute("data-id"));
                var got = complaintIds.indexOf(num);
                if(got >= 0){
                    checkboxes1[i].checked = true;
                }
            }
        });
    </script>
@endsection