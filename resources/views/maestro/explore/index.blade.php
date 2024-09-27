@extends('maestro.layouts.default')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Explore Page: Featured Content</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Explore Page</li>
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
                         </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="search-icon" style="max-height: 37px;">
                                                <i class="fa fa-search"></i>
                                            </span>
                                        </div>
                                        <div class="search-container" style="width: 95%">
                                            <input type="text" id="searchInput" class="search-input form-control" placeholder="Search Challenges, Labs or Resources...">
                                            <div class="row mt-2">
                                                <div class="col-xl-9 col-lg-8 col-md-6 col-sm-10" id="searchResults" style="border-radius: 4px; background: #FFF; box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.25); display: none;">
                                                </div>
                                                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-2" id="filterComponent" style="border-radius: 4px; background: #FFF; box-shadow: 0px 0px 10px 0px rgba(0, 0, 0, 0.25); display: none;">
                                                    <h6 style="text-transform:uppercase">Filter by Component</h6>
                                                    <input type="radio" name="filter" class="filter-input"  value="Lab"> Lab (<span id="labCount">0</span>)<br>
                                                    <input type="radio" name="filter"  class="filter-input" value="Lab Program"> Lab Program (<span id="labProgramCount">0</span>)<br>
                                                    <hr>
                                                    <input type="radio" name="filter" class="filter-input" value="Challenge"> Challenge (<span id="challengeCount">0</span>)<br>
                                                    <input type="radio" name="filter"  class="filter-input" value="Challenge Path"> Challenge Path (<span id="ChallengePathCount">0</span>)<br>
                                                    <hr>
                                                    <input type="radio" name="filter"  class="filter-input" value="Resource Module"> Resource Module (<span id="resourceCount">0</span>)<br>
                                                    <input type="radio" name="filter"  class="filter-input" value="Resource Collection"> Resource Collection (<span id="ResourceCollectionCount">0</span>)<br>
                                                    <input type="radio" name="filter"  class="filter-input" value="Resource Group"> Resource Group (<span id="ResourceGroupCount">0</span>)<br>
                                                    <hr>
                                                    <input type="radio" name="filter"  class="filter-input" value="Project"> Project (<span id="ProjectCount">0</span>)<br>
                                                </div>
                                            </div>
                                            <button id="viewMoreButton" style="display: none;" class="btn btn-block btn-primary mt-2 col-9">View More</button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12" id="componentsContainer">
                                @if($data)
                                    @foreach($data as $component)
                                        <div class="col-12 my-2">
                                            <div class="explore_section explore-item project_component">
                                                <div class="d-flex row">
                                                    <div class="col-xl-2 col-lg-3 col-md-5 col-xs-12 p-2">
                                                        <div class="my_lab_img cover_image">
                                                            @if($component->media_type === 'image')
                                                          
                                                                <img src="{{$component->media}}" alt="image" onerror="imageError(this)" style="width: 100%;">
                                                            @elseif($component->media_type === 'embedded')
                                                                <div class="embed-responsive embed-responsive-21by9" style="height:122px !important;">
                                                                    {!! str_replace(env('AWS_URL').'/', " ", $component->media) !!}
                                                                </div>
                                                            @else
                                                                <video id="video-banner" class="img-thumbnail" controls>
                                                                    <source src="{{ $component->media }}">
                                                                </video>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-5 col-md-7 col-xl-7 p-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <a href="">
                                                                    <h6 class="explore-item-title ttle_break">{{ $component->title }}</h6>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-7 col-xl-3 p-2">
                                                        <div class="mt-2" style="float: right;">
                                                            <a class="btn btn-primary" compId="{{$component->id}}" compType="lab" href="{{ route('explore.edit', $component->id) }}">Edit</a> 
                                                            <a class="btn btn-danger" compId="{{$component->id}}" compType="lab" onclick="deleteExploreData('{{ route('explore.destroy', $component->id) }}')">Remove</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
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

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script type="text/javascript">
        /* Delete Organisation Function */
        function deleteExploreData(url) {
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
                            setTimeout(function () {
                                window.location.reload(true);
                            }, 1500);
                        },
                        error: function (error) {
                            Swal.fire(
                                'Error!',
                                'An error occurred while deleting the Record.',
                                'error'
                            );
                        }
                    });
                } else {
                    Swal.fire(
                        'Canceled!',
                        'You are safe , Record is not deleted!',
                        'error'
                    );
                }
            });
        }
        $(document).ready(function() {
            let currentPage = 1;
            let query = '';
            let selectedFilter = '';

            // Function to perform search and update results
            function searchComponents(query, filter, page = 1) {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('searchComponents') }}",
                    data: { query: query, filter: filter, page: page },
                    success: function(response) {
                        if (page === 1) {
                            $('#searchResults').html(response.html); // Replace existing content on first page load
                        } else {
                            $('#searchResults').append(response.html); // Append new content for subsequent pages
                        }

                        currentPage = page; // Update current page

                        // Show or hide the "View More" button based on remaining results
                        if (currentPage * response.perPage < response.total) {
                            $('#viewMoreButton').show();
                        } else {
                            $('#viewMoreButton').hide();
                        }
                        console.log(response.counts);
                        // Update the filter counts
                        $('#labCount').text(response.counts.Lab);
                        $('#labProgramCount').text(response.counts.LabProgram);
                        $('#challengeCount').text(response.counts.Challenge);
                        $('#challengePathCount').text(response.counts.ChallengePath);
                        $('#resourceCount').text(response.counts.ResourceModule);
                        $('#resourceCollectionCount').text(response.counts.ResourceCollection);
                        $('#resourceGroupCount').text(response.counts.ResourceGroup);
                        $('#projectCount').text(response.counts.Project);

                        $('#searchResults').show(); // Ensure search results are visible
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            }

            // Handle keyup event for search input
            $('#searchInput').on('keyup', function() {
                query = $(this).val().trim();
                if (query) {
                    searchComponents(query, selectedFilter, 1);
                    $('#searchResults').show();
                    $('#filterComponent').show();
                } else {
                    $('#searchResults').hide();
                    $('#viewMoreButton').hide();
                    $('#filterComponent').hide();
                }
            });

            // Handle click event for filters
            $('.filter-input').on('change', function() {
                selectedFilter = $(this).val();
                searchComponents(query, selectedFilter, 1);
            });

            // Handle click event for "View More" button
            $('#viewMoreButton').on('click', function(event) {
                event.stopPropagation();
                searchComponents(query, selectedFilter, currentPage + 1);
            });

            // Hide search results when clicking outside
            $(document).on('click', function(event) {
                if (!$(event.target).closest('#searchResults, #searchInput, #viewMoreButton, #filterComponent').length) {
                    $('#searchResults').hide();
                    $('#viewMoreButton').hide();
                    $('#filterComponent').hide();
                }
            });

            // Prevent hiding when clicking inside search results or "View More" button
            $('#searchResults, #viewMoreButton').on('click', function(event) {
                event.stopPropagation();
            });
        });

        function insertData(compId, compType) {
            $.ajax({
                type: 'POST',
                url: "{{ route('insertExploreData') }}",
                data: {
                    compId: compId,
                    compType: compType,
                    _token: "{{ csrf_token() }}" // Add CSRF token for security
                },
                success: function (data) {
                    if (data.status === 'success') {
                        toastr.success(data.message);
                        window.location.reload();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    toastr.error('An error occurred.');
                }
            });
        }
    </script>
@endsection
