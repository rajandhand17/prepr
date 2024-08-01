@extends('maestro.layouts.default')
@section('title', 'View Tag Group')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Tag Group Details</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Tag Group Details</li>
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
              <h3 class="card-title">Tag Group Details</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered m-0">
                        <tbody>
                        @if($languages->count() > 0)
        @foreach($languages as $single)

            @php
                if ($single->iso == 'en') {
                    $lableName = 'Tag Group Title';
                    $inputName = 'title';

                    $lableName2 = 'Tag Group Description';
                    $inputName2 = 'description';
                } else {
                     $columName = $single->iso;
                     $lableName = $single->name . ' Tag Group Title';
                     $lableName2 = $single->name . ' Tag Group Description';
                     if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                         $columName = str_replace(' ', '_', $columName);
                     }
                     if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                         $columName = str_replace('-', '_', $columName);
                     }

                    $inputName = $columName . '_title';
                    $inputName2 = $columName . '_description';
                 }
            @endphp
            <tr>
              <td style="width: 25%;"><code>{{$lableName}}</code></td>
              <td>{{$taggroup->$inputName}}</td>
            </tr>

            <tr>
              <td style="width: 25%;"><code>{{$lableName2}}</code></td>
              <td>{{$taggroup->$inputName2}}</td>
            </tr>

        @endforeach
    @endif
    <tr>
        <td style="width: 25%;"><code>Tags</code></td>
        <td>{{$selectedTags}}</td>
    </tr>
                        </tbody>
                    </table>
                  </div>
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
