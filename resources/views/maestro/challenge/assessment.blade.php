@extends('maestro.layouts.default')
@section('title', 'Challenge Assessment')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Challenge Assessment</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">Home</a></li>
          <li class="breadcrumb-item active">Challenge Assessment</li>
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
        <h3 class="card-title">Challenge Assessment for <b>{{ $challenge->title }} </b></h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        {!!Form::model(array('method'=>'POST','files' => true,'route'=>array('challenge.assessmentUpdate')))!!}
        @csrf
        <input type="hidden" name="challenge_id" value="{{ $challenge->id }}">
        <input type="hidden" name="is_create" value="create">
        <div class="row">
          <div class="col-md-12">
            <p> <b>Assessment Type</b> </p>
            <p>Choose how project submissions will be assessed.</p>
          </div>
          <div class="col-md-12">
            <h4>Assessment Type</h4>
            <div class="form-group">
              <button type="button" class="btn evaluationType" id="noEvaluation" style="border: double;">No
                Evaluation</button>
              <button type="button" class="btn evaluationType" id="closeEvaluation" style="border: double;">Close
                  Evaluation</button>
              <button type="button" class="btn evaluationType" id="openEvaluation" style="border: double;">Open
                Evaluation</button>
              <input type="hidden" name="assessment_type" id="assessment_type" class="assessment_type" value="0">
            </div>
          </div>
        </div>
        {{-- Start no evaluation --}}
        <div class="row" id="noEval">
          <div class="col-md-12">
            <p> <b> No evaluation</b> is selected. There will be no project assessment for this challenge.</p>
          </div>
        </div>
        {{-- End no evaluation --}}

        {{-- Start Close evaluation --}}
        <div class="row" id="closeEval">
          <div class="col-md-12">
            <p> As a <b> closed evaluation</b>, only assigned members will be able to access the projects.</p>
          </div>

          <div class="col-md-12">
            <div class="form-group {{($errors->has('members_email')) ? 'has-error' : ''}}">
              {!! Form::label('members_email', 'Evaluators', ['class' => 'control-label ']) !!}
              {!! Form::select('members_email',[],[], ['class' => 'form-control select2bs4',
              'id'=>'members_email','required' => 'required' ,'multiple' => 'multiple']) !!}
              <span style="color: #ea6c41 !important;" class="help-block user_error">{{
                $errors->first('members_email')}}</span>
            </div>
          </div>

          <div class="col-md-12">
            <div class="form-group {{($errors->has('visibility')) ? 'has-error' : ''}}">
              <div id="visibility" class="ml-20"></div>
              {!! Form::checkbox('visibility', 'visibility' ,false, ['class'=>'seltype']) !!} 
              <label for="visibility" style="padding-left:10px"> Allow users to view the assessment for their project. </label><br><br>
              <div class="help-block with-errors"></div>
            </div>
          </div>

          <div class="col-md-12">
            <p> <b> Assessment Guidelines</b> </p>
            <p>Write or attach a file to help evaluators understand assessment criteria.</p>
          </div>

          <div class="col-md-12">
            <div class="form-group {{($errors->has('guidelines')) ? 'has-error' : ''}}">
              {!! Form::label('guidelines', 'Description', ['class' => 'control-label']) !!}
              {!! Form::textarea('guidelines',null, ['class' => 'form-control', 'rows'=>'10',"id"=>"guidelines"]) !!}
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('guidelines')}}</span>
            </div>
          </div>

          <div class="col-md-12">
            <div class="form-group {{($errors->has('attachments')) ? 'has-error' : ''}}">
              {!! Form::label('attachments', 'Attachment', ['class' => 'control-label']) !!}</br>
              {!! Form::file('attachments', ['class' => 'form-control']) !!}
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('attachments')}}</span>
            </div>
          </div>
        </div>
        {{-- End Close evaluation --}}

        {{-- Open Close evaluation --}}
        <div class="row" id="openEval">

          <div class="col-md-12">
            <p> As a <b> open evaluation</b>, all users who participated in the challenge will be able to assess the
              projects.</p>
          </div>

          <div class="col-md-12">
            <div class="form-group {{($errors->has('visibility')) ? 'has-error' : ''}}">
              <div id="visibility" class="ml-20"></div>
              {!! Form::checkbox('visibility', 'visibility' ,false, ['class'=>'seltype']) !!} <label for="visibility"
                style="padding-left:10px"> Allow users to view the assessment for their project. </label><br><br>
              <div class="help-block with-errors"></div>
            </div>
          </div>

          <div class="col-md-12">
            <p> <b> Assessment Guidelines</b> </p>
            <p>Write or attach a file to help evaluators understand assessment criteria.
            </p>
          </div>

          <div class="col-md-12">
            <div class="form-group {{($errors->has('guidelines')) ? 'has-error' : ''}}">
              {!! Form::label('guidelines', 'Description', ['class' => 'control-label']) !!}
              {!! Form::textarea('guidelines',null, ['class' => 'form-control', 'rows'=>'10',"id"=>"guidelines"]) !!}
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('guidelines')}}</span>
            </div>
          </div>

          <div class="col-md-12">
            <div class="form-group {{($errors->has('attachments')) ? 'has-error' : ''}}">
              {!! Form::label('attachments', 'Attachment', ['class' => 'control-label']) !!}</br>
              {!! Form::file('attachments', ['class' => 'form-control']) !!}
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('attachments')}}</span>
            </div>
          </div>

        </div>
        {{-- End Open evaluation --}}

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Update</button>
              <a class="btn btn-danger mr-1" href="{{ route('challenge.index') }}"><i class="icon-cross2"></i>
                Cancel</a>
            </div>
          </div>
        </div>
        {!!Form::close()!!}
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

@section('scripts')
<script>
    $(document).ready(function () {
        getUsers();
        $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-primary');
        $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-secoundary');
        $('#noEvaluation').addClass('btn-primary');
        $('#openEvaluation','closeEvaluation').addClass('btn-secoundary');
        $('#assessment_type').val('0');
        $('#noEval').show();
        $('#openEval,#closeEval').hide();
    });

    $(document).on('click', '#noEvaluation', function() {
      $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-primary');
      $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-secoundary');
      $('#noEvaluation').addClass('btn-primary');
      $('#openEvaluation','closeEvaluation').addClass('btn-secoundary');
      $('#assessment_type').val('0');
      $('#noEval').show();
      $('#openEval,#closeEval').hide();
    });

    $(document).on('click', '#openEvaluation', function() {
      $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-primary');
      $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-secoundary');
      $('#openEvaluation').addClass('btn-primary');
      $('#noEvaluation','closeEvaluation').addClass('btn-secoundary');
      $('#assessment_type').val('1');
      $('#openEval').show();
      $('#noEval,#closeEval').hide();
    });

    $(document).on('click', '#closeEvaluation', function() {
      $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-primary');
      $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-secoundary');
      $('#closeEvaluation').addClass('btn-primary');
      $('#openEvaluation','noEvaluation').addClass('btn-secoundary');
      $('#assessment_type').val('2');
      $('#closeEval').show();
      $('#noEval,#openEval').hide();
    });

    function getUsers(){
        $('#members_email').select2({      
            placeholder: "Select Member",
            ajax: {
                url: '{{ route('getUserEmail') }}',
                type: 'GET',
                dataType: 'json',
                data: function (params) {
                    return {
                        search: params.term,
                    };
                },
                processResults: function (data) {
                    if(data.status == 'fail'){
                      $('#members_email').select2("close");
                      $('.user_error').show();
                      $('.user_error').html(data.message);
                    } else {
                        $('.user_error').hide();
                        return {
                          results: data.result
                        };
                    }
                }
            }
        });
    }

        // Incentive section start
        $('.add_new_incentive').click(function(){
        var row_no = $(this).attr('row-no');
        var html_data = $('#row_no_1').html();
        var row_no = parseInt(row_no) + 1;
        $(this).attr('row-no', row_no);
        var new_html = '<div class="row form-group" id="row_no_' + row_no + '">'
                new_html += html_data;
        new_html += '</div>';
        $('#incentive_area_appends').append(new_html);
        $('#row_no_' + row_no).find(".button_add_remove").html('<button class="btn btn-danger" onclick="removeIncentive(' + row_no + ')" type="button" style="margin-top: 20px;">-</button>');
        $('#row_no_' + row_no).find(".incentive_name").val('');
        $('#row_no_' + row_no).find(".incentive_prize").val('');
        $('#row_no_' + row_no).find(".incentive_point").val('');
        $('#row_no_' + row_no).find(".incentive_trophy").val('');
        $('#row_no_' + row_no).find(".trtrytr").remove();
        });
        function removeIncentive(row_no){
        $('#incentive_area_appends').find('#row_no_' + row_no).remove();
        }
        // INcentive section end
</script>
@endsection