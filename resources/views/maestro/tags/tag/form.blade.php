<link rel="stylesheet" type="text/css"
      href="https://cdnjs.cloudflare.com/ajax/libs/tagmanager/3.0.2/tagmanager.min.css">
<div class="row form-group ">
    <div class="col-sm-6 col-xs-6">

    
    <div class="form-group {{($errors->has('category')) ? 'has-error' : ''}}">
        {!! Form::label('category', 'Select category', ['class' => 'control-label']) !!}
        {!! Form::select('components[]', array('lab' => 'Lab', 'challenge' => 'Challenge', 'resource' => 'Resource'), $category, ['class' => 'form-control select2 ','multiple'=>'multiple','id'=>'']) !!}



        <!-- {!! Form::select('category[]', $category, old('category'), ['class' => 'form-control ','multiple'=>'multiple','id'=>'tag_multi_select2']) !!} -->
            <span class="help-block">{{ $errors->first('category')}}</span>
        </div>
        @if($languages->count() > 0)
            @foreach($languages as $single)

                @php
                    if ($single->iso == 'en') {
                        $lableName = 'English Tag';
                        $inputName = 'title';
                        $lableFName = 'Upload English Tag Image';
                        $inputFName = 'tag_image';
                    } else {
                         $columName = $single->iso;
                         $lableName = $single->name . ' Tag';
                         $lableFName = 'Upload '.$single->name.' Tag Image';
                         if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                             $columName = str_replace(' ', '_', $columName);
                         }
                         if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                             $columName = str_replace('-', '_', $columName);
                         }
                        $inputName = $columName . '_title';
                        $inputFName = $columName . '_tag_image';
                     }
                @endphp
                <div class="form-group {{($errors->has('tag')) ? 'has-error' : ''}}">
                    {!! Form::label($lableName, 'Enter '.$lableName, ['class' => 'control-label']) !!}
                    {!! Form::text($inputName,null, ['class' => 'form-control', 'id'=>'tag']) !!}
                    <span class="help-block">{{ $errors->first('tag')}}</span>
                </div>
                <div class="row form-group ">
                    <div class="col-sm-6 col-xs-6">
                        <div class="form-group {{($errors->has('tag_image')) ? 'has-error' : ''}}">
                            {!! Form::label($inputFName, $lableFName, ['class' => 'control-label custom-file-label ']) !!}
                            {!! Form::file($inputFName,null,['id'=>'fileUpload']) !!}
                            <span class="help-block">{{ $errors->first($inputFName)}}</span>
                        </div>
                        @if(isset($data[$inputFName]) && $data[$inputFName]!="")
                            <div class="pull-right trtrytr">
                                <img src="{{asset($data->$inputFName)}}" onerror="this.onerror=null;this.src='{{config(('site-settings.maestro_cdn_url').'public/front/img/no-img.jpg')}}' " width="auto" height="50px">
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif

    </div>
</div>
<script>
/* on change file upload function */
    $(document).on('change', '#fileUpload', function () {

        var fileExtension = ['jpeg', 'jpg', 'png'];
        if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
            $(this).next('.help-block').text("The tags image must be a file of type: jpeg, png, jpg");
            $(this).val('');
        } else {
            return true;

        }
    })
</script>