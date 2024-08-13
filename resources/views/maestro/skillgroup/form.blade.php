<link rel="stylesheet" type="text/css"
      href="https://cdnjs.cloudflare.com/ajax/libs/tagmanager/3.0.2/tagmanager.min.css">
<div class="row form-group ">
    @if($languages->count() > 0)
        @foreach($languages as $single)

            @php
                if ($single->iso == 'en') {
                    $lableName = 'Group Title';
                    $inputName = 'title';

                    $lableName2 = 'Group Description';
                    $inputName2 = 'description';
                } else {
                     $columName = $single->iso;
                     $lableName = $single->name . ' Group Title';
                     $lableName2 = $single->name . ' Group Description';
                     if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                         $columName = str_replace(' ', '_', $columName);
                     }
                     if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                         $columName = str_replace('-', '_', $columName);
                     }

                    $inputName = $columName . '_title';
                    $inputName2 = $columName . '_description';
                 }
            @endphp

            <div class="col-md-6 col-xs-6">
                <div class="form-group {{($errors->has('title')) ? 'has-error' : ''}}">
                    {!! Form::label($inputName, $lableName, ['class' => 'control-label']) !!}
                    {!! Form::text($inputName, @$group->$inputName, ['class' => 'form-control']) !!}
                    <span class="help-block">{{ $errors->first($inputName)}}</span>
                </div>
            </div>

            <div class="col-md-6 col-xs-12">
                <div class="form-group {{($errors->has('description')) ? 'has-error' : ''}}">
                    {!! Form::label($inputName2, $lableName2, ['class' => 'control-label']) !!}
                    {!! Form::textarea($inputName2, @$group->$inputName2, ['class' => 'form-control  ','rows'=>'4']) !!}
                    <span class="help-block">{{ $errors->first($inputName2)}}</span>
                </div>
            </div>
        @endforeach
    @endif

    <div class="col-md-6 col-xs-12">
        <div class="form-group {{($errors->has('group_skills')) ? 'has-error' : ''}}">
            {!! Form::label('group-skills', 'Group Skills', ['class' => 'control-label']) !!}
            {!! Form::select('group_skills[]', $selectedSkills, array_keys($selectedSkills), ['class' => 'form-control select2', 'multiple'=> 'multiple','id' => 'Skills']) !!}
            <span class="help-block skill_error">{{ $errors->first('group_skills')}}</span>
        </div>
    </div>
    <div class="col-md-6 col-xs-12">
        <div class="form-group {{($errors->has('group_stacks')) ? 'has-error' : ''}}">
            {!! Form::label('group-stacks', 'Group Stacks', ['class' => 'control-label']) !!}
            {!! Form::select('group_stacks[]', $selectedStacks, array_keys($selectedStacks), ['class' => 'form-control select2', 'multiple'=> 'multiple','id' => 'SkillStack']) !!}
            <span class="help-block skillstack_error">{{ $errors->first('group_stacks')}}</span>
        </div>
    </div>
</div>

