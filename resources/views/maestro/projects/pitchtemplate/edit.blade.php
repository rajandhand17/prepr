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

                    <div class="row">
                      @if($languages->count() > 0)
                          @foreach($pitchSection as $key => $section)
                              @foreach($languages as $key => $single)
                                  @php
                                    if ($loop->last) {
                                        $isEnable = 'yes';
                                        $callSpan = 2;
                                    } else {
                                      $isEnable = 'no';
                                      $callSpan = 3;
                                    }
                                      $inputpitchName = "pitch[name][$single->iso][]";
                                      $inputdescriptionName = "pitch[description][$single->iso][]";

                                    if ($single->iso == 'en') {
                                        $inputName = 'title';
                                        $description = 'description';
                                    } else {
                                          $columName = $single->iso;
                                          $inputdescription = $single->iso;
                                          if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                                              $columName = str_replace('-', '_', $columName);
                                              $inputdescription = str_replace('-', '_', $inputdescription);
                                          }
                                          if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                                              $columName = str_replace('-', '_', $columName);
                                              $inputdescription = str_replace('-', '_', $inputdescription);
                                          }
                                        
                                        $inputName = $columName . '_title';
                                        $description = $inputdescription . '_description';
                                    }
                                  @endphp
                                   <div class="col-md-3">
                                    <div class="form-group">
                                      <input type="text" class="form-control" name="{{ $inputpitchName }}" value="{{ $section->inputName }}" required />
                                    </div>
                                  </div>
                                  <div class="col-md-{{$callSpan}}">
                                    <div class="form-group">
                                      <input type="text" class="form-control" name="{{ $inputdescriptionName }}" value="{{ $section->description }}" required/>
                                    </div>
                                  </div>
                                  @if($isEnable == 'yes')
                                    <div class="col-md-1">
                                      <div class="form-group">
                                      <a href="javascript:void(0);" class="remove_templ_btn edit"><i class="fa fa-minus-circle"></i></a>
                                      </div>
                                    </div>
                                  @endif
                              @endforeach
                          @endforeach
                      @endif
                    </div>
                    <div class="dynamic_wraP edit" id="appendSectionHtml"> </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="dynmic_input add-wrp">
                          <a href="javascript:void(0);" class="add_templt_btn" title="Add field" id="addSection"><i class="fa fa-plus-circle"></i> &nbsp; Add Section</a>
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

                    <div class="row">
                      @if($languages->count() > 0)
                        @foreach($pitchTask as $key => $task)
                          @foreach($languages as $key => $single)
                              @php
                                if ($loop->last) {
                                    $isEnable = 'yes';
                                    $callSpan = 5;
                                }else {
                                  $isEnable = 'no';
                                  $callSpan = 6;
                                }
                                  $inputName = "pitch[task][$single->iso][]";
                                  if ($single->iso == 'en') {
                                        $inputValueName = 'title';
                                  } else {
                                        $columName = $single->iso;
                                        if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                                            $columName = str_replace('-', '_', $columName);
                                        }
                                        if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                                            $columName = str_replace('-', '_', $columName);
                                        }
                                      $inputValueName = $columName . '_title';
                                  }
                              @endphp
                              <div class="col-md-{{$callSpan}}">
                                <div class="form-group">
                                <input type="text" class="form-control" name="{{ $inputName }}" value="{{$task->inputValueName}}" required/>
                                </div>
                              </div>
                              @if($isEnable == 'yes')
                                <div class="col-md-1">
                                  <div class="form-group">
                                  <a href="javascript:void(0);" class="remove_task_btn"><i class="fa fa-minus-circle"></i></a>
                                  </div>
                                </div>
                              @endif
                          @endforeach
                          @endforeach
                      @endif
                    </div>
                    <div class="dynamic_wraP edit" id="appendTaskHtml"> </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="dynmic_input add-wrp">
                          <a href="javascript:void(0);" class="add_templt_btn" title="Add field" id="addTask"><i class="fa fa-plus-circle"></i> &nbsp; Add Task</a>
                        </div>
                      </div>
                    </div>
                    <hr>
                    {{-- <div class="row">
                        <div class="col-md-12">
                          <div class="form-group {{($errors->has('status')) ? 'has-error' : ''}}">
                              {!! Form::label('status', 'Status', ['class' => 'control-label']) !!}
                              {!! Form::select('status', $status, old('status'), ['class' => 'form-control']) !!}
                              <span class="help-block">{{ $errors->first('status')}}</span>
                          </div>
                        </div>
                    </div> --}}
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
$(document).ready(function(){
        var maxField = 100;
        var wrapper = $('.dynamic_wraP.edit');

        var templateHTML = `<div class="row">
                            <div class="col-md-3">
                              <div class="form-group">
                              <input type="text" class="form-control" name="pitch[name][en][]" value=""/ required>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group">
                              <input type="text" class="form-control" name="pitch[description][en][]" value=""/ required>
                              </div>
                            </div>
                            <div class="col-md-3">
                              <div class="form-group">
                              <input type="text" class="form-control" name="pitch[name][fr-CA][]" value=""/ required>
                              </div>
                            </div>
                            <div class="col-md-2">
                              <div class="form-group">
                              <input type="text" class="form-control" name="pitch[description][fr-CA][]" value=""/ required>
                              </div>
                            </div>
                            <div class="col-md-1">
                              <div class="form-group">
                              <a href="javascript:void(0);" class="remove_templ_btn edit"><i class="fa fa-minus-circle"></i></a>
                              </div>
                            </div>
                            </div>`;
        var taskHTML =`<div class="row">
                              <div class="col-md-6">
                                <div class="form-group">
                                <input type="text" class="form-control" name="pitch[task][en][]" value=""/ required>
                                </div>
                              </div>
                              <div class="col-md-5">
                                <div class="form-group">
                                <input type="text" class="form-control" name="pitch[task][fr-CA][]" value=""/ required>
                                </div>
                              </div>
                              <div class="col-md-1">
                                <div class="form-group">
                                <a href="javascript:void(0);" class="remove_task_btn"><i class="fa fa-minus-circle"></i></a>
                                </div>
                              </div>
                              </div>`;
        var x = 0;

        $('#addTask').click(function(){
            if(x < maxField){
                x++;
                $('#appendTaskHtml').append(taskHTML);
            }
        });

        $('#addSection').click(function(){
            if(x < maxField){
                x++;
                $('#appendSectionHtml').append(templateHTML);
            }
        });

        //Once remove button is clicked
        $(wrapper).on('click', '.remove_templ_btn', function(e){
            e.preventDefault();
            $(this).parent('.dynamic_wraP').remove(); //Remove field html
            x--; //Decrement field counter
        });

        //Once task remove button is clicked
        $(wrapper).on('click', '.remove_task_btn', function(e){
            e.preventDefault();
            $(this).parent('.dynamic_wraPTask').remove(); //Remove field html
            x--; //Decrement field counter
        });
    });
</script>
@endsection
