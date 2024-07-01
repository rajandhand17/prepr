


@if($languages->count() > 0)
    @foreach($languages as $single)

        @php
            if ($single->iso == 'en') {
                $lableName = 'English Email Subject';
                $inputName = 'subject';

                $lableName2 = 'English Email Body';
                $inputName2 = 'body_content';
            } else {
                 $columName = $single->iso;
                 $lableName = $single->name . ' Email Subject';
                 $lableName2 = $single->name . ' Email Body';
                 if ($columName == trim($columName) && strpos($columName, ' ') !== false) {
                     $columName = str_replace(' ', '_', $columName);
                 }
                 if ($columName == trim($columName) && strpos($columName, '-') !== false) {
                     $columName = str_replace('-', '_', $columName);
                 }

                $inputName = $columName . '_subject';
                $inputName2 = $columName . '_body_content';
             }
        @endphp
        
<div class="row">
    <!-- <div class="col-md-4">
        <div class="form-group">
            <label class="control-label mb-10">
                Identifier
            </label>
            @if((strpos(Request::url(),'edit')) !== false )
                {!! Form::text('identifier', null, array('class' => 'form-control required', 'id' => 'eventRegInput1','readonly'=>'true')) !!}
            @else
                {!! Form::text('identifier', null, array('class' => 'form-control required', 'id' => 'eventRegInput1')) !!}
                <span class="help-block">You can not change identifier once you save this template</span>
            @endif
        </div>
    </div> -->
    <!-- <div class="col-md-2">
        <div class="form-group">
            <label class="control-label mb-10">
                Language
            </label>
            @if((strpos(Request::url(),'edit')) !== false )
            <select class="form-control" name="language">
                @if(!empty($languages))
                    @foreach($languages as $key => $lang)
                        <option value="{{ $lang->lang_iso }}" @if($lang->lang_iso == $template->language) selected @endif>{{ $lang->lang_name }}</option>
                    @endforeach
                @endif
            </select>
            @else
            <select class="form-control" name="language">
                @if(!empty($languages))
                    @foreach($languages as $key => $lang)
                        <option value="{{ $lang->lang_iso }}" >{{ $lang->lang_name }}</option>
                    @endforeach
                @endif
            </select>
            @endif
        </div>
    </div> -->
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label mb-10">
                {{$lableName}}
            </label>
            @if((strpos(Request::url(),'edit')) !== false )
            {!! Form::text( $inputName, null, array('class' => 'form-control required', 'id' =>  $inputName)) !!}
            @else
                {!! Form::text( $inputName, null, array('class' => 'form-control required', 'id' =>  $inputName)) !!}
            @endif
        </div>
    </div>
</div>

<div class="row">
    <!--/span-->
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label mb-10">
              {{ $lableName2 }}
            </label>
            {!! Form::textarea( $inputName2, null, array( 'id' =>  $inputName2 ,'style' => 'width:100%; height:500px','name' => $inputName2 )) !!}
            <script type="text/javascript">
                CKEDITOR.editorConfig = function (config) {
                    config.extraPlugins = 'filebrwoser,widget,clipboard,lineutils,widgetselection,html5video,video';
                    config.extraAllowedContent = 'article[*]';
                    config.allowedContent = true;
                    config.extraPlugins = 'imageuploader';
                    config.filebrowserBrowseUrl = "{{asset('back/plugins/ckfinder/ckfinder.html')}}";
                    config.filebrowserImageBrowseUrl = "{{asset('backend/plugins/ckfinder/ckfinder.html?type=Images')}}";
                    config.filebrowserUploadUrl = "{{asset('backend/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files')}}";
                    config.filebrowserImageUploadUrl = "{{route('ckeditor.upload', ['_token' => csrf_token() ])}}";
                };
                CKEDITOR.replace('content', {
                    filebrowserBrowseUrl: "{{asset('back/plugins/ckfinder_new/ckfinder.html')}}",
                    filebrowserUploadUrl: "{{route('ckeditor.upload', ['_token' => csrf_token() ])}}",
                });
            </script>
            
        </div>
    </div>
    <!--/span-->
</div>
@endforeach
@endif
<div class="row">
    <!--/span-->
    <div class="col-md-6">
        <div class=" form-group {{($errors->has('template_type')) ? 'has-error' : ''}}">
            {!! Form::label('template_type', 'Template Type', ['class' => 'control-label ']) !!}
            {!! Form::select('template_type', ['0'=>'Invitation'] , null , ['class' => 'form-control','placeholder' => 'Please select option']) !!}
            <span class="help-block">{{ $errors->first('template_type')}}</span>
        </div>
    </div>
    <div class="col-md-6">       
        <div class=" form-group {{($errors->has('module_type')) ? 'has-error' : ''}}">
            {!! Form::label('module_type', 'Component Type', ['class' => 'control-label ']) !!}
            {!! Form::select('module_type', ['0' => 'Organization' , '1' => 'Lab', '2' => 'Lab Program', '3' => 'Challenge', '4' => 'Challenge Path' , '5' => 'Project'] , null , ['class' => 'form-control','placeholder' => 'Please select option']) !!}
            <span class="help-block">{{ $errors->first('module_type')}}</span>
        </div>
    </div>
</div>
@section('pagelevelcssorjs')
    <script src="{{asset('adminassets/vendors/laravel-ckeditor/ckeditor.js')}}"></script>
@endsection
