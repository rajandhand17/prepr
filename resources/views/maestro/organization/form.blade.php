<style type="text/css">
    .form-control[disabled],
    .form-control[readonly] {
        color: #222;
    }

    .sponser-add {
        margin-top: 30px;
        margin-bottom: 20px;
    }

    .sponser-add .sponsor_clnt {
        text-align: center;
    }

    .sponser-add .sponsor_clnt .spons_img {
        width: 200px;
        height: 200px;
        display: inline-block;
        position: relative;
        margin: auto;
        border: 1px solid #747474;
        background-color: #454545;
        border-radius: 5px;
    }

    .sponser-add .sponsor_clnt .spons_img img {
        width: auto;
        height: auto;
        position: absolute;
        top: 0;
        bottom: 0;
        right: 0;
        left: 0;
        max-height: 100%;
        max-width: 100%;
        margin: auto;
        padding: 5px;

    }

    .dm-uploader {
        border: 2px dashed #575757;
        padding: 5px 5px 15px;
    }
</style>
<div class="row form-group ">
    <div class="col-lg-6 col-sm-12 p-l-0 m-p-l-0 text-center">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <!-- Our markup, the important part here! -->
                <div id="drag-and-drop-zone" class="dm-uploader p-5 sld_brder">
                    <div class="row">
                        <div class="col-md-12 col-md-12 mb-1 mt-1">
                            <label class="control-label">Cover Image</label>

                            <p class="mb-0"></br></p>
                            <div class="btn btn-primary mb-2 showBg1">
                                <span>Upload Logo</span>
                                <input type="file" title="{{ __('labels.labels_click_to_add_files') }}" name="cover_image" id="coverImage"
                                       onchange="loadFile1(event)"/>
                            </div>
                            @if(Request::route()->getName() == 'organization.edit')
                                @if(!empty($data->cover_image))

                                    <div class="col-sm-3 col-xs-3">
                                        <a href="{{asset('uploads/organisation').'/'.$data->cover_image}}">
                                            <img src='{{asset($data->cover_image)}}' onerror="this.onerror=null;this.src='{{config(('site-settings.aws_url').'public/front/img/no-img.jpg')}}';" width='50px'>

                                        </a>

                                    </div>

                                @endif
                            @endif

                        </div>
                    </div>
                </div><!-- /uploader -->
                <span class="text text-danger">{{ $errors->first('cover_image')}}</span>
            </div>
            <div class="col-md-12 col-sm-12  file-upload-status upload-extr-p-form">
                <div class="h-100">
                    <ul class="list-unstyled p-2 d-flex flex-column col" id="files">
                        <li class="text-muted text-center empty"></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
 
    <div class="col-lg-6 col-sm-12 p-l-0 m-p-l-0 text-center">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <!-- Our markup, the important part here! -->
                <div id="drag-and-drop-zone" class="dm-uploader p-5 sld_brder">
                    <div class="row">
                        <div class="col-md-12 col-md-12 mb-1 mt-1">
                            <label class="control-label">Profile Image</label>

                            <p class="mb-0"></br></p>
                            <div class="btn btn-primary mb-2 showBg2">
                                <span>Upload Logo</span>
                                <input type="file" title="{{ __('labels.labels_click_to_add_files') }}" name="profile_image" id="profileImage"
                                       onchange="loadFile2(event)"/>

                            </div>
                            @if(Request::route()->getName() == 'organization.edit')
                                @if(!empty($data->profile_image))

                                    <div class="col-sm-3 col-xs-3">
                                        <a href="{{asset('uploads/organisation').'/'.$data->profile_image}}">
                                            <img src='{{asset($data->profile_image)}}' onerror="this.onerror=null;this.src='{{config(('site-settings.aws_url').'public/front/img/no-img.jpg')}}';" width='50px'>

                                        </a>

                                    </div>

                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <!-- /uploader -->
                <span class="text text-danger">{{ $errors->first('profile_image')}}</span>
            </div>
            <div class="col-md-12 col-sm-12  file-upload-status upload-extr-p-form">
                <div class="h-100">
                    <ul class="list-unstyled p-2 d-flex flex-column col" id="files">
                        <li class="text-muted text-center empty"></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-sm-12 row form-group">
    <div class="form-head-title">
        <h6>General</h6>
    </div>
</div>
<div class="clearfix mb-3"></div>

<div class="row form-group">
        <div class="col-sm-6 col-xs-6">
            <div class="form-group {{($errors->has('name')) ? 'has-error' : ''}}">
                {!! Form::label('name', 'Name', ['class' => 'control-label']) !!}
                {!! Form::text('title',null, ['class' => 'form-control', 'id'=>'name']) !!}
                <span class="help-block">{{ $errors->first('title')}}</span>
            </div>
        </div>
        <div class="col-sm-6 col-xs-6">
            <div class="form-group {{($errors->has('language')) ? 'has-error' : ''}}">
                {!! Form::label('language', 'Language', ['class' => 'control-label ']) !!}
                @if((strpos(Request::url(),'edit')) !== false )
                    {!! Form::select('language', $languages, $data->language, ['class' => 'form-control ','id' => 'languageId','disabled' => 'disabled']) !!}
                @else
                    {!! Form::select('language', $languages, old('language'), ['class' => 'form-control','id' => 'languageId']) !!}            
                @endif
                <span class="help-block">{{ $errors->first('language')}}</span>
            </div>
        </div>
    <div class="clearfix"></div>
</div>

<div class="row form-group">
<div class="col-sm-12 col-xs-12">
    <div class="form-group {{($errors->has('about')) ? 'has-error' : ''}}">
        {!! Form::label('about', 'Description', ['class' => 'control-label ']) !!}
        {!! Form::textarea('about',null, ['class' => 'form-control', 'id' => 'about_description']) !!}
        <span class="help-block">{{ $errors->first('about')}}</span>
    </div>
</div>
<div class="clearfix"></div>
</div>

<div class="row form-group ">
    <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('website')) ? 'has-error' : ''}}">
            {!! Form::label('website', 'Website', ['class' => 'control-label ']) !!}
            {!! Form::url('website', null , ['class' => 'form-control ']) !!}
            <span class="help-block">{{ $errors->first('title')}}</span>
        </div>
    </div>

    @if(auth()->user())
        <div class="col-sm-6 col-xs-6">
            <div class="form-group {{($errors->has('user_id')) ? 'has-error' : ''}}">
                {!! Form::label('user_id', 'Organization User', ['class' => 'control-label ']) !!}
                {!! Form::select('user_id', @$orgAssociatedItems['user'] ?? [], old('user_id'),['class' => 'form-control ', 'id'=>'userId','placeholder'=>'Select User']) !!}
                <span class="help-block">{{ $errors->first('user_id')}}</span>
            </div>
        </div>
    @else
        <input type="hidden" name="user_id" value="{{auth()->user()->id}}">
    @endif
    <div class="clearfix"></div>
</div>
<div class="row form-group ">
    <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('category')) ? 'has-error' : ''}}">
            {!! Form::label('category', 'Category', ['class' => 'control-label ']) !!}
            @if((strpos(Request::url(),'edit')) !== false )
                {{ Form::select('category',  @$orgAssociatedItems['category'] ?? [], [], ['class' => 'form-control select2','id' => 'listCategory']) }}
            @else
                {{ Form::select('category', [], old('category'), ['class' => 'form-control select2','id' => 'listCategory']) }}
            @endif
            <span class="help-block">{{ $errors->first('category')}}</span>
        </div>
    </div>

    <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('address')) ? 'has-error' : ''}}">
            {!! Form::label('address', 'Location', ['class' => 'control-label ']) !!}
            {!! Form::text('address', null, ['class' => 'form-control','id'=>'searchTextField']) !!}
            <span class="help-block">{{ $errors->first('address')}}</span>
            <span class="help-block">{{ $errors->first('city2')}}</span>
        <!-- <input type="hidden" id="city2" name="city2"  value="{{ old('city2') }}"/> -->
            {!! Form::hidden('city2',null, ['class' => 'form-control','id'=>'city2']) !!}
            {{-- <input type="hidden" id="cityLat" name="cityLat"  value="{{ old('cityLat') }}"/> --}}
            {!! Form::hidden('latitude',null, ['class' => 'form-control','id'=>'cityLat']) !!}
            {{-- <input type="hidden" id="cityLng" name="cityLng"  value="{{ old('cityLng') }}"/>  --}}
            {!! Form::hidden('longitude',null, ['class' => 'form-control','id'=>'cityLng']) !!}

        </div>
    </div>
    <div class="clearfix"></div>
</div>
<div class="row form-group ">

    <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('status')) ? 'has-error' : ''}}">
            {!! Form::label('status', 'Status', ['class' => 'control-label ']) !!}
            {!! Form::select('status',$status_array,null, ['class' => 'form-control']) !!}
            <span class="help-block">{{ $errors->first('status')}}</span>
        </div>
    </div>

    {!! Form::hidden('slug',null, ['class' => 'form-control','id'=>'vanity_slug']) !!}
    <!-- <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('vanity_link')) ? 'has-error' : ''}}">
            {!! Form::label('vanity_link', 'Vanity link', ['class' => 'control-label ']) !!}
            {!! Form::url('vanity_link',null, ['class' => 'form-control','id'=>'vanity_link']) !!}
            <span class="help-block">{{ $errors->first('vanity_link')}}</span>
        </div>
    </div> -->

    <div class="clearfix"></div>
</div>

<hr>
<div class="col-sm-12 row form-group">
    <div class="form-head-title">
        <h6>Add Social Link</h6>
    </div>
</div>
<div class="clearfix mb-3"></div>
<div class="social_area">
    @if(isset($orgSocialLink) && count($orgSocialLink) > 0)
        @foreach($orgSocialLink as $key=>$link)
            <div class="row form-group" id="social_row-no_{{$key+1}}">
                <div class="col-sm-3 col-xs-3">
                    <div class="form-group {{($errors->has('org_social')) ? 'has-error' : ''}}">
                        {!! Form::label('org_social', 'Social Media', ['class' => 'control-label ']) !!}
                        {!! Form::select('org_social[]',$social_name->pluck('title', 'id'), $link->social_link_id, ['class' => 'form-control org_social']) !!}
                        <span class="help-block">{{ $errors->first('org_social')}}</span>
                    </div>
                </div>
                <div class="col-sm-3 col-xs-3">
                    <div class="form-group {{($errors->has('social_url')) ? 'has-error' : ''}}">
                        {!! Form::label('social_url', 'Enter URL', ['class' => 'control-label ']) !!}
                        {!! Form::text('social_url[]',$link->social_media_link , ['class' => 'form-control social_url social_length']) !!}
                        <span class="help-block">{{ $errors->first('social_url')}}</span>
                    </div>
                </div>
                <div class="col-sm-1 col-xs-1 button_add_remove">
                    @if($key == 0)
                        <button class="btn btn-primary add_new_social" type="button"
                                row-no="{{count($orgSocialLink)}}" style="margin-top: 20px;">+
                        </button>
                    @else
                        <button class="btn btn-danger" onclick="removeSocialUrl('{{$key+1}}')" type="button"
                                row-no="{{count($orgSocialLink)}}" style="margin-top: 20px;">-
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="row form-group" id="social_row-no_1">
            <div class="col-sm-3 col-xs-3">
                <div class="form-group {{($errors->has('org_social')) ? 'has-error' : ''}}">
                    {!! Form::label('org_social', 'Social Media', ['class' => 'control-label ']) !!}
                    {!! Form::select('org_social[]',$social_name->pluck('title', 'id'),null, ['class' => 'form-control org_social']) !!}
                    <span class="help-block">{{ $errors->first('org_social')}}</span>
                </div>
            </div>
            <div class="col-sm-3 col-xs-3">
                <div class="form-group {{($errors->has('social_url')) ? 'has-error' : ''}}">
                    {!! Form::label('social_url', 'Enter URL', ['class' => 'control-label ']) !!}
                    {!! Form::text('social_url[]',null, ['class' => 'form-control social_url social_length']) !!}
                    <span class="help-block">{{ $errors->first('social_url')}}</span>
                </div>
            </div>

            <div class="col-sm-1 col-xs-1 button_add_remove">
                <button class="btn btn-primary add_new_social" type="button" row-no="1" style="margin-top: 20px;">+
                </button>
            </div>
        </div>
    @endif
</div>
<hr>
<div class="col-sm-12 row form-group">
    <div class="form-head-title">
        <h6>Add People</h6>
    </div>
</div>
<div class="clearfix mb-3"></div>
<div class="incentive_area">
    @if(isset($org_members) && count($org_members) > 0)
        @foreach($org_members as $key=>$peoples)
            <div class="row form-group" id="row_no_{{$key+1}}">
                <div class="col-sm-3 col-xs-3">
                    <div class="form-group {{($errors->has('people_name')) ? 'has-error' : ''}}">
                        {!! Form::label('people_name', 'People Name', ['class' => 'control-label ']) !!}
                        {!! Form::text('people_name[]',$peoples->name, ['class' => 'form-control incentive_name','maxlength'=>'150']) !!}
                        <span class="help-block">{{ $errors->first('people_name')}}</span>
                    </div>
                </div>
                <div class="col-sm-3 col-xs-3">
                    <div class="form-group {{($errors->has('people_des')) ? 'has-error' : ''}}">
                        {!! Form::label('people_des', 'People Description', ['class' => 'control-label ']) !!}
                        {!! Form::text('people_des[]',$peoples->description, ['class' => 'form-control  incentive_prize','min'=>'0','maxlength'=>'500']) !!}
                        <span class="help-block">{{ $errors->first('people_des')}}</span>
                    </div>
                </div>

                <div class="col-sm-3 col-xs-3">
                    <input type="hidden" name="image[]" value="{{$peoples->image}}">
                    <div class="form-group {{($errors->has('incentive_trophy')) ? 'has-error' : ''}}">
                        {!! Form::label('image', 'People Image', ['class' => 'control-label ']) !!}
                        {!! Form::file('image[]', ['class' => 'incentive_trophy','id'=>'fileUpload','accept'=>'image/x-png,image/gif,image/jpeg']) !!}
                        <span class="help-block">{{ $errors->first('image')}}</span>
                    </div>

                    @if(!empty($peoples))
                        <div class="col-sm-3 col-xs-3">
                            <a href="{{asset('uploads/organisation').'/'.$peoples->image}}">
                                <img src='{{asset($peoples->image)}}' onerror="this.onerror=null;this.src='{{config(('site-settings.aws_url').'public/front/img/no-img.jpg')}}';" width='50px'>

                            </a>
                        </div>
                    @endif
                </div>
                <div class="col-sm-1 col-xs-1 button_add_remove">
                    @if($key == 0)
                        <button class="btn btn-primary add_new_incentive" type="button"
                                row-no="{{count($org_members)}}" style="margin-top: 20px;">+
                        </button>
                    @else
                        <button class="btn btn-danger" onclick="removeIncentive('{{$key+1}}')" type="button"
                                row-no="{{count($org_members)}}" style="margin-top: 20px;">-
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="row form-group" id="row_no_1">
            <div class="col-sm-3 col-xs-3">
                <div class="form-group {{($errors->has('incentive_name')) ? 'has-error' : ''}}">
                    {!! Form::label('people_name', 'People Name', ['class' => 'control-label ']) !!}
                    {!! Form::text('people_name[]',null, ['class' => 'form-control  incentive_name','maxlength'=>'150']) !!}
                    <span class="help-block">{{ $errors->first('people_name')}}</span>
                </div>
            </div>
            <div class="col-sm-3 col-xs-3">
                <div class="form-group {{($errors->has('incentive_prize')) ? 'has-error' : ''}}">
                    {!! Form::label('people_des', 'People Description', ['class' => 'control-label ']) !!}
                    {!! Form::text('people_des[]',null, ['class' => 'form-control  incentive_prize','maxlength'=>'500']) !!}
                    <span class="help-block">{{ $errors->first('people_des')}}</span>
                </div>
            </div>

            <div class="col-sm-3 col-xs-3">
                <div class="form-group {{($errors->has('image')) ? 'has-error' : ''}}">
                    {!! Form::label('image', 'People Image', ['class' => 'control-label ']) !!}
                    {!! Form::file('image[]', ['class' => 'incentive_trophy','accept'=>'image/x-png,image/gif,image/jpeg']) !!}
                    <span class="help-block">{{ $errors->first('image')}}</span>
                </div>
            </div>

            <div class="col-sm-1 col-xs-1 button_add_remove">
                <button class="btn btn-primary add_new_incentive" type="button" row-no="1" style="margin-top: 20px;">+
                </button>
            </div>
        </div>
    @endif
</div>

<div class="row upload-extr-p-form mt-4 sponser-add">

</div>
<hr>

<script type="text/javascript"
        src="{{asset('adminassets/vendors/bower_components/moment/min/moment-with-locales.min.js')}}"></script>

<script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places"></script>

<script>
       /* show image instant display code for that*/
       var loadFile1 = function (event) {
        var output = document.getElementById('output');
        //output.src = URL.createObjectURL(event.target.files[0]);
        var imageurl = URL.createObjectURL(event.target.files[0]);
        console.log(imageurl);

        $("body").find('.showBg1').eq(0).attr('style', 'background:url(' + imageurl + ' ) no-repeat center');
    };

    var loadFile2 = function (event) {
        var output = document.getElementById('output');
        //output.src = URL.createObjectURL(event.target.files[0]);
        var imageurl = URL.createObjectURL(event.target.files[0]);
        console.log(imageurl);

        $("body").find('.showBg2').eq(0).attr('style', 'background:url(' + imageurl + ' ) no-repeat center');
    };


    function initialize() {
        var input = document.getElementById('searchTextField');
        new google.maps.places.Autocomplete(input);

        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();
            document.getElementById('city2').value = place.name;
            document.getElementById('cityLat').value = place.geometry.location.lat();
            document.getElementById('cityLng').value = place.geometry.location.lng();
            //alert("This function is working!");
            //alert(place.name);
            // alert(place.address_components[0].long_name);
        });

    }

    google.maps.event.addDomListener(window, 'load', initialize);

    $('#searchTextField').keypress(function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });
        /* add new incentive dynamic jquery code */
    $('.add_new_incentive').click(function () {
        var row_no = $(this).attr('row-no');
        var html_data = $('#row_no_1').html();
        //$('#row_no_'+row_no).find(".button_add_remove").html('');
        var row_no = parseInt(row_no) + 1;
        $(this).attr('row-no', row_no);
        var new_html = '<div class="row form-group" id="row_no_' + row_no + '">';
        new_html += html_data;
        new_html += '</div>';
        $('.incentive_area').append(new_html);
        $('#row_no_' + row_no).find(".button_add_remove").html('<button class="btn btn-danger" onclick="removeIncentive(' + row_no + ')" type="button" style="margin-top: 20px;">-</button>');
        $('#row_no_' + row_no).find(".incentive_name").val('');
        $('#row_no_' + row_no).find(".incentive_prize").val('');
        $('#row_no_' + row_no).find(".incentive_point").val('');
        $('#row_no_' + row_no).find(".incentive_trophy").val('');
    });

    function removeIncentive(row_no) {
        $('.incentive_area').find('#row_no_' + row_no).remove();
    }

    /* add new user role jquery code */
    $('.add_new_user_role').click(function () {
        var row_no = $(this).attr('row-no');
        var numItems = $('.user_length').length;
        var html_data = $('#row-no_1').html();
        //$('#row_no_'+row_no).find(".button_add_remove").html('');
        var row_no = parseInt(row_no) + 1;
        $(this).attr('row-no', row_no);
        var new_html = '<div class="row form-group" id="row-no_' + row_no + '">';
        new_html += html_data;
        new_html += '</div>';
        new_html = new_html.replace("user_length", "user_length user_length"+numItems);
        $('.user_role_area').append(new_html);
        $('#row-no_' + row_no).find(".button_add_remove").html('<button class="btn btn-danger" onclick="removeUserRole(' + row_no + ')" type="button" style="margin-top: 20px;">-</button>');
        $('#row-no_' + row_no).find(".user_name").val('');
        $('#row-no_' + row_no).find(".user_role").val('');

        $('.user_length'+numItems).autocomplete({
            source: "{{url('autocomplete')}}",
            minlength:1,
            autoFocus:true,
            select:function(e,ui)
            {
                $(this).val(ui.item.value);
            }
        });
    });

    function removeUserRole(row_no) {
        $('.user_role_area').find('#row-no_' + row_no).remove();
    }

    /* add new social link jquery code */
    $('.add_new_social').click(function () {
        var row_no = $(this).attr('row-no');
        var numItems = $('.social_length').length;
        var html_data = $('#social_row-no_1').html();
        //$('#row_no_'+row_no).find(".button_add_remove").html('');
        var row_no = parseInt(row_no) + 1;
        $(this).attr('row-no', row_no);
        var new_html = '<div class="row form-group" id="social_row-no_' + row_no + '">';
        new_html += html_data;
        new_html += '</div>';
        new_html = new_html.replace("social_length", "social_length social_length"+numItems);
        $('.social_area').append(new_html);
        $('#social_row-no_' + row_no).find(".button_add_remove").html('<button class="btn btn-danger" onclick="removeSocialUrl(' + row_no + ')" type="button" style="margin-top: 20px;">-</button>');
        $('#social_row-no_' + row_no).find(".org_social").val('');
        $('#social_row-no_' + row_no).find(".social_url").val('');

    });

    function removeSocialUrl(row_no) {
        $('.social_area').find('#social_row-no_' + row_no).remove();
    }

        $('.user_name').autocomplete({
            source: "{{url('autocomplete')}}",
            minlength:1,
            autoFocus:true,
            select:function(e,ui)
            {
                $(this).val(ui.item.value);
            }
        });

        $("#name").keyup(function(){
            var slug = convertToSlug($("#name").val());
            $("#vanity_slug").val(slug);
            $("#vanity_link").val(window.location.origin+'/org/'+slug);
        });
        $("#vanity_link").keyup(function(){
            var data = window.location.origin+'/org/';
            var slug = $("#vanity_link").val();
            var v_slug = slug.replace(data,'');
            v_slug = v_slug.replace(/\W+(?!$)/g, '-').toLowerCase();
            v_slug = v_slug.replace(/\W$/, '').toLowerCase();
            $("#vanity_slug").val(v_slug);
            $("#vanity_link").val(data+v_slug);
        });

        function convertToSlug(Text)
        {
            return Text
                .toLowerCase()
                .replace(/[^\w ]+/g,'')
                .replace(/ +/g,'-')
                ;
        }
 


    function check_words(e) {
        var BACKSPACE  = 8;
        var DELETE     = 46;
        var MAX_WORDS  = 301;
        var valid_keys = [BACKSPACE, DELETE];
        var words      = this.value.split(' ');

        if (words.length >= MAX_WORDS && valid_keys.indexOf(e.keyCode) == -1) {
            e.preventDefault();
            words.length = MAX_WORDS;
            this.value = words.join(' ');
        }
    }
    var textarea = document.getElementById('about_description');
    textarea.addEventListener('keydown', check_words);
    textarea.addEventListener('keyup', check_words);

    </script>