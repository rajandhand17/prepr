<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login - Prepr Network</title>
  <link rel="dns-prefetch" href="//fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/fontawesome-free/css/all.min.css'}}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/plugins/icheck-bootstrap/icheck-bootstrap.min.css'}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{config('site-settings.aws_url').'public/maestro/dist/css/adminlte.min.css'}}">
</head>

<body class="hold-transition login-page" style="background-image: linear-gradient(to right, #d5ffc6, #7BD1F2); color: #000000;">
  <div class="login-box" style="box-shadow: 11px 10px 5px lightslategrey; width: 430px !important;">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="{{ route('login') }}" class="h1"><img src="{{config('site-settings.aws_url').'public/front/img/logoNew.png'}}"> </img> </a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Sign in to Prepr Network</p>

        <form method="POST" action="{{ route('login') }}">
          @csrf
          @if (session('error'))
          <center>
              <span class="errorblock" style="padding: 10px 1px 10px 1px;color: red;">
                  {{ session('error') }}
              </span>
          </center>
          @endif

          <div class="input-group mb-3" style="padding-top: 10px;">
            <input id="email" type="email" placeholder="Email Address" class="form-control"  name="email" value="{{ old('email') }}" required>

            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input id="password" type="password" placeholder="Email Password" class="form-control" name="password" required>

            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                  <label class="form-check-label" for="remember"> {{ __('Remember Me') }} </label>
                </div>
              </div>
            </div>

          </div>

          <div class="social-auth-links text-center mt-2 mb-3">
            <button type="submit" class="btn btn-primary"> {{ __('Login') }} </button>
          </div>
        </form>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="{{config('site-settings.aws_url').'public/maestro/plugins/jquery/jquery.min.js'}}"></script>
  <!-- Bootstrap 4 -->
  <script
    src="{{config('site-settings.aws_url').'public/maestro/plugins/bootstrap/js/bootstrap.bundle.min.js'}}">
  </script>
  <!-- AdminLTE App -->
  <script src="{{config('site-settings.aws_url').'public/maestro/dist/js/adminlte.min.js'}}"></script>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
  @if(Session::has('success'))
      toastr.success("{{ Session::get('success') }}");
  @endif

  @if(Session::has('error'))
      toastr.error("{{ Session::get('error') }}");
  @endif
</script>
</html>