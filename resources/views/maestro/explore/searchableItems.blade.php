@foreach($components as $component)
<div class="col-12 my-2">
    <div class="explore_section explore-item">
                <div class="component-type">
                    {{ ucfirst($component->type) }}
                </div>
        <div class="d-flex row">
            <div class="col-xl-2 col-lg-5 col-md-5 col-12 col-xs-12 p-2">
                <div class="my_lab_img cover_image">
                @if($component->mediaType === 'image')
                    <img src="{{ $component->media }}" alt="" onerror="imageError(this)" style="width: 100%;">
                @elseif($component->mediaType === 'embedded')
                    <div class="embed-responsive embed-responsive-21by9" style="height:122px !important;">
                        {!! str_replace(env('AWS_URL').'/', " ", $component->media) !!}
                    </div>
                @else
                    <video id="video-banner" class="img-thumbnail" controls>
                        <source src="{{ $component->media }}">
                    </video>
                @endif
                </div>
            </div>
            <div class="col-lg-7 col-md-7 col-xl-10 col-12 p-2">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="">
                            <h6 class="explore-item-title ttle_break">{{ $component->title }}</h6>
                        </a>
                    </div>
                </div>
                <div class="mt-2">
                    <button class="btn btn-success" style="float: right;" compId="{{$component->id}}" compType="{{$component->type}}" onclick="insertData({{ $component->id }}, '{{$component->type }}')">
                        add</button>
                </div>
            </div>
        </div>
       
    </div>
</div>
@endforeach
