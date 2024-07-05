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
                    <li class="breadcrumb-item"><a href="{{ route('category.index') }}">Home</a></li>
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
                                    <option value="{{$role->name}}">{{$role->display_name}}</option>
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
                                    <select id="lab_list" name="lab_list[]" multiple class="form-control multi-select-search" style="height: 50px;">
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    @if(Session::has('success'))
        toastr.success("{{ Session::get('success') }}");
    @endif

    @if(Session::has('error'))
        toastr.error("{{ Session::get('error') }}");
    @endif

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
        // Hide all elements
        elements.forEach(el => $(el).css('display', 'none'));
        if (role === "organization_owner" || role === "organization_manager") {
            $('#org_type').css('display','block');
            $('#clonecheckbox_div').css('display','flex');
        }
        if(role === "lab_manager" || role==="resource_manager" || role==="challenge_manager"){
            $('#clonecheckbox_div').css('display','flex');
        }
        if(role === 'super_admin'){
            $('#clonecheckbox_div').css('display','flex');
            $('#invite_users_div').css('display','flex');
            $('#lab_list_div').css('display','block');
            $('#challenge_list_div').css('display','block');
            $('#lab_group_list_div').css('display','block');
            $('#challenge_group_list_div').css('display','block');
        }

        if(role === 'user'){
            $('#clonecheckbox_div').css('display','flex');
            $('#invite_users_div').css('display','flex');
            $('#user_type').css('display','flex');
        }
    }
    // Get the checkbox element
    var checkbox = document.getElementById('clone_lab_chk');
    var challengecheckbox = document.getElementById('clone_challenge_chk');

    // Add an event listener for the 'change' event
    checkbox.addEventListener('change', function() {
        // Check if the checkbox is checked
        if (checkbox.checked) {
            $('#lab_list_div').css('display','block');
            $('#lab_group_list_div').css('display','block');
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
</script>
@endsection
