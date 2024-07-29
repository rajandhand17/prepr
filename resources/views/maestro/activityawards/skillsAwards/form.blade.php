<div class ="row">
    
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
                 $lableName = $single->name . ' Name';
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
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('name')) ? 'has-error' : ''}}">
        {!! Form::label($inputName, $lableName, ['class' => 'control-label ']) !!}
        {!! Form::text($inputName, null, ['class' => 'form-control  ']) !!}
        <span class="help-block">{{ $errors->first('$inputName')}}</span>
    </div>
    <div class="clearfix"></div>
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
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('points')) ? 'has-error' : ''}}">
        {!! Form::label('Point', 'Points', ['class' => 'control-label ']) !!}
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
        @if (isset($award->image) && $award->image != "")
            <div class="pull-right">
                <img src="{{asset($award->image)}}" onerror="this.onerror=null;this.src='{{config(('site-settings.aws_url').'public/front/img/no-img.jpg')}}';" width="auto" height="50px">
            </div>
        @endif
        <span class="help-block">{{ $errors->first('image')}}</span>
    </div>
    <div class="clearfix"></div>
</div>
<div class="col-sm-6">
    <div class="mt-5 form-group {{($errors->has('skill')) ? 'has-error' : ''}}">
        {!! Form::label('skills', 'Skill', ['class' => 'control-label ']) !!}
        {!! Form::select('skill[]', $selectedSkills, array_keys($selectedSkills),  ['class' => 'form-control select2', 'multiple'=> 'multiple','id' => 'Skills']) !!}
        <span class="help-block skill_error">{{ $errors->first('skill')}}</span>
    </div>
</div>
<div class="col-sm-12 mt-20 mb-10"><h5>Conditions:</h5></div>
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
</div>

