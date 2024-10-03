@extends('maestro.layouts.default')
@section('title', 'Auto Create Template')
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Auto Create Template</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('auto-create.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Auto Create Template</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="panel-body">
                            <div class='row'>
                                <div class='col-md-6 form-group '>
                                    <select class='form-control' id='role_list' name="non_drop">
                                        <option value="">Select Role</option>
                                        <option value="organization_owner" name='organization_owner'>Organization Owner</option>
                                        <option value="organization_manager" name= 'organization_manager'>Organization Manager</option>
                                        <option value="lab_manager" name= 'lab_manager'>Lab Manager</option>
                                        <option value="challenge_manager" name= 'challenge_manager'>Challenge Manager</option>
                                        <option value="resource_manager" name= 'resource_manager'>Resource Manager</option>
                                        <option value="user" name= 'user'>User</option>
                                    </select>
                                </div>
                                <div class='col-md-6 form-group '>
                                    @php
                                        $languages = \App\Models\Language::where('status', 1)->get();
                                    @endphp

                                    <select class='form-control' id='language' name="language">
                                        @if($languages)
                                            @foreach($languages as $key => $language)
                                                <option value="{{$language->iso}}" name='{{$language->iso}}'>{{$language->name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class='col-md-6 form-group '>
                                    <div class="organization_manager list">
                                         <select id="organisation_type" name="organisation_type" class="form-control select_role_type">
                                                <option value="" selected>Org Manager Type</option>
                                                <option value="enterprise">Enterprise</option>
                                                <option value="small_mid_size_business">Small/Mid-size Business</option>
                                                <option value="startup">Startup</option>
                                                <option value="community_organization">Community Organization</option>
                                                <option value="ngo_charity_not_for_profit">NGO/Charity/Not-for-profit</option>
                                                <option value="government">Government</option>
                                                <option value="educational_institution">Educational Institution</option>
                                            </select>
                                            <span class="text-left help-block text-red">{{$errors->first('organisation_type')}}</span>
                                    </div>
                                
                                    <div class='col-md-9'>
                                        <div class="user list">
                                            <select id="user_type" name="user_type" class="form-control" style="height: 50px">
                                                <option value="" selected>User type</option>
                                                <option value="student">Student</option>
                                                <option value="applicant">Applicant</option>
                                                <option value="teacher">Teacher</option>
                                                <option value="facilitator">Facilitator</option>
                                                <option value="mentor">Mentor</option>
                                                <option value="expert">Expert</option>
                                                <option value="recent_grad">Recent Grad</option>
                                                <option value="employee">Employee</option>
                                                <option value="employer">Employer</option>
                                                <option value="investor">Investor</option>
                                                <option value="job_seeker">Job Seeker</option>
                                                <option value="intrapreneur">Intrapreneur</option>
                                                <option value="aspiring_entrepreneur">Aspiring Entrepreneur</option>
                                                <option value="learner">Learner</option>
                                                <option value="innovator">Innovator</option>
                                                <option value="startup">Startup</option>
                                                <option value="evaluator">Evaluator</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style='display:none' id='clonecheckbox_div'>
                                <div class="col-md-6 form-group">
                                    <div class="form-check">
                                        <input class="form-check-input clonecheckbox" type="checkbox" id="clone_lab_chk">
                                        <label class="form-check-label" for="clone_lab_chk">&nbsp;&nbsp;
                                        Clone Lab
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <div class="form-check">
                                        <input class="form-check-input clonecheckbox" type="checkbox" id="clone_challenge_chk">
                                        <label class="form-check-label" for="clone_challenge_chk">&nbsp;&nbsp;
                                        Clone Challenge
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style= 'display:none' id='invite_users_div'>
                                <div class="col-md-6 form-group" style='display:none' id='invite_lab_div'>
                                    <div class="form-check">
                                        <input class="form-check-input invite_user_checkbox" type="checkbox" id="invite_lab_chk">
                                        <label class="form-check-label" for="invite_lab_chk">&nbsp;&nbsp;
                                        Add Users to Labs
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <div class="form-check" style='display:none' id='invite_challenge_div'>
                                        <input class="form-check-input invite_user_checkbox" type="checkbox" id="invite_challenge_chk">
                                        <label class="form-check-label" for="invite_challenge_chk">&nbsp;&nbsp;
                                        Add Users to Challenges
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class='col-md-6' id='lab_list_div' style="display:none">
                                    <div class= 'control-label'>Select Lab Template(s)</div>
                                    <div class="form-group">
                                        <select id="lab_list" name="lab_list[]" multiple class="form-control multi-select-search" style="height: 50px">
                                      </select>
                                    </div>
                                </div>
                                <div class='col-md-6' id='challenge_list_div' style="display:none">
                                    <div class= 'control-label'>Select Challenge Template(s)</div>
                                    <div class="form-group">
                                        <select id="challenge_list" name="challenge_list[]" multiple class="form-control" style="height: 50px">
                                      </select>

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class='col-md-6' id='lab_group_list_div' style="display:none">
                                    <div class= 'control-label'>Select Lab Program(s)</div>
                                    <div class="form-group">
                                        <select id="lab_group_list" name="lab_group_list[]" multiple class="form-control" style="height: 50px">
                                      </select>

                                    </div>
                                </div>

                                <div class='col-md-6' id='challenge_group_list_div' style="display:none">
                                    <div class= 'control-label'>Select Challenge Path(s)</div>
                                    <div class="form-group">
                                        <select id="challenge_group_list" name="challenge_group_list[]" multiple class="form-control" style="height: 50px">
                                      </select>

                                    </div>
                                </div>
                            </div>
                    </div>
                    <button type="button" class="btn btn-success clonebtn">Save</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="cloneModal" tabindex="-1" role="dialog" aria-labelledby="cloneModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="cloneModalLabel">Info..!</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            Are you sure you want to update the details ?
        </div>
        <div class="modal-footer">
            {!!Form::open(array('method'=>'POST','route'=>'createUpdate'))!!}
            <input type='hidden' id='sdc_lab' name='sdc_lab' value=''/>
            <input type='hidden' id='sdc_challenge' name='sdc_challenge' value=''/>
            <input type='hidden' id='selected_role' name='selected_role' value=''/>
            <input type='hidden' id='role_user_type_slected' name='role_user_type_slected' value=''/>
            <input type='hidden' id='selected_lab_ids' name='selected_lab_ids' value=''/>
            <input type='hidden' id='selected_challenge_ids' name='selected_challenge_ids' value=''/>
            <input type='hidden' id='selected_group_challenge_ids' name='selected_group_challenge_ids' value=''/>
            <input type='hidden' id='selected_group_lab_ids' name='selected_group_lab_ids' value=''/>
            <input type='hidden' id='invite_lab' name='invite_lab' value=''/>
            <input type='hidden' id='invite_challenge' name='invite_challenge' value=''/>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <input type='hidden' id='selected_language' name='selected_language' value=''/>
            <button type="submit" class="btn btn-primary">Save</button>
            {!!Form::close()!!}
        </div>
        </div>
    </div>
</div>

@stop
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> --}}
@section('scripts')
<script>
    @if(Session::has('success'))
        toastr.success("{{ Session::get('success') }}");
    @endif

    @if(Session::has('error'))
        toastr.error("{{ Session::get('error') }}");
    @endif
</script>

<script tyoe='text/javascript'>

$(document).ready(function(){

    $('#lab_list').select2({
        placeholder: "Select Option",
        ajax: {
        url: '{{route("fetchLabList")}}',
                data: function (params) {
                return {
                    search: params.term,
                    language : $('#language :selected').val()
                };
                },
                processResults: function (data) {
                return {
                results: data.result
                };
                }
        }

    });

    $('#challenge_list').select2({
        placeholder: "Select Option",
        ajax: {
        url: '{{route("fetchChallengeList")}}',
                data: function (params) {
                return {
                search: params.term,
                    language : $('#language :selected').val()
                };
                },
                processResults: function (data) {
                return {
                results: data.result
                };
                }
        }
    });

    $('#challenge_group_list').select2({
        placeholder: "Select Option",
        ajax: {
        url: '{{route("fetchChallengeGroupList")}}',
                data: function (params) {
                return {
                search: params.term,
                    language : $('#language :selected').val()
                };
                },
                processResults: function (data) {
                return {
                results: data.result
                };
                }
        }
    });

    $('#lab_group_list').select2({
        placeholder: "Select Option",
        ajax: {
        url: '{{route("fetchLabGroupList")}}',
                data: function (params) {
                return {
                    search: params.term,
                    language : $('#language :selected').val()
                };
                },
                processResults: function (data) {
                return {
                results: data.result
                };
                }
        }
    });


    $("#role_list").change(function(){
        $('#clone_lab_chk').prop('checked',false);
        $('#clone_challenge_chk').prop('checked',false);
        $("#organisation_type option:selected").removeAttr("selected");
        $(this).find("option:selected").each(function(){
            role_selected= $(this).val()
            $('#lab_list_div').css('display','none')
            $('#lab_group_list_div').css('display','none')
            $('#challenge_list_div').css('display','none')
            $('#challenge_group_list_div').css('display','none')
            // if(role_selected === 'free_lab_manager' ){
            //     $('#clone_challenge_chk_div').css('display','none');
            //     $('#invite_challenge_div').css('display','none');
            // }
            var optionValue = $(this).attr("name");
            if(optionValue){
                $(".list").not("." + optionValue).hide();
                $("." + optionValue).show();
            } else{
                $(".list").hide();
            }


        });
    }).change();

    $("#role_list").change(function(){
        $('#clone_lab_chk').prop('checked',false);
        $('#clone_challenge_chk').prop('checked',false);
        $("#org_type option:selected").removeAttr("selected");
        $(this).find("option:selected").each(function(){
            role_selected= $(this).val()
            $('#lab_list_div').css('display','none')
            $('#lab_group_list_div').css('display','none')
            $('#challenge_list_div').css('display','none')
            $('#challenge_group_list_div').css('display','none')
            // if(role_selected === 'free_lab_manager' ){
            //     $('#clone_challenge_chk_div').css('display','none');
            //     $('#invite_challenge_div').css('display','none');
            // }
            var optionValue = $(this).attr("name");
            if(optionValue){
                $(".list").not("." + optionValue).hide();
                $("." + optionValue).show();
            } else{
                $(".list").hide();
            }


        });
    }).change();

    role_type_selected= '';
    $('#role_list').change(function(){
        $(".non_drop option:selected").removeAttr("selected");
         role_type_selected= $( ".non_drop option:selected" ).val()
         if(role_type_selected!= ''){
             $('#clonecheckbox_div').css('display','block')
             getPreSelectLabList(role_selected, role_type_selected);
             getPreSelectChallengeList(role_selected, role_type_selected);
             getPreSelectChallengeGroupList(role_selected, role_type_selected);
             getPreSelectLabGroupList(role_selected, role_type_selected);
         }
    })
    $('#lab_type').change(function(){
         role_type_selected= $( "#lab_type option:selected" ).val()
         if(role_type_selected!= ''){
             $('#clonecheckbox_div').css('display','block')
             getPreSelectLabList(role_selected, role_type_selected);
             getPreSelectChallengeList(role_selected, role_type_selected);
             getPreSelectChallengeGroupList(role_selected, role_type_selected);
             getPreSelectLabGroupList(role_selected, role_type_selected);
         }
    })
    $('#organisation_type').change(function(){
         role_type_selected= $( "#organisation_type option:selected" ).val()
         if(role_type_selected!= ''){
             $('#clonecheckbox_div').css('display','block')
             getPreSelectLabList(role_selected, role_type_selected);
             getPreSelectChallengeList(role_selected, role_type_selected);
             getPreSelectChallengeGroupList(role_selected, role_type_selected);
             getPreSelectLabGroupList(role_selected, role_type_selected);
         }
    });
    $('#org_type').change(function(){
         role_type_selected= $( "#org_type option:selected" ).val()
         if(role_type_selected!= ''){
             $('#clonecheckbox_div').css('display','block')
             getPreSelectLabList(role_selected, role_type_selected);
             getPreSelectChallengeList(role_selected, role_type_selected);
             getPreSelectChallengeGroupList(role_selected, role_type_selected);
             getPreSelectLabGroupList(role_selected, role_type_selected);
         }
    });
    $('#language').change(function(){
        $('#clonecheckbox_div').css('display','block')
        getPreSelectLabList(role_selected, role_type_selected);
        getPreSelectChallengeList(role_selected, role_type_selected);
        getPreSelectChallengeGroupList(role_selected, role_type_selected);
        getPreSelectLabGroupList(role_selected, role_type_selected);
    });
    $('#user_type').change(function(){
         role_type_selected= $( "#user_type option:selected" ).val()
         if(role_type_selected!= ''){
             $('#clonecheckbox_div').css('display','block')
             getPreSelectLabList(role_selected, role_type_selected);
             getPreSelectChallengeList(role_selected, role_type_selected);
             getPreSelectChallengeGroupList(role_selected, role_type_selected);
             getPreSelectLabGroupList(role_selected, role_type_selected);
         }


    });

    $('#clone_lab_chk').click(function(){
        role_selected= $( "#role_list option:selected" ).val();
        clone_challenge= false;
        if ($('#clone_lab_chk').is(':checked')) {
            clone_lab= true;
            $('#lab_list_div').css('display','block')
            $('#lab_group_list_div').css('display','block');
            $('#invite_users_div').css('display','block');
            $('#invite_lab_div').css('display','block');

        }else{
            clone_lab= false;
            $('#lab_list_div').css('display','none')
            $('#lab_group_list_div').css('display','none')
           // $('#invite_users_div').css('display','none')
           // $('#invite_lab_chk').css('display','none')
        }
        if(role_selected!= '' && role_type_selected!='')
        {
            //getPreSelectLabList(role_selected, role_type_selected);

        }

    })
    invite_lab= 0;
    invite_challenge= 0;

    $('#clone_challenge_chk').click(function(){
        role_selected= $( "#role_list option:selected" ).val();
        if ($('#clone_challenge_chk').is(':checked')) {
            clone_challenge= true;
            $('#challenge_list_div').css('display','block')
            $('#challenge_group_list_div').css('display','block')
            $('#invite_users_div').css('display','block');
            $('#invite_challenge_div').css('display','block');
        }else{
            clone_challenge= false;
            $('#challenge_list_div').css('display','none')
            $('#challenge_group_list_div').css('display','none')
           // $('#invite_users_div').css('display','none');
           // $('#invite_challenge_chk').css('display','none');
        }

        if(role_selected!= '' && role_type_selected!='')
        {
           // getPreSelectChallengeList(role_selected, role_type_selected);

        }
    })

    $('.clonebtn').click(function(){
       selected_labs= $("#lab_list").select2("val")
       selected_challenges= $("#challenge_list").select2("val")
       selected_group_challenges= $("#challenge_group_list").select2("val")
       selected_group_lab_ids= $("#lab_group_list").select2("val")
       if ($('#clone_lab_chk').is(':checked')) {
            clone_lab= true;
            $('#lab_list_div').css('display','block')
            $('#lab_group_list_div').css('display','block')
        }else{
            clone_lab= false;
            $('#lab_list_div').css('display','none')
            $('#lab_group_list_div').css('display','none')
        }

        if ($('#clone_challenge_chk').is(':checked')) {
            clone_challenge= true;
            $('#challenge_list_div').css('display','block')
            $('#challenge_group_list_div').css('display','block')
        }else{
            clone_challenge= false;
            $('#challenge_list_div').css('display','none')
            $('#challenge_group_list_div').css('display','none')
        }
        if ($('#invite_lab_chk').is(':checked')) {
            invite_lab= 1;
        }
        if ($('#invite_challenge_chk').is(':checked')) {
                invite_challenge= 1;
        }
        $('#sdc_lab').val(clone_lab);
        $('#sdc_challenge').val(clone_challenge);
        $('#selected_role').val(role_selected);
        $('#role_user_type_slected').val(role_type_selected);
        $('#selected_lab_ids').val(selected_labs);
        $('#selected_challenge_ids').val(selected_challenges);
        $('#selected_group_challenge_ids').val(selected_group_challenges);
        $('#selected_group_lab_ids').val(selected_group_lab_ids);
        $('#invite_lab').val(invite_lab);
        $('#invite_challenge').val(invite_challenge);
        $('#selected_language').val($('#language :selected').val());

       colenchecked_len= $('.clonecheckbox:checked').length
        if(colenchecked_len>0){

        }
        $('#cloneModal').modal('show');
    })




});

function getPreSelectLabList(role_selected, role_type_selected)
{
    $.ajax({
            type:'POST',
            url:"{{ route('getPreSelectLabList') }}",
            data:{role_selected:role_selected, role_type_selected:role_type_selected,
                language : $('#language :selected').val()},
            success:function(respoonse){
            if(respoonse.result!= ''){
                $('#clone_lab_chk').prop('checked',true);
                $('#lab_list_div').css('display','block')
                $('#lab_group_list_div').css('display','block')

                if(respoonse.invite_info.invite_labs== '1'){
                    $('#invite_lab_chk').prop('checked',true);
                    $('#invite_users_div').css('display','block');
                    $('#invite_lab_div').css('display','block');

                }else{
                    $('#invite_lab_chk').prop('checked',false);
                    $('#invite_users_div').css('display','block');
                    $('#invite_lab_div').css('display','block');
                }

            }else{
                $('#clone_lab_chk').prop('checked',false);
                $('#lab_list_div').css('display','none')

            }

                $('#lab_list').html('');
            var toAppend = '';
            $.each(respoonse.result,function(index,title){
                toAppend += '<option value='+title.id+' selected>'+title.text+'</option>';
            });

            $('#lab_list').append(toAppend);


            }
    });
}

function getPreSelectLabGroupList(role_selected, role_type_selected)
{
    $.ajax({
            type:'POST',
            url:"{{ route('getPreSelectLabGroupList') }}",
            data:{role_selected:role_selected, role_type_selected:role_type_selected,
                language : $('#language :selected').val()},
            success:function(respoonse){
            if(respoonse.result!= ''){
                $('#clone_lab_chk').prop('checked',true);
                $('#lab_group_list_div').css('display','block')
            }
                $('#lab_group_list').html('');
            var toAppend = '';
            $.each(respoonse.result,function(index,title){
                toAppend += '<option value='+title.id+' selected>'+title.text+'</option>';
            });

            $('#lab_group_list').append(toAppend);


            }
    });
}
function  getPreSelectChallengeList(role_selected, role_type_selected){

$.ajax({
        type:'POST',
        url:"{{ route('getPreSelectedChallengeList') }}",
        data:{role_selected:role_selected, role_type_selected:role_type_selected,
            language : $('#language :selected').val()},
        success:function(respoonse) {
            $('#challenge_list').html('');
            var toAppend = '';
            if(respoonse.result!= '') {
                 $('#clone_challenge_chk').prop('checked',true);
                 $('#challenge_list_div').css('display','block')
                 $('#challenge_group_list_div').css('display','block');

                 if (respoonse.invite_info.invite_challenges== '1'){
                    $('#invite_challenge_chk').prop('checked',true);
                    $('#invite_users_div').css('display','block');
                    $('#invite_challenge_div').css('display','block');
                    $('#clone_challenge_chk_div').css('display','block');

                } else {
                    $('#invite_challenge_chk').prop('checked',false);
                    $('#invite_users_div').css('display','block');
                    $('#invite_challenge_div').css('display','block');
                     $('#clone_challenge_chk_div').css('display','block');

                }

            } else {
                $('#clone_challenge_chk').prop('checked',false);
                $('#challenge_list_div').css('display','none');
                $('#challenge_group_list_div').css('display','none');
            }
            $.each(respoonse.result,function(index,title){
                toAppend += '<option value='+title.id+' selected>'+title.text+'</option>';
            });
            $('#challenge_list').append(toAppend);



    }
});

}
function  getPreSelectChallengeGroupList(role_selected, role_type_selected){

    $.ajax({
            type:'POST',
            url:"{{ route('getPreSelectChallengeGroupList') }}",
            data:{role_selected:role_selected, role_type_selected:role_type_selected,
                language : $('#language :selected').val()},
            success:function(respoonse){
                $('#challenge_group_list').html('');
                var toAppend = '';
                if(respoonse.result!= ''){
                    $('#clone_challenge_chk').prop('checked',true);
                    $('#challenge_group_list_div').css('display','block')
                }
                $.each(respoonse.result,function(index,title){
                    toAppend += '<option value='+title.id+' selected>'+title.text+'</option>';
                });

                $('#challenge_group_list').append(toAppend);


            }
    });

}

</script>

@endsection
