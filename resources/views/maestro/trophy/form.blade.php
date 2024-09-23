<div class="row">
<div class="col-md-6">
    <div class=" form-group {{($errors->has('name')) ? 'has-error' : ''}}">
        {!! Form::label('name', 'Name', ['class' => 'control-label ']) !!}
        {!! Form::text('name',null, ['class' => 'form-control', 'required' => 'required']) !!}
        <span class="help-block">{{ $errors->first('name')}}</span>
    </div>
    <div class=" form-group {{($errors->has('criteria')) ? 'has-error' : ''}}">
        {!! Form::label('criteria', 'Criteria', ['class' => 'control-label ']) !!}
        {!! Form::text('criteria',null, ['class' => 'form-control', 'required' => 'required']) !!}
        <span class="help-block">{{ $errors->first('criteria')}}</span>
    </div>
    {!! Form::label('issue_trophy_date', 'Issue Trophy date', ['class' => 'control-label ']) !!}
    <div class="input-group date" id="issue_trophy_date" data-target-input="nearest">
    <input type="text" class="form-control datetimepicker-input" data-target="#issue_trophy_date" name="issue_trophy_date" value="@isset($awardedTrophies){{$awardedTrophies->issue_trophy_date}}@endif" required/>
        <div class="input-group-append" data-target="#issue_trophy_date" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>
    </div>
    
    {!! Form::label('expiration_date', 'Expiration date', ['class' => 'control-label ']) !!}
    <div class="input-group date" id="expiration_date" data-target-input="nearest">
    <input type="text" class="form-control datetimepicker-input" data-target="#expiration_date" name="expiration_date" value="@isset($awardedTrophies){{$awardedTrophies->expiration_date}}@endif" required/>
        <div class="input-group-append" data-target="#expiration_date" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>
    </div>
    
    <div class=" form-group {{($errors->has('trophy_code_id')) ? 'has-error' : ''}}">
        {!! Form::label('trophy_code_id', 'Trophy Code ID', ['class' => 'control-label ']) !!}
        {!! Form::text('trophy_code_id',null,['class' => 'form-control', 'required' => 'required']) !!}
        <span class="help-block">{{ $errors->first('trophy_code_id')}}</span>
    </div>
    <div class=" form-group {{($errors->has('no_of_times_issued')) ? 'has-error' : ''}}">
        {!! Form::label('no_of_times_issued', '# of times issued', ['class' => 'control-label ']) !!}
        {!! Form::selectRange('no_of_times_issued', 1, 20 , null , ['class' => 'form-control', 'required' => 'required']) !!}
        <span class="help-block">{{ $errors->first('no_of_times_issued')}}</span>
    </div>
    <div class=" form-group {{($errors->has('status')) ? 'has-error' : ''}}">
        {!! Form::label('status', 'Status', ['class' => 'control-label ']) !!}
        {!! Form::select('status', $status, old('status'), ['class' => 'form-control ']) !!}
        <span class="help-block">{{ $errors->first('status')}}</span>
    </div>
</div>
<div class="col-md-6">
    <div class="form-group {{($errors->has('description')) ? 'has-error' : ''}}">
        {!! Form::label('description', 'Description', ['class' => 'control-label ']) !!}
        {!! Form::textarea('description',null, ['class' => 'form-control','rows'=>'12','style'=>'resize:none;', 'required' => 'required']) !!}
        <span class="help-block">{{ $errors->first('description')}}</span>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                    {!! Form::label('image', 'Image', ['class' => 'control-label']) !!}
                    <div class="clearfix"></div>
                    <div class="pull-left">
                        {!! Form::file('image', ['class' => 'form-control', 'required' => isset($awardedTrophies) ? '' : 'required']) !!}
                    </div>
                    @if(isset($awardedTrophies->image) && $awardedTrophies->image !== '')
                        <div class="pull-right">
                            <img src="{{ asset($awardedTrophies->image) }}" 
                                onerror="this.onerror=null;this.src='{{ config('site-settings.aws_url') . 'public/front/img/no-img.jpg' }}';" 
                                width="auto" height="50px">
                        </div>
                    @endif
                    <span class="help-block">{{ $errors->first('image') }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class=" form-group {{($errors->has('user_id')) ? 'has-error' : ''}}">
                    {!! Form::label('user_id', 'Users List', ['class' => 'control-label ']) !!}
                    {!! Form::select('user_id[]', $users , $awardedMembers , ['class' => 'form-control select2', 'multiple'=> 'multiple','id' => 'users', 'required' => 'required']) !!}
                    <span class="help-block">{{ $errors->first('user_id')}}</span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class=" form-group {{($errors->has('points_gained')) ? 'has-error' : ''}}">
            {!! Form::label('points_gained', 'Point Gained', ['class' => 'control-label ']) !!}
            {!! Form::number('points_gained',null, ['class' => 'form-control', 'required' => 'required']) !!}
            <span class="help-block">{{ $errors->first('points_gained')}}</span>
        </div>
        <div class=" form-group {{($errors->has('badge_type')) ? 'has-error' : ''}}">
            {!! Form::label('badge_type', 'Badge Type', ['class' => 'control-label ']) !!}
            {!! Form::select('badge_type', ['Points-Based'=>'Points-Based','Awarded'=>'Awarded','Challenge-Based'=>'Challenge-Based'] , null , ['class' => 'form-control','placeholder' => 'Please select option']) !!}
            <span class="help-block">{{ $errors->first('badge_type')}}</span>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">
   $(document).ready(function () {
    $('#datetimepicker1').datetimepicker({
        useCurrent: false,
        format: 'YYYY-MM-DD',
        <?php if (!isset($awardedTrophies)) { ?>
        minDate: new Date(),
        <?php } ?>
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down"
        },
    }).on('dp.show', function() {
        if ($(this).data("DateTimePicker").date() === null)
            $(this).data("DateTimePicker").date(moment());
    });
});


    $('#datetimepicker2').datetimepicker({
        useCurrent: false,
        format: 'YYYY-MM-DD',
        <?php if (!isset($awardedTrophies)) { ?>
        minDate:new Date(),
        <?php } ?>
        icons: {
            time: "fa fa-clock-o",
            date: "fa fa-calendar",
            up: "fa fa-arrow-up",
            down: "fa fa-arrow-down"
        },
    }).on('dp.show', function() {
        if ($(this).data("DateTimePicker").date() === null)
            $(this).data("DateTimePicker").date(moment());
    });

    $(document).ready(function() {
        $('.userSelect').select2({
            placeholder: "Select User",
        });
    });
</script>
