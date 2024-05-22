<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS only -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
        integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <!-- JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
        crossorigin="anonymous"></script>
    <!-- Css Stylesheet -->
    <link id="theme" rel="stylesheet" href="{{ asset('errors/css/styles.css') }}">
    <title>{{Settings::get('site_name')}} | {{ __('labels.labels_serror') }}</title>
</head>

<body>
    <div class="container-small">
        <div class="row">
            <div class="col-md-6 col-12">
                @if(App::currentLocale() == 'fr-CA')
                    <img class="gif" src="{{ asset('errors/img/500_GIF_FR.gif') }}" alt="500 gif">
                @else
                    <img class="gif" src="{{ asset('errors/img/500_GIF_1000.gif') }}" alt="500 gif">
                @endif
            </div>
            <div class="col-md-6 col-12">
                <img class="code_image" src="{{ asset('errors/img/500.png')}}" alt="500 image">
                <h4>{{ __('labels.labels_llwhsipal') }}</h4>
                <a class="btn btn-success text-white m-2" href="{{url('/')}}">{{ __('labels.labels_home') }}</a>
                <a class="btn btn-success text-white m-2" href="{{url('/labs/technical-qa-analyst-career-lab')}}">{{ __('labels.labels_bcaqaa') }}</a>
                <h5 class="mt-2">{{ __('labels.labels_wiaefh') }}</h5>
                <p>{{ __('labels.labels_thscigerc') }}</p>
                @if(App::currentLocale() == 'fr-CA')
                    <img id="hidden_gif" class="gif" src="{{ asset('errors/img/500_GIF_FR.gif') }}" alt="">
                @else
                    <img id="hidden_gif" class="gif" src="{{ asset('errors/img/500_GIF_1000.gif') }}" alt="">
                @endif
            </div>
        </div>
    </div>
</body>

</html>
