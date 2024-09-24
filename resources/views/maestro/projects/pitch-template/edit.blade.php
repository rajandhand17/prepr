@extends('maestro.layouts.default')
@section('title', 'Edit Pitch Template')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Edit Pitch Template</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('projects-pitch-template.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Edit Pitch Template</li>
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
              <h3 class="card-title">Edit Pitch Template</h3>
            </div>
            <!-- /.card-header -->
                <div class="card-body">
                  {!!Form::model($pitchTemplate,array('method'=>'PUT','files'=>true,'route'=>array('projects-pitch-template.update',$pitchTemplate->id)))!!}
                    <div class="row">
                      @if($languages->count() > 0)
                          @foreach($languages as $single)
                              @php
                                  if ($single->iso == 'en') {
                                      $lableName = 'English Pitch Template Title';
                                      $inputName = 'title';
                                  } else {
                                        $columName = $single->iso;
                                        $lableName = $single->name . ' Pitch Template Title';
                                        if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                                            $columName = str_replace(' ', '_', $columName);
                                        }
                                        if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                                            $columName = str_replace('-', '_', $columName);
                                        }
                                      $inputName = $columName . '_title';
                                  }
                              @endphp
                              <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label($inputName, $lableName, ['class' => 'control-label']) !!}
                                    {!! Form::text($inputName,null, ['class' => 'form-control','required' => 'required']) !!}
                                </div>
                              </div>
                          @endforeach
                      @endif
                    </div>
                    <hr>
                    <div class="row">
                      @if($languages->count() > 0)
                          @foreach($languages as $single)
                              @php
                                  if ($single->iso == 'en') {
                                      $lableName = 'English Pitch Template';
                                      $placeholde = 'English Placeholder Text';
                                  } else {
                                        $lableName = $single->name . ' Pitch Template';
                                        $placeholde = $single->name . ' Placeholder Text';
                                  }
                              @endphp
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label class="control-label">{{ $lableName }}</label>
                                </div>
                              </div>
                              <div class="col-md-3">
                                <div class="form-group">
                                  <label class="control-label">{{ $placeholde }}</label>
                                </div>
                              </div>
                          @endforeach
                      @endif
                    </div>

                    <div id="dynamicTable">
                      @if(!empty($pitchSection) && count($pitchSection) > 0)
                          @foreach($pitchSection as $pitch)
                          <div class="row dynamicRow">
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <input type="text" class="form-control" name="pitch[name][en][]" value="{{ $pitch->title }}" required="">
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <input type="text" class="form-control" name="pitch[description][en][]" value="{{ $pitch->description }}" required="">
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <input type="text" class="form-control" name="pitch[name][fr-CA][]" value="{{ $pitch->fr_CA_title }}" required="">
                                  </div>
                              </div>
                              <div class="col-md-2">
                                  <div class="form-group">
                                      <input type="text" class="form-control" name="pitch[description][fr-CA][]" value="{{ $pitch->fr_CA_description }}" required="">
                                  </div>
                              </div>
                              <div class="col-md-1">
                                  <div class="form-group">
                                      <a href="javascript:void(0);" class="remove_task_btn"><i class="fa fa-minus-circle"></i></a>
                                  </div>
                              </div>
                          </div>
                          @endforeach
                      @else
                          <div class="row dynamicRow">
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <input type="text" class="form-control" name="pitch[name][en][]" value="" required="">
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <input type="text" class="form-control" name="pitch[description][en][]" value="" required="">
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <input type="text" class="form-control" name="pitch[name][fr-CA][]" value="" required="">
                                  </div>
                              </div>
                              <div class="col-md-2">
                                  <div class="form-group">
                                      <input type="text" class="form-control" name="pitch[description][fr-CA][]" value="" required="">
                                  </div>
                              </div>
                              <div class="col-md-1">
                                  <div class="form-group">
                                      <a href="javascript:void(0);" class="remove_task_btn"><i class="fa fa-minus-circle"></i></a>
                                  </div>
                              </div>
                          </div>
                      @endif
                  </div>
                  
                  <div class="row">
                      <div class="col-md-12">
                          <div class="dynmic_input add-wrp">
                              <a href="javascript:void(0);" class="add_templt_btn" title="Add field" id="addRowBtn"><i class="fa fa-plus-circle"></i>&nbsp; Add Section</a>
                          </div>
                      </div>
                  </div>
                  <hr>
                  

                    <div class="row">
                      @if($languages->count() > 0)
                          @foreach($languages as $single)
                              @php
                                  if ($single->iso == 'en') {
                                      $lableName = 'English Task';
                                  } else {
                                        $lableName = $single->name . ' Task';
                                  }
                              @endphp
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="control-label">{{ $lableName }}</label>
                                </div>
                              </div>
                          @endforeach
                      @endif
                    </div>
                    <div id="dynamicTaskTable">
                      @if(!empty($pitchTask) && count($pitchTask) > 0)
                          @foreach($pitchTask as $task)
                          <div class="row dynamicRow">
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <input type="text" class="form-control" name="pitch[task][en][]" value="{{ $task->title }}" required="">
                                  </div>
                              </div>
                              <div class="col-md-5">
                                  <div class="form-group">
                                      <input type="text" class="form-control" name="pitch[task][fr-CA][]" value="{{ $task->fr_CA_title }}" required="">
                                  </div>
                              </div>
                              <div class="col-md-1">
                                  <div class="form-group">
                                      <a href="javascript:void(0);" class="remove_task_btn"><i class="fa fa-minus-circle"></i></a>
                                  </div>
                              </div>
                          </div>
                          @endforeach
                      @else
                          <div class="row dynamicRow">
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <input type="text" class="form-control" name="pitch[task][en][]" value="" required="">
                                  </div>
                              </div>
                              <div class="col-md-5">
                                  <div class="form-group">
                                      <input type="text" class="form-control" name="pitch[task][fr-CA][]" value="" required="">
                                  </div>
                              </div>
                              <div class="col-md-1">
                                  <div class="form-group">
                                      <a href="javascript:void(0);" class="remove_task_btn"><i class="fa fa-minus-circle"></i></a>
                                  </div>
                              </div>
                          </div>
                      @endif
                  </div>
                  
                  <div class="row">
                      <div class="col-md-12">
                          <div class="dynmic_input add-wrp">
                              <a href="javascript:void(0);" class="add_templt_btn" title="Add field" id="addTask"><i class="fa fa-plus-circle"></i>&nbsp;  Add Task</a>
                          </div>
                      </div>
                  </div>
                  <hr>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <a class="btn btn-danger mr-1" href="{{ route('projects-pitch-template.index') }}"><i class="icon-cross2"></i> Cancel</a>
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
<script type="text/javascript">
    $(document).ready(function() {
    let rowCount = {{ count($pitchTask) > 0 ? count($pitchTask) : 1 }};

    $('#addTask').on('click', function() {
        rowCount++;
        const newRow = `
            <div class="row dynamicRow" id="row-${rowCount}">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" name="pitch[task][en][]" value="" required="">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="text" class="form-control" name="pitch[task][fr-CA][]" value="" required="">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <a href="javascript:void(0);" class="remove_task_btn"><i class="fa fa-minus-circle"></i></a>
                    </div>
                </div>
            </div>
        `;
        $('#dynamicTaskTable').append(newRow);
    });

    $('#dynamicTaskTable').on('click', '.remove_task_btn', function() {
        if ($('#dynamicTaskTable .dynamicRow').length > 1) {
            $(this).closest('.dynamicRow').remove();
        }
    });
});


$(document).ready(function() {
    let rowCount = {{ count($pitchSection) > 0 ? count($pitchSection) : 1 }};

    $('#addRowBtn').on('click', function() {
        rowCount++;

        const newRow = `
            <div class="row dynamicRow" id="row-${rowCount}">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" class="form-control" name="pitch[name][en][]" value="" required="">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" class="form-control" name="pitch[description][en][]" value="" required="">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" class="form-control" name="pitch[name][fr-CA][]" value="" required="">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <input type="text" class="form-control" name="pitch[description][fr-CA][]" value="" required="">
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <a href="javascript:void(0);" class="remove_task_btn"><i class="fa fa-minus-circle"></i></a>
                    </div>
                </div>
            </div>
        `;

        $('#dynamicTable').append(newRow);
    });

    $('#dynamicTable').on('click', '.remove_task_btn', function() {
        if ($('#dynamicTable .dynamicRow').length > 1) {
            $(this).closest('.dynamicRow').remove();
        }
    });
});

</script>
@endsection
