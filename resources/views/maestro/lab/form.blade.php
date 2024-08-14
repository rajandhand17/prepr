<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key={{ env('GOOGLE_MAP_KEY') }}&sensor=false&libraries=places"></script>
 <!-- Select2 CSS -->
<style>
    .tooltipadmincat, .tooltipadminpriv {
        width: 10px;
        display: inline-block;
        /* position: absolute;
        margin-top: -25px;
        margin-left: 18%; */
    }
    .btn-success1{
        border: 1px solid #ccc;
        display: inline-block;
        padding: 6px 12px;
        background: #469408;
        color: white;
        border-radius: 2px;
    }
    .pos{
        width: 10px;
        display: inline-block;
        /* position: absolute;
        margin-top: -28px;
        margin-left: 14%; */
    }
    .tooltipadmin{
        display: inline-block;
    }
    .form-control[disabled], .form-control[readonly] {
        color: #222;
    }
</style>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/tagmanager/3.0.2/tagmanager.min.css">
<div class="row form-group ">
    <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('title')) ? 'has-error' : ''}}">
            {!! Form::label('title', 'Title', ['class' => 'control-label ']) !!}
            {!! Form::text('title',null, ['class' => 'form-control  ']) !!}
            <span class="help-block">{{ $errors->first('title')}}</span>
        </div>
    </div>
    <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('language')) ? 'has-error' : ''}}">
            {!! Form::label('language', 'Language', ['class' => 'control-label ']) !!}
            @if((strpos(Request::url(),'edit')) !== false )
            {!! Form::select('language', $languages, $data->language, ['class' => 'form-control  ','id' => 'languageId','disabled' => 'disabled']) !!}
            @else
            {!! Form::select('language', $languages, old('language'), ['class' => 'form-control  ','id' => 'languageId']) !!}            
            @endif
            <span class="help-block">{{ $errors->first('language')}}</span>
        </div>
    </div>
    <div class="clearfix"></div>
</div>

{{--Verification--}}
<div class="row form-group">
    @if(auth()->user()->hasRole('superadmin'))
        <div class="col-sm-3 col-xs-3">
            <div class="form-group {{($errors->has('user_id')) ? 'has-error' : ''}}">
                {!! Form::label('user_id', 'Lab User', ['class' => 'control-label ']) !!}
                {!! Form::select('user_id', $lab_member_labManager, old('user_id'), ['class' => 'form-control  ']) !!}
                <span class="help-block">{{ $errors->first('user_id')}}</span>
            </div>
        </div>
    @else
        <input type="hidden" name="user_id" value="{{auth()->user()->id}}">
    @endif
    <div class="col-sm-6 col-xs-6">
    <div class="form-group {{($errors->has('organization_id')) ? 'has-error' : ''}}">
            {!! Form::label('organization_id', 'Organisation', ['class' => 'control-label']) !!}
            {{ Form::select('organization_id', @$labAssociatedItems['organization'] ?? [],  @$data->organization_id, ['class' => 'form-control select2','required' => 'required','id' =>'organisationId','required' => 'required']) }}
            <span style="color: #ea6c41 !important;" class="help-block org_error">{{ $errors->first('organization_id')}}</span>
          </div>
        
    </div>
</div>
<div class="row form-group ">
    <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('category')) ? 'has-error' : ''}}">
            {!! Form::label('category', 'Lab Category', ['class' => 'control-label ']) !!}
            <div class="tooltipadmincat" data-toggle="tooltip" data-placement="right"
                 data-original-title="Where did this lab originate?" title="">
                <i class="fa fa-question-circle"></i>
            </div>
            @if((strpos(Request::url(),'edit')) !== false )
                {{ Form::select('category', @$labAssociatedItems['category'], [], ['class' => 'form-control select2 ','id' => 'listCategory']) }}
            @else
                {{ Form::select('category', @$labAssociatedItems['category'] ?? [], old('category'), ['class' => 'form-control select2 ','id' => 'listCategory']) }}
            @endif
            <span class="help-block">{{ $errors->first('category')}}</span>
        </div>
    </div>
    <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('privacy')) ? 'has-error' : ''}}">

            {!! Form::label('privacy', 'Lab Privacy', ['class' => 'control-label ']) !!}
            <div class="tooltipadmin" data-toggle="tooltip" data-placement="right" data-original-title="{{ __('labels.labels_lab_plaofa') }}" title="">
                <i class="fa fa-question-circle"></i>
            </div>

            {!! Form::select('privacy', $lab_privacy, old('privacy'), ['class' => 'form-control  privacy']) !!}
            <span class="help-block">{{ $errors->first('privacy')}}</span>

        </div>
    </div>

    <div class="clearfix"></div>
</div>
<div class="row form-group ">
    <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('labSkills')) ? 'has-error' : ''}}">
            {!! Form::label('labSkills', 'Skills', ['class' => 'control-label ']) !!}
              {!! Form::select('labSkills[]', @$labAssociatedItems['skills'] ?? [], @$labAssociatedItems['skillIds'] ?? [], ['class' => 'form-control
              select2','multiple'=>'multiple', 'id'=>'Skills']) !!}
              <span class="help-block">{{ $errors->first('labSkills')}}</span>

        </div>
    </div>
</div>
<div class="row form-group ">
    <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('resource_modules')) ? 'has-error' : ''}}">
            {!! Form::label('resource_modules', 'Associated Resources module', ['class' => 'control-label']) !!}
            {!! Form::select('resource_modules[]', @$labAssociatedItems['resourceModules'] ?? [], @$labAssociatedItems['resourceModules']->keys() , ['class' => 'form-control select2','multiple'=>'multiple','id'=>'resourceModule']) !!}
            <span class="help-block">{{ $errors->first('resource_modules')}}</span>
            <span class="help-block text-danger" id="source_error"></span>
        </div>
    </div>
    <div class="col-sm-6 col-xs-6">
        {!! Form::label('Location', 'Location', ['class' => 'control-label ']) !!}
        <input type="text" class="form-control" placeholder="Location" name="address" id="searchTextField"
               value="{{ isset($data) ? $data->address : old('address') }}">
        <input type="hidden" id="city2" name="city2" value="{{ isset($data)  ? $data->city : old('city2') }}"/>
        <input type="hidden" id="country2" name="country2"
               value="{{ isset($data)  ? $data->country : old('country2') }}"/>
        <input type="hidden" id="cityLat" name="cityLat"
               value="{{ isset($data)  ? $data->latitute : old('cityLat') }}"/>
        <input type="hidden" id="cityLng" name="cityLng"
               value="{{ isset($data)  ? $data->longitude : old('cityLng') }}"/>
        {{--<input type="hidden" name="status" id="status" value="{{ isset($data)  ? $data->status : '1'}}">--}}
        <input type="hidden" value="" name="status" id="status" value="1">

        @if ($errors->has('location'))
            <div class="text-danger">{{ $errors->first('location') }}</div>
        @endif
        @if ($errors->has('address'))
            <div class="text-danger">{{ $errors->first('address') }}</div>
        @endif
        <!-- @if ($errors->has('cityLng'))
            <div class="text-danger">{{ $errors->first('cityLng') }}</div>
        @endif -->
        @if ($errors->has('country2'))
            <div class="text-danger">{{ $errors->first('country2') }}</div>
        @endif
        @if ($errors->has('city2'))
            <div class="text-danger">{{ $errors->first('city2') }}</div>
        @endif
    </div>
</div>
<div class="row form-group ">
    <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('image')) ? 'has-error' : ''}}">
            {!! Form::label('image', 'Cover Image', ['class' => 'control-label ']) !!}
            <div class="tooltipadminpriv" data-toggle="tooltip" data-placement="right"
                 data-original-title="The image must be 355 x 625 pixels" title="">
                <i class="fa fa-question-circle"></i>
            </div>
            {!! Form::file('image') !!}
            <span class="help-block">{{ $errors->first('image')}}</span>
        </div>
        @if(isset($data->image) && $data->image!="")
            <div class="pull-right trtrytr">
                <img src="{{asset($data->image)}}" width="auto" height="50px">
            </div>
        @endif
    </div>

    <div class="clearfix"></div>
</div>
<hr>
<div class="row form-group ">
    <div class="col-sm-6 col-xs-6">
        <div class="form-group {{($errors->has('description')) ? 'has-error' : ''}}">
            {!! Form::label('description', 'Lab Description', ['class' => 'control-label ']) !!}
            {!! Form::textarea('description',null, ['class' => 'form-control  ','rows'=>'4']) !!}
            <span class="help-block">{{ $errors->first('description')}}</span>
        </div>
    </div>
  
    <div class="clearfix"></div>
</div>

<div class="row">
        <div class="col-md-6">
          <div class="form-group {{($errors->has('level')) ? 'has-error' : ''}}">
            {!! Form::label('level', 'Level', ['class' => 'control-label']) !!}
            {{ Form::select('level', @$labAssociatedItems['level'] ?? [],[], ['class' => 'form-control select2','id' => 'challengeLevels','required' => 'required']) }}
            <span style="color: #ea6c41 !important;" class="help-block level_error">{{ $errors->first('level')}}</span>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group {{($errors->has('duration')) ? 'has-error' : ''}}">
            {!! Form::label('duration', 'Duration', ['class' => 'control-label']) !!}
            {{ Form::select('duration', @$labAssociatedItems['duration'] ?? [],[], ['class' => 'form-control select2','id' => 'challengeDuration','required' => 'required']) }}
            <span style="color: #ea6c41 !important;" class="help-block duration_error">{{ $errors->first('duration')}}</span>
          </div>
        </div>
</div>
<hr>
<div class="col-sm-12 row form-group">
    <div class="form-head-title">
        <h6>Add Social Link</h6>
    </div>
</div>
<div class="social_area">
    @if(isset($labSocialLink) && !empty($labSocialLink))
        @foreach($labSocialLink as $key=>$link)
            <div class="row form-group" id="social_row-no_{{$key+1}}">
                <div class="col-sm-3 col-xs-3">
                    <div class="form-group {{($errors->has('lab_social')) ? 'has-error' : ''}}">
                        {!! Form::label('lab_social', 'Social Media', ['class' => '']) !!}
                        {!! Form::select('lab_social[]', $social_name->pluck('title', 'id') ,$link->social_link_id, ['class' => 'form-control  lab_social']) !!}
                        <span class="help-block">{{ $errors->first('lab_social')}}</span>
                    </div>
                </div>
                <div class="col-sm-3 col-xs-3">
                    <div class="form-group {{($errors->has('social_url')) ? 'has-error' : ''}}">
                        {!! Form::label('social_url', 'Enter URL', ['class' => 'control-label ']) !!}
                        {!! Form::text('social_url[]',$link->social_media_link, ['class' => 'form-control  social_url social_length']) !!}
                        <span class="help-block">{{ $errors->first('social_url')}}</span>
                    </div>
                </div>
                <div class="col-sm-1 col-xs-1 button_add_remove">
                    @if($key == 0)
                        <button class="btn btn-primary add_new_social" type="button"
                                row-no="{{count($labSocialLink)}}" style="margin-top: 20px;">+
                        </button>
                    @else
                        <button class="btn btn-danger" onclick="removeSocialUrl('{{$key+1}}')" type="button"
                                row-no="{{count($labSocialLink)}}" style="margin-top: 20px;">-
                        </button>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="row form-group" id="social_row-no_1">
            <div class="col-sm-3 col-xs-3">
                <div class="form-group {{($errors->has('lab_social')) ? 'has-error' : ''}}">
                    {!! Form::label('lab_social', 'Social Media', ['class' => 'control-label ']) !!}
                    {!! Form::select('lab_social[]', $social_name->pluck('title', 'id') ,null, ['class' => 'form-control  lab_social']) !!}
                    <span class="help-block">{{ $errors->first('lab_social')}}</span>
                </div>
            </div>
            <div class="col-sm-3 col-xs-3">
                <div class="form-group {{($errors->has('social_url')) ? 'has-error' : ''}}">
                    {!! Form::label('social_url', 'Enter URL', ['class' => 'control-label ']) !!}
                    {!! Form::text('social_url[]',null, ['class' => 'form-control  social_url social_length']) !!}
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

<!-- Start Notify participants of lab updates-->
@if(Request::segment(4)=='edit')
    <hr>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <div class="row">
                    <label class="control-label col-lg-4">Notify participants of lab updates</label>
                </div>
                <div class="row">
                    <div class="col-lg-10">
                        <input type="radio" id="send" name="notify_participants" value="send"> Send email &nbsp;
                        <input type="radio" id="notSend" name="notify_participants" value="notSend" checked> Do not send email
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Initialize Select2 -->

<!-- End Notify participants of challenge updates-->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tagmanager/3.0.2/tagmanager.min.js"></script>
<script type="text/javascript">
    
    $('[data-toggle="popover-hover"]').popover({
        html: true,
        trigger: 'hover',
        placement: 'right',
        content: function () { return '<img src="' + $(this).data('img') + '" />'; }
    });

</script>
