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
                        <div class="row auto-crate-body">
                            <div class="col-md-6">
                                <select class="form-control" id="role" onchange="changeRoles(this.value)">
                                    <option value="">Select Role</option>
                                    @foreach($roles as $role)
                                    <option value="{{$role->id}}">{{$role->display_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                @include('maestro/common/language-switcher')
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <select id="user_type" name="user_type" class="form-control" style="display:none">
                                    <option value="" selected="">User type</option>
                                    <option value="0">Employee</option>
                                    <option value="1">Investor</option>
                                    <option value="2">Teacher</option>
                                    <option value="3">Job Seeker</option>
                                    <option value="4">Student</option>
                                    <option value="5">Recent Grad</option>
                                    <option value="6">Expert</option>
                                    <option value="7">Employee</option>
                                    <option value="9">Facilitator</option>
                                    <option value="10">Job Seeker</option>
                                    <option value="11">Startup</option>
                                    <option value="12">Learner</option>
                                    <option value="13">Mentor</option>
                                    <option value="14">Innovator</option>
                                    <option value="15">Aspiring Entrepreneur</option>
                                    <option value="16">Evaluator</option>
                                    <option value="17">Small</option>
                                    <option value="18">Intrapreneur</option>
                                    <option value="19">Ngo</option>
                                    <option value="20">Enterprise</option>
                                    <option value="21">Applicant</option>
                                    <option value="22">Educational</option>
                                    <option value="23">Community</option>
                                    <option value="24">Educator</option>
                                    <option value="25">Government</option>
                                    <option value="26">Others</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <select id="org_type" name="org_type" class="form-control select_role_type" style="display:none;">
                                    <option value="" selected>Org Manager Type</option>
                                    <option value="enterprise">Enterprise</option>
                                    <option value="small_mid_size_business">Small/Mid-size Business</option>
                                    <option value="startup">Startup</option>
                                    <option value="community_organization">Community Organization</option>
                                    <option value="ngo_charity_not_for_profit">NGO/Charity/Not-for-profit</option>
                                    <option value="government">Government</option>
                                    <option value="educational_institution">Educational Institution</option>
                                </select>
                                <span class="text-left help-block text-red">{{$errors->first('org_type')}}</span>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>

                        <div class="row" id='clonecheckbox_div' style="display:none;">
                            <div class="col-md-6 form-group">
                                <div class="form-check">
                                    <input class="form-check-input clonecheckbox" type="checkbox" id="clone_lab_chk">
                                    <label class="form-check-label" for="clone_lab_chk">&nbsp;&nbsp; Clone Lab</label>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <div class="form-check">
                                    <input class="form-check-input clonecheckbox" type="checkbox" id="clone_challenge_chk">
                                    <label class="form-check-label" for="clone_challenge_chk">&nbsp;&nbsp; Clone Challenge</label>
                                </div>
                            </div>

                        </div>
                        <div class="row" id="invite_users_div" style="display:none;">
                            <div class="col-md-6 form-group">
                                <div class="form-check">
                                    <input class="form-check-input invite_user_checkbox" type="checkbox" id="invite_lab_chk">
                                    <label class="form-check-label" for="invite_lab_chk">&nbsp;&nbsp; Add Users to Labs</label>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <div class="form-check">
                                    <input class="form-check-input invite_user_checkbox" type="checkbox" id="invite_challenge_chk">
                                    <label class="form-check-label" for="invite_challenge_chk">&nbsp;&nbsp; Add Users to Challenges</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class='col-md-6' id='lab_list_div' style="display:none;">
                                <div class='control-label mb-5'>Select Lab Template(s)</div>
                                <div class="form-group">
                                    <select id="lab_list" name="lab_list[]" multiple class="form-control select_role_type"  style="height: 50px;">

                                    </select>
                                </div>
                            </div>
                            <div class='col-md-6' id='challenge_list_div' style="display:none;">
                                <div class='control-label mb-5'>Select Challenge Template(s)</div>
                                <div class="form-group">
                                    <select id="challenge_list" name="challenge_list[]" multiple class="form-control" style="height: 50px;">
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class='col-md-6' id='lab_group_list_div' style="display:none;">
                                <div class='control-label mb-5'>Select Lab Program(s)</div>
                                <div class="form-group">
                                    <select id="lab_group_list" name="lab_group_list[]" multiple class="form-control" style="height: 50px;">
                                    </select>
                                </div>
                            </div>
                            <div class='col-md-6' id='challenge_group_list_div' style="display:none;">
                                <div class='control-label mb-5'>Select Challenge Path(s)</div>
                                <div class="form-group">
                                    <select id="challenge_group_list" name="challenge_group_list[]" multiple class="form-control" style="height: 50px;">
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

<script>
    @if(Session::has('success'))
        toastr.success("{{ Session::get('success') }}");
    @endif

    @if(Session::has('error'))
        toastr.error("{{ Session::get('error') }}");
    @endif
    var checkbox = document.getElementById('clone_lab_chk');
    var challengecheckbox = document.getElementById('clone_challenge_chk');
    checkbox.addEventListener('change', function() {
        if (checkbox.checked) {
            $('#lab_list_div').css('display','block');
            $('#lab_group_list_div').css('display','block');
            getData('lab_template','lab_list');

        } else {
            $('#lab_list_div').css('display','none');
            $('#lab_group_list_div').css('display','none');
        }
    });
    challengecheckbox.addEventListener('change', function() {
        if (challengecheckbox.checked) {
            $('#challenge_list_div').css('display','block');
            $('#challenge_group_list_div').css('display','block');
        } else {
            $('#challenge_list_div').css('display','none');
            $('#challenge_group_list_div').css('display','none');
        }
    });
    function getData(module,ids){
        try {
            $.ajax({
                type:'POST',
                url:"{{ route('getModuleList') }}",
                aysc:false,
                data:{
                    language: $('#language :selected').val(),
                    module:module,
                },
                success:function(response){
                    var toAppend = '';
                    $.each(response.result,function(index,title){
                        toAppend += '<option value='+title.id+' selected>'+title.text+'</option>';
                    });
                 //   $('#lab_list').append(toAppend);
                    $('#'.ids).append(toAppend);
                }
            });
        }catch (Exception $e) {
            return false;
        }
    }
    function changeRoles(role) {
        const elements = [
            '#org_type',
            '#clonecheckbox_div',
            '#lab_list_div',
            '#lab_group_list_div',
            '#challenge_list_div',
            '#challenge_group_list_div',
            '#invite_users_div',
            '#invite_lab_div',
            '#invite_challenge_div'
        ];
        // Hide all elements initially
        elements.forEach(el => $(el).css('display', 'none'));
        // Clear lists
        $('#lab_list').html('');
        $('#lab_group_list').html('');
        $('#challenge_group_list').html('');
        $('#challenge_list').html('');
        // Reset checkboxes
        $('#clone_lab_chk').prop('checked', false);
        $('#clone_challenge_chk').prop('checked', false);
        // Show the clonecheckbox_div by default
        $('#clonecheckbox_div').css('display', 'flex');
        $('#user_type').css('display', 'none');
        // Handle role-specific logic
        switch (role) {
            case "2":
                $('#org_type').css('display', 'block');
                break;
            case "1":
            case "3":
            case "4":
            case "5":
                // These roles only need the clonecheckbox_div which is already displayed
                break;
            case "6":
                $('#invite_users_div').css('display', 'flex');
                $('#user_type').css('display', 'flex');
                break;
            default:
                break;
        }
    }

</script>
@endsection
