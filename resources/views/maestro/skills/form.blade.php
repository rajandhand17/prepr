<link rel="stylesheet" type="text/css"
      href="https://cdnjs.cloudflare.com/ajax/libs/tagmanager/3.0.2/tagmanager.min.css">
<div class="row form-group ">
    <div class="col-sm-6 col-xs-6">

        @if($languages->count() > 0)
            @foreach($languages as $single)

                @php
                    if ($single->iso == 'en') {
                        $lableName = 'English Skill';
                        $inputName = 'title';
                    } else {
                         $columName = $single->iso;
                         $lableName = $single->name . ' Skill';
                         if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                             $columName = str_replace(' ', '_', $columName);
                         }
                         if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                             $columName = str_replace('-', '_', $columName);
                         }
                        $inputName = $columName . '_title';
                     }
                @endphp
                <div class="form-group {{($errors->has('skill')) ? 'has-error' : ''}}">
                    {!! Form::label($lableName, 'Enter '.$lableName, ['class' => 'control-label']) !!}
                    {!! Form::text($inputName,null, ['class' => 'form-control', 'id'=>'skill']) !!}
                    <span class="help-block">{{ $errors->first('skill')}}</span>
                </div>
            @endforeach
        @endif

    </div>
</div>

