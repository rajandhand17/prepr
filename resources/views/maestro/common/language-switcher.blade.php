<div class="pull-right">
    <select class="form-control" id="selectlang" onchange="switchLanguage(this.value)">
        @php
        $languages = \App\Models\Language::where('status', 1)->get();
        if(!empty(Session::get('globalLocale'))){
            $selectedLanguage = \Session::get('globalLocale');
        } else {
            $selectedLanguage = 'en';
        }
        @endphp
        @if(!empty($languages))
            @foreach($languages as $key => $lang)
                <option value="{{ $lang->iso }}" @if($lang->iso == $selectedLanguage) selected @endif> {{ $lang->name }}</option>
            @endforeach
        @endif
    </select>
</div>
<script>
    function switchLanguage(language){
        $.ajax({
                url: '{{route('switchLanguage')}}',
                type: 'POST',
                data: {
                        'language' : language
                },
                success: function (result) {
                    window.location.reload(true);
                },
            });
    }
</script>