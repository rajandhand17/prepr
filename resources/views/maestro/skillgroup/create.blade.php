@extends('maestro.layouts.default')
@section('title', 'Create Skill Group')
@section('content')
     <!-- Content Header (Page header) -->
     <section class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1>Create Skill Group</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('skill-group.index') }}">Home</a></li>
                <li class="breadcrumb-item active">Create Skill Group</li>
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
              <h3 class="card-title">Create Skill Group</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                    {!!Form::open(array('method'=>'POST','route'=>'skill-group.store','files'=>'true', 'data-toggle'=>"validator",'role'=>"form",'novalidate'=>"true"))!!}
                        @include('maestro.skillgroup.form')
                        <div class="form-actions mt-10">
                            {!!Form::submit('Save',array('class'=>'btn btn-primary mr-10'))!!}

                            <a class="btn btn-danger mr-1" href="{{ route('skills.index') }}">
                                <i class="icon-cross2"></i> Cancel
                            </a>
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
<script>
   $(document).ready(function() {
    getSkills();
    getSkillStack();
});

function getSkills() {
    $('#Skills').select2({
        placeholder: "Select skill",
        ajax: {
            url: '{{route('getAjaxSkills')}}',
            cache: true,
            type: 'GET',
            dataType: 'json',
            data: function(params) {
                return {
                    search: params.term,
                };
            },
            processResults: function(data) {
                if (data.status == 'fail') {
                    $('#Skills').select2("close");
                    $('.skill_error').show();
                    $('.skill_error').html(data.message);
                } else {
                    $('.skill_error').hide();
                    return {
                        results: data.result.map(function(skill) {
                            return {
                                id: skill.id,
                                text: skill.text
                            };
                        })
                    };
                }
            }
        }
    });
}
function getSkillStack() {
    $('#SkillStack').select2({
        placeholder: "Select skill",
        ajax: {
            url: '{{route('getAjaxSkillStack')}}',
            cache: true,
            type: 'GET',
            dataType: 'json',
            data: function(params) {
                return {
                    search: params.term,
                };
            },
            processResults: function(data) {
                if (data.status == 'fail') {
                    $('#SkillStack').select2("close");
                    $('.skillstack_error').show();
                    $('.skillstack_error').html(data.message);
                } else {
                    $('.skillstack_error').hide();
                    return {
                        results: data.result.map(function(skill) {
                            return {
                                id: skill.id,
                                text: skill.text
                            };
                        })
                    };
                }
            }
        }
    });
}
</script>
@endsection