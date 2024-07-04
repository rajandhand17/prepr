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
        {!!Form::open(array('method'=>'POST','route' => ['challenge.assessmentStore'],'files'=>true))!!}
        @csrf
        @php
          $request_type = !empty($assessment) ? 'update' : 'create';
          $assessment_id = !empty($assessment) ? $assessment->id : null;
          $assessment_type = !empty($assessment) ? $assessment->assessment_type : 0; // 0 no assessment , 1 open assessment ,2 close assessment
          $visibility = !empty($assessment) && $assessment->visibility == '1' ? 'checked' : null;
          $members_email = !empty($assessment) ? $assessment->members_email : [];
          $guidelines = !empty($assessment) ? $assessment->guidelines : null;
          $attachments = !empty($assessment) ? $assessment->attachments : null;
        @endphp
        <input type="hidden" name="request_type" value="{{ $request_type }}">
        <input type="hidden" name="challenge_id" value="{{ $challenge->id }}">
        <input type="hidden" name="assessment_id" value="{{ $assessment_id }}">
        <input type="hidden" name="assessment_type" id="assessment_type" value="{{ $assessment_type }}">
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
              {!! Form::select('members_email[]',[],$members_email, ['class' => 'form-control select2bs4',
              'id'=>'members_email' ,'multiple' => 'multiple']) !!}
              <span style="color: #ea6c41 !important;"
                class="help-block user_error">{{$errors->first('members_email')}}</span>
            </div>
          </div>

          <div class="col-md-12">
            <div class="form-group {{($errors->has('visibility')) ? 'has-error' : ''}}">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="visibility" id="visibility" {{ $visibility }}>
                <label class="form-check-label">Allow users to view the assessment for their project.</label>
              </div>
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
              {!! Form::textarea('closeEvGuidelines',$guidelines, ['class' => 'form-control',
              'rows'=>'10',"id"=>"guidelines"]) !!}
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('guidelines')}}</span>
            </div>
          </div>

          <div class="col-md-12">
            <div class="form-group {{($errors->has('attachments')) ? 'has-error' : ''}}">
              {!! Form::label('attachments', 'Attachment', ['class' => 'control-label']) !!}</br>
              {!! Form::file('closeEvAttachments', ['class' => 'form-control']) !!}
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
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="visibility" id="visibility" {{ $visibility }}>
                <label class="form-check-label">Allow users to view the assessment for their project.</label>
              </div>
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
              {!! Form::textarea('openEvGuidelines',$guidelines, ['class' => 'form-control',
              'rows'=>'10',"id"=>"guidelines"]) !!}
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('guidelines')}}</span>
            </div>
          </div>

          <div class="col-md-12">
            <div class="form-group {{($errors->has('attachments')) ? 'has-error' : ''}}">
              {!! Form::label('attachments', 'Attachment', ['class' => 'control-label']) !!}</br>
              {!! Form::file('openEvAttachments', ['class' => 'form-control']) !!}
              <span style="color: #ea6c41 !important;" class="help-block">{{ $errors->first('attachments')}}</span>
            </div>
          </div>

        </div>
        {{-- End Open evaluation --}}

        {{-- Criteria start --}}
        <hr>
        <div id="criteriaSection">
          <b> Assessment Criteria *</b>
          @if(!empty($criteria))
          @foreach ($criteria as $key => $criteriaObj)
          <div class="row form-group" id="row_no{{ $key }}1">
            <div class="col-md-4">
              <div class="form-group {{($errors->has('creteria_title')) ? 'has-error' : ''}}">
                {!! Form::label('creteria_title', 'Criteria Title', ['class' => 'control-label']) !!}
                {!! Form::text('creteria_title[]',$criteriaObj->title, ['class' => 'form-control
                creteria_title','placeholder' => "Criteria Title : Maximum 30 characters",'required' => 'required','id'
                => 'creteria_title']) !!}
                <span class="help-block">{{ $errors->first('creteria_title')}}</span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group {{($errors->has('score')) ? 'has-error' : ''}}">
                {!! Form::label('score', 'Max Score', ['class' => 'control-label']) !!}
                {!! Form::number('score[]',$criteriaObj->score, ['class' => 'form-control score','placeholder'=> "Max
                Score",'min'=> "0",'required' => 'required','id' => 'score']) !!}
                <span class="help-block">{{ $errors->first('score')}}</span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group {{($errors->has('weight')) ? 'has-error' : ''}}">
                {!! Form::label('weight', 'Weight %', ['class' => 'control-label']) !!}
                {!! Form::number('weight[]',$criteriaObj->weight, ['class' => 'form-control weight','min'=> "0" ,'max'
                => "100", 'placeholder' => "Weight : Sum of weight percentages must add up to 100%.",'required' =>
                'required','id' => 'weight']) !!}
                <span class="help-block">{{ $errors->first('weight')}}</span>
              </div>
            </div>
            <div class="col-sm-1 col-xs-1 button_add_remove">
              @if($key == 0)
                <button class="btn btn-success add_new_incentive" type="button" row-no="1" style="margin-top: 20px;">+</button>
              @else
                <button class="btn btn-danger" onclick="removeIncentive({{ count($criteria) + 1 }})" type="button" style="margin-top: 20px;">-</button>
              @endif
            </div>
          </div>
          @endforeach
          @endif
          <div class="row form-group" id="row_no_1">
            <div class="col-md-4">
              <div class="form-group {{($errors->has('creteria_title')) ? 'has-error' : ''}}">
                {!! Form::label('creteria_title', 'Criteria Title', ['class' => 'control-label']) !!}
                {!! Form::text('creteria_title[]',null, ['class' => 'form-control creteria_title','placeholder' =>
                "Criteria Title : Maximum 30 characters",'required' => 'required','id' => 'creteria_title']) !!}
                <span class="help-block">{{ $errors->first('creteria_title')}}</span>
              </div>
            </div>
            <div class="col-md-3">
              <div class="form-group {{($errors->has('score')) ? 'has-error' : ''}}">
                {!! Form::label('score', 'Max Score', ['class' => 'control-label']) !!}
                {!! Form::number('score[]',null, ['class' => 'form-control score','placeholder'=> "Max Score",'min'=>
                "0",'required' => 'required','id' => 'score']) !!}
                <span class="help-block">{{ $errors->first('score')}}</span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group {{($errors->has('weight')) ? 'has-error' : ''}}">
                {!! Form::label('weight', 'Weight %', ['class' => 'control-label']) !!}
                {!! Form::number('weight[]',null, ['class' => 'form-control weight','min'=> "0" ,'max' => "100",
                'placeholder' => "Weight : Sum of weight percentages must add up to 100%.",'required' => 'required','id'
                => 'weight']) !!}
                <span class="help-block">{{ $errors->first('weight')}}</span>
              </div>
            </div>
            <div class="col-sm-1 col-xs-1 button_add_remove">
              <button class="btn btn-success add_new_incentive" type="button" row-no="{{ count($criteria) + 1 }}"
                style="margin-top: 20px;">+</button>
            </div>
          </div>
          <div class="incentive_area_appends" id="incentive_area_appends"></div>
        </div>
        {{-- Criteria end --}}

        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <button type="submit" class="btn btn-primary">Update Project Assessment</button>
              <a class="btn btn-danger mr-1" href="{{ route('challenge.index') }}"><i class="icon-cross2"></i>Cancel</a>
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
@stop

@section('scripts')
<script>
  var assessment_type = @json($assessment_type);

   if(assessment_type == '0'){
      noEvaluation();    
   }else if(assessment_type == '1'){
      openEvaluation();
   }else if(assessment_type == '2'){
      closeEvaluation();
   }

    $(document).ready(function () {
        getUsersMember();
    });

    $(document).on('click', '#noEvaluation', function() {
      noEvaluation();
    });

    $(document).on('click', '#openEvaluation', function() {
      openEvaluation();
    });

    $(document).on('click', '#closeEvaluation', function() {
      closeEvaluation();
    });
    function noEvaluation(){
      $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-primary');
      $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-secoundary');
      $('#noEvaluation').addClass('btn-primary');
      $('#openEvaluation','closeEvaluation').addClass('btn-secoundary');
      $('#assessment_type').val('0');
      $('#noEval').show();
      $('#openEval,#closeEval').hide();
      $('#criteriaSection').hide();
      $('#creteria_title,#score,#weight').removeAttr('required');
    }

    function openEvaluation(){
      $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-primary');
      $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-secoundary');
      $('#openEvaluation').addClass('btn-primary');
      $('#noEvaluation','closeEvaluation').addClass('btn-secoundary');
      $('#assessment_type').val('1');
      $('#openEval').show();
      $('#noEval,#closeEval').hide();
      $('#criteriaSection').show();
      $('#creteria_title,#score,#weight').attr('required', 'required');
    }

    function closeEvaluation(){
      $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-primary');
      $('#noEvaluation,#openEvaluation,#closeEvaluation').removeClass('btn-secoundary');
      $('#closeEvaluation').addClass('btn-primary');
      $('#openEvaluation','noEvaluation').addClass('btn-secoundary');
      $('#assessment_type').val('2');
      $('#closeEval').show();
      $('#noEval,#openEval').hide();
      $('#criteriaSection').show();
      $('#creteria_title,#score,#weight').attr('required', 'required');
    }

    function getUsersMember(){
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
          $('#row_no_' + row_no).find(".creteria_title").val('');
          $('#row_no_' + row_no).find(".score").val('');
          $('#row_no_' + row_no).find(".weight").val('');
          $('#row_no_' + row_no).find(".incentive_trophy").val('');
          $('#row_no_' + row_no).find(".trtrytr").remove();
        });

        function removeIncentive(row_no){
          $('#incentive_area_appends').find('#row_no_' + row_no).remove();
        }
</script>
@endsection