@php
    // Initialize an empty array to track displayed component types
    $displayedTypes = [];
@endphp

@if($components->count() >= 1)
@foreach($components as $component)
<div class="col-12 my-2">
    <div class="explore_section explore-item">
        {{-- Display component type only once for each unique type --}}
        @if(!in_array($component->type, $displayedTypes))
            <div class="component-type">
                {{ ucfirst($component->type) }}
            </div>
            @php
                // Add the current component type to the displayed types array
                $displayedTypes[] = $component->type;
            @endphp
        @endif

        <div class="d-flex row">
            <div class="col-xl-2 col-lg-5 col-md-5 col-12 col-xs-12 p-2">
                <div class="my_lab_img cover_image">
                    @if($component->media_type === 'image' || $component->media_type == '0')
                        <img src="{{ $component->media }}" alt="" 
                            onerror="this.onerror=null;this.src='{{ config('site-settings.aws_url') . 'public/front/img/no-img.jpg' }}'" 
                            style="width: 100%;">
                    @elseif($component->media_type === 'embedded' || $component->media_type == '1')
                        <div class="embed-responsive embed-responsive-21by9" style="height:122px !important;">
                            {!! str_replace(env('AWS_URL').'/', " ", $component->media) !!}
                        </div>
                    @else
                        <img src="{{ config('site-settings.aws_url') . 'public/front/img/no-img.jpg' }}" style="width: 100%;">
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
                    <button class="btn btn-success" style="float: right;" compId="{{$component->id}}" compType="{{$component->type}}" 
                            onclick="insertData({{ $component->id }}, '{{$component->type }}')">
                        Add
                    </button>
                </div>
            </div>
        </div>
       
    </div>
</div>
@endforeach
@else
<div class ="col-12 my-2">
    <h6> No result found, please try different keywords.</h6>
</div>
@endif
