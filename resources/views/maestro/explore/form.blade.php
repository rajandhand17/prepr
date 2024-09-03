<style>
    .dm-uploader {
        border: 2px dashed #575757;
        padding: 5px 5px 15px;
    }
    .bluebox {
	color: white;
	border-radius: 10px;
	background: var(--Info-900, #0A2B77);
	box-shadow: 2px 4px 4px 0px rgba(0, 0, 0, 0.25);
	padding: 25px;
	height: 300px;
}

.bluebox p {
    color: white;
    font-size: 14px;
    font-style: normal;
    font-weight: 400;
}

.bluebox h3 {
	color: white;
	font-weight: 600;
	text-align: left;
}

.btnwhite {
	background: white;
	border: 1px solid white;
	border-radius: 5px;
	color: #0A2B77;
    font-family: 'SFUIDISPLAYBOLD', sans-serif;
	display: inline-flex;
	height: 41px;
	padding: 8px 17px;
	width: auto;
    transition: all 0.25 ease;
}

.btnwhite:hover {
	background: #0A2B77;
	border: 1px solid white;
	border-radius: 5px;
	color: white;
    font-family: 'SFUIDISPLAYBOLD', sans-serif;
	display: inline-flex;
	height: 41px;
	padding: 8px 17px;
	width: auto;
}

.card a {
    color: #498CCE;
}
</style>
       


<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label mb-10">
               Title
            </label>
            {!! Form::text( 'title', null, array('class' => 'form-control required', 'id' =>  'title' , 'maxlength' => '100', 'onkeyup' => 'validateLength(this, 100)')) !!}
            <small id="titleHelp" class="form-text text-muted text-red" style="display:none;">Max 100 characters</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label mb-10">
               Button Text
            </label>
            {!! Form::text( 'action_button', null, array('class' => 'form-control required', 'id' =>  'buttonText', 'maxlength' => '20', 'onkeyup' => 'validateLength(this, 20)')) !!}
            <small id="buttonTextHelp" class="form-text text-muted text-red" style="display:none;">Max 20 characters</small>
        </div>
    </div>
</div>

<div class="row">
    <!--/span-->
    <div class="col-md-12">
    <div class="form-group">
            <label class="control-label mb-10">
               Description
            </label>
            {!! Form::text( 'description', null, array('class' => 'form-control required', 'id' =>  'description',  'maxlength' => '200', 'oninput' => 'validateLength(this, 200)')) !!}
            <small id="descriptionHelp" class="form-text text-muted text-red" style="display:none;">Max 200 characters</small>
        </div>
    </div>
    <div class="col-md-12">
    <div class="form-group">
            <label class="control-label mb-10">
               Audience
            </label>
            <select name="roles[]" class="select2" multiple="multiple" data-placeholder="Select a Role" style="width: 100%;">
                @if(!empty($roles))
                @foreach($roles as $key => $role)
                    <option value="{{ $role->name }}"  @selected(in_array($role->name, $selected_role))>{{ $role->display_name }}</option>
                @endforeach
                @endif
            </select>
            
        </div>
    </div>
 
<div class="row form-group " style="width:100%">
    
<div class="col-lg-6 col-sm-12 p-l-0 m-p-l-0">
    <div class="row">
        <div class="col-12">
            <label class="control-label mb-10">
                Cover Image
            </label>
        </div>
        <div class="col-12">
            <p>The image needs to be at least 625 x 355 pixels.</p>
        </div>
    </div>
    <div class="col-md-12 col-sm-12 text-center">
        <div id="drag-and-drop-zone" class="dm-uploader p-5 sld_brder">
            <div class="row">
                <div class="col-md-12 mb-1 mt-1">
                    <label class="control-label">Cover Image</label>
                    <p class="mb-0"><br/></p>
                    <div class="btn btn-primary mb-2 showBg1">
                        <span>Upload Logo</span>
                        <input type="file" title="{{ __('labels.labels_click_to_add_files') }}" name="media" id="coverImage" onchange="loadFile1(event)"/>
                    </div>
                    @if(Request::route()->getName() == 'explore.edit')
                        @if(!empty($component->media))
                            <div class="col-sm-3 col-xs-3">
                                <a href="{{ asset('uploads/explore/'.$component->media) }}">
                                    <img src='{{asset($component->media)}}' onerror="this.onerror=null;this.src='{{ config('site-settings.aws_url').'public/front/img/no-img.jpg' }}';" width="50px">
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <span class="text text-danger">{{ $errors->first('media')}}</span>
    </div>
    <div class="col-md-12 col-sm-12 file-upload-status upload-extr-p-form">
        <div class="h-100">
            <ul class="list-unstyled p-2 d-flex flex-column col" id="files">
                <li class="text-muted text-center empty"></li>
            </ul>
        </div>
    </div>
</div>


    <div class="col-lg-6 col-sm-12 p-l-0 m-p-l-0 ">
    <div><label>Preview</label><button class="btn-primary float-right" onclick="previewBox()">View updated Preview</button></div>
    <div><br></div>
        <div class="bluebox card"  style="display: none;">
                    <div class="row">
                        <div class="col-6">
                            <h3 id="previewTitle">Title</h3>
                                          
                            <p id="previewDesc">Description</p>
                            <a class="btnwhite center" id="previewButton" href="">Action Button</a>
                        </div>
                        <div class="col-6">
                            <div class="imgbox">
                            @if(@$lab->mediaType == 'image')
                                <div id="canvasHTML" class="imageCanvasError" style="display: none;">
                                    <div class="canvasbox">
                                        <div class="canvas-header">
                                            <h5 class="blue-txt">Lab</h5>
                                        </div>
                                        <div class="canvas-body blue-bg-box">
                                            <span>bhjhbj</span>
                                        </div>
                                        <div class="canvas-footer">
                                        </div>
                                    </div>
                                </div>
                                <img src="" onerror="imageError(this)">
                            @elseif(@$lab->mediaType == 'embeddedCode')
                                <div class="embed-responsive embed-responsive-21by9">
                                    {!! @$lab->getRawOriginal('image') !!}
                                </div>
                            @else
                            <img id="preview" class="preview" src="#" alt="Cover Image Preview" height="250" width="250">
                            <!-- <img src="" onerror="imageError(this)"> -->
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
        </div>
</div>
    <!--/span-->
</div>
<script>
            function previewBox() {
                event.preventDefault();
            // Select the element with the id 'previewTitle'
            $('.bluebox').show();
            var previewTitleElement = document.getElementById("previewTitle");
            var previewDescElement = document.getElementById("previewDesc");
            var previewBtnElement = document.getElementById("previewButton");
            // Replace the content of the selected element
            previewTitleElement.innerHTML = $('#title').val();
            previewDescElement.innerHTML = $('#description').val();
            previewBtnElement.innerHTML = $('#buttonText').val();
            
        }

        function loadFile1(event) {
            var preview = document.getElementById('preview');
            var file = event.target.files[0];
            var reader = new FileReader();
            reader.onload = function() {
                preview.src = reader.result;
                preview.style.display = "block"; // Show the preview image
            }
            if (file) {
                reader.readAsDataURL(file); // Read the file as a data URL
            }
        }
        function validateLength(element, maxLength) {
            var helpTextId = element.id + 'Help';
           
            var helpTextElement = document.getElementById(helpTextId);
          
            if (element.value.length >= maxLength) {
                helpTextElement.style.display = 'inline';
            } else {
                helpTextElement.style.display = 'none';
            }
        }
</script>
