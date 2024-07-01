

@if($languages->count() > 0)
    @foreach($languages as $single)

        @php
            if ($single->iso == 'en') {
                $lableName = 'English Name';
                $inputName = 'name';

                $lableName2 = 'Trophy Description';
                $inputName2 = 'description';
            } else {
                 $columName = $single->iso;
                 $lableName = $single->name . ' English Name';
                 $lableName2 = $single->name . ' Trophy Description';
                 if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                     $columName = str_replace(' ', '_', $columName);
                 }
                 if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                     $columName = str_replace('-', '_', $columName);
                 }

                $inputName = $columName . '_name';
                $inputName2 = $columName . '_description';
             }
        @endphp
<div class ="row">
        <div class="col-md-6 col-xs-12">
            <div class="form-group {{($errors->has('title')) ? 'has-error' : ''}}">
                {!! Form::label($inputName, $lableName, ['class' => 'control-label ']) !!}
                {!! Form::text($inputName, @$group->$inputName, ['class' => 'form-control  ']) !!}
                <span class="help-block">{{ $errors->first($inputName)}}</span>
            </div>
        </div>
        <div class="col-md-6 col-xs-12">
            <div class="form-group {{($errors->has('description')) ? 'has-error' : ''}}">
                {!! Form::label($inputName2, $lableName2, ['class' => 'control-label ']) !!}
                {!! Form::textarea($inputName2, @$group->$inputName2, ['class' => 'form-control  ','rows'=>'4']) !!}
                <span class="help-block">{{ $errors->first($inputName2)}}</span>
            </div>
        </div>
</div>
    @endforeach
@endif
<div class = "row">
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('points')) ? 'has-error' : ''}}">
        {!! Form::label('Point', 'Point', ['class' => 'control-label ']) !!}
        {!! Form::number('points', null, ['class' => 'form-control  ', 'id' => 'total_points', 'min'=> 0, 'style' => 'background-color: unset;']) !!}
        <span class="help-block"></span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('image')) ? 'has-error' : ''}}">
        {!! Form::label('image', 'Image', ['class' => 'control-label ']) !!}
        <div class="clearfix"></div>
        <div class="pull-left">
            {!! Form::file('image',null, ['class' => 'form-control  ']) !!}
        </div>
        @if(isset($trophy->image) && $trophy->image!="")
            <div class="pull-right">
                <img src="{{asset($trophy->image)}}" onerror="this.onerror=null;this.src='{{config(('site-settings.aws_url').'public/front/img/no-img.jpg')}}';" width="auto" height="50px">
            </div>
        @endif
        <span class="help-block">{{ $errors->first('image')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<div class="col-sm-12 mt-20 mb-10"><h5>Conditions For Award:</h5></div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('create_project_point')) ? 'has-error' : ''}}">
        {!! Form::label('create_project_point', '# Projects Created', ['class' => 'control-label ']) !!}
        {!! Form::number('create_project_point',null, ['class' => 'form-control  ', 'id' => 'create_project_point', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('create_project_point')}}</span>
    </div>
    <div class="clearfix"></div>
</div>

<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('add_member_point')) ? 'has-error' : ''}}">
        {!! Form::label('add_member_point', '# Team Members Added', ['class' => 'control-label ']) !!}
        {!! Form::number('add_member_point',null, ['class' => 'form-control  ', 'id' => 'add_member_point', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('add_member_point')}}</span>
    </div>
    <div class="clearfix"></div>
</div>

<div class="clearfix"></div>


<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('create_chat_point')) ? 'has-error' : ''}}">
        {!! Form::label('create_chat_point', '# Threads Posted', ['class' => 'control-label ']) !!}
        {!! Form::number('create_chat_point',null, ['class' => 'form-control  ', 'id' => 'create_chat_point', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('create_chat_point')}}</span>
    </div>
    <div class="clearfix"></div>
</div>

<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('reply_chat_point')) ? 'has-error' : ''}}">
        {!! Form::label('reply_chat_point', '# Times Responded to a thread', ['class' => 'control-label ']) !!}
        {!! Form::number('reply_chat_point',null, ['class' => 'form-control  ', 'id' => 'reply_chat_point', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('reply_chat_point')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('submit_project_point')) ? 'has-error' : ''}}">
        {!! Form::label('submit_project_point', '# Projects Submitted', ['class' => 'control-label ']) !!}
        {!! Form::number('submit_project_point',null, ['class' => 'form-control  ', 'id' => 'submit_project_point', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('submit_project_point')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('vote_project_point')) ? 'has-error' : ''}}">
        {!! Form::label('vote_project_point', '# Project Votes Received', ['class' => 'control-label ']) !!}
        {!! Form::number('vote_project_point',null, ['class' => 'form-control  ', 'id' => 'vote_project_point', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('vote_project_point')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('fb_point')) ? 'has-error' : ''}}">
        {!! Form::label('fb_point', '# Times Facebook Connected', ['class' => 'control-label ']) !!}
        {!! Form::number('fb_point',null, ['class' => 'form-control  ', 'id' => 'fb_point', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('fb_point')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('google_point')) ? 'has-error' : ''}}">
        {!! Form::label('google_point', '# Times Google Connected', ['class' => 'control-label ']) !!}
        {!! Form::number('google_point',null, ['class' => 'form-control  ', 'id' => 'google_point', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('google_point')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('linked_point')) ? 'has-error' : ''}}">
        {!! Form::label('linked_point', '# Times LinkedIn Connected', ['class' => 'control-label ']) !!}
        {!! Form::number('linked_point',null, ['class' => 'form-control  ', 'id' => 'linked_point', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('linked_point')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('login_point')) ? 'has-error' : ''}}">
        {!! Form::label('login_point', '# Times Logged In', ['class' => 'control-label ']) !!}
        {!! Form::number('login_point',null, ['class' => 'form-control  ', 'id' => 'login_point', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('login_point')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="clearfix"></div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('join_lab_point')) ? 'has-error' : ''}}">
        {!! Form::label('join_lab_point', '# Times Joined a Lab', ['class' => 'control-label ']) !!}
        {!! Form::number('join_lab_point',null, ['class' => 'form-control  ', 'id' => 'join_lab_point', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('join_lab_point')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('success_submit_project_point')) ? 'has-error' : ''}}">
        {!! Form::label('success_submit_project_point', '# Successful Project Submission', ['class' => 'control-label ']) !!}
        {!! Form::number('success_submit_project_point',null, ['class' => 'form-control  ', 'id' => 'success_submit_project_point', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('success_submit_project_point')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('challenge_participation_awards')) ? 'has-error' : ''}}">
        {!! Form::label('challenge_participation_awards', '# Challenge Participation Awards', ['class' => 'control-label ']) !!}
        {!! Form::number('challenge_participation_awards',null, ['class' => 'form-control  ', 'id' => 'challenge_participation_awards', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('challenge_participation_awards')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('challenge_win_awards')) ? 'has-error' : ''}}">
        {!! Form::label('challenge_win_awards', '# Challenge Win Awards', ['class' => 'control-label ']) !!}
        {!! Form::number('challenge_win_awards',null, ['class' => 'form-control  ', 'id' => 'challenge_win_awards', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('challenge_win_awards')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('challenge_path_awards')) ? 'has-error' : ''}}">
        {!! Form::label('challenge_path_awards', '# Challenge Path Awards', ['class' => 'control-label ']) !!}
        {!! Form::number('challenge_path_awards',null, ['class' => 'form-control  ', 'id' => 'challenge_path_awards', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('challenge_path_awards')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('lab_program_awards')) ? 'has-error' : ''}}">
        {!! Form::label('lab_program_awards', '# Lab Program Awards', ['class' => 'control-label ']) !!}
        {!! Form::number('lab_program_awards',null, ['class' => 'form-control  ', 'id' => 'lab_program_awards', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('lab_program_awards')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('resource_group_awards')) ? 'has-error' : ''}}">
        {!! Form::label('resource_group_awards', '# Resource Group Awards', ['class' => 'control-label ']) !!}
        {!! Form::number('resource_group_awards',null, ['class' => 'form-control  ', 'id' => 'resource_group_awards', 'min'=>'0']) !!}
        <span class="help-block">{{ $errors->first('resource_group_awards')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="clearfix"></div>

<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('criteria')) ? 'has-error' : ''}}">
        {!! Form::label('criteria', 'Criteria', ['class' => 'control-label ']) !!}
        {!! Form::text('criteria',null, ['class' => 'form-control']) !!}
        <span class="help-block">{{ $errors->first('criteria')}}</span>
    </div>
</div>

<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('badge_type')) ? 'has-error' : ''}}">
        {!! Form::label('badge_type', 'Badge Type', ['class' => 'control-label ']) !!}
{{--        {!! Form::select('badge_type', ['Points-Based'=>'Points-Based','Awarded'=>'Awarded','Challenge-Based'=>'Challenge-Based'] , null , ['class' => 'form-control','placeholder' => 'Please select option']) !!}--}}
        {!! Form::select('badge_type', ['Points-Based'=>'Points-Based'] , null , ['class' => 'form-control','placeholder' => 'Please select option']) !!}
        <span class="help-block">{{ $errors->first('badge_type')}}</span>
    </div>
</div>

<div class="clearfix"></div>
</div>
