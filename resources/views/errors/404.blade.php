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
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
        integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"
        integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous">
    </script>
    <!-- Css Stylesheet -->
    <link id="theme" rel="stylesheet" href="{{ asset('errors/css/styles.css') }}">
    <title>404 - Prepr Network</title>

    <style>
        body {
            background-image: url(../ErrorPageOverlayBG.png);
            background-repeat: no-repeat;
            background-size: cover;
        }

        .container-small {
            max-width: 100%;
            margin: 0 auto;
            font-family: 'Poppins', sans-serif !important;
            font-weight: 300;
        }

        .gif {
            display: block;
            margin: 0 auto;
            max-width: 100%;
            max-height: 100%;
        }

        #hidden_gif {
            display: none;
        }

        .code_image {
            margin-bottom: 1.5rem;
            width: 70%;
        }

        .container-small .row .btn-success {
            background-color: #5aad50 !important;
            font-family: 'Poppins', sans-serif !important;
            font-weight: bold;

        }

        .container-small .row h5,
        h4 {
            font-weight: bold;
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
                background-image: none;
                background-color: #f2f2f2;
            }

            .gif {
                display: none;
            }

            #hidden_gif {
                display: block;
            }

            .container-small .row {
                text-align: center;
            }
        }

        @media (min-width: 768px) {
            .container-small {
                width: 600px;
                margin: 100px auto;
            }
        }

        @media (min-width: 992px) {
            .container-small {
                width: 800px;
            }
        }

        @media (min-width: 1200px) {
            .container-small {
                width: 1100px;
            }
        }
    </style>
</head>

<body>
    <div class="container-small">
        <div class="row">
            <div class="col-md-6 col-12">
                <img class="gif" src="{{ asset('404_GIF_1000.gif')}}" alt="404 gif">
            </div>
            <div class="col-md-6 col-12">
                <img class="code_image" src="{{ asset('404.png')}}" alt="404 image">
                <h4>Looks like you're lost! But don't worry, we'll fly you back home in no time.</h4>
                <a class="btn btn-success text-white m-2" href="https://preprlabs.org">Home</a>
                <a class="btn btn-success text-white m-2" href="https://preprlabs.org">Go To Preprlabs</a>
                <h5 class="mt-2">What is an error 404?</h5>
                <p>The HTTP error 404, or more commonly called '404 error', means that the page you're trying to open
                    could not be found on the server. This is a client-side incident which means either the page has
                    been deleted or moved, and the URL has not been modified accordingly, or that you have misspelled
                    the URL.</p>
                <img id="hidden_gif" class="gif" src="{{ asset('404_GIF_1000.gif')}}" alt="">

            </div>
        </div>
    </div>
</body>

</html>