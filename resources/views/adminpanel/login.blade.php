<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('constants.app_name') }}<| Log in (v2)</title>
            {{-- <link rel="icon" type="image/x-icon" href="{{ url('adminpanel/dist/img/logo_oodler.jpg') }}"> --}}
            <link rel="icon" href="{{ url('adminpanel/dist/img/favicon1.png') }}" sizes="32x32" />
            <link rel="icon" href="{{ url('adminpanel/dist/img/favicon2.png') }}" sizes="192x192" />
            <link rel="apple-touch-icon" href="{{ url('adminpanel/dist/img/favicon3.png') }}" />
            <meta name="msapplication-TileImage" content="{{ url('adminpanel/dist/img/favicon1.png') }}" />

            <!-- Google Font: Source Sans Pro -->
            <link rel="stylesheet"
                href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
            <!-- Font Awesome -->
            <link rel="stylesheet" href="{{ url('adminpanel/plugins/fontawesome-free/css/all.min.css') }}">
            <!-- icheck bootstrap -->
            <link rel="stylesheet" href="{{ url('adminpanel/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
            <!-- Theme style -->
            <link rel="stylesheet" href="{{ url('adminpanel/dist/css/adminlte.min.css') }}">
            <style>
                .card-primary.card-outline {
                    border-top: none !important;
                }
            </style>
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div style="background: #343a40;" class="card-header text-center">
                <a href="{{ url()->current() }}" class="h1"><b><img
                            src="{{ url('adminpanel/dist/img/oodler-Final-logo-white.png') }}" alt="OodlerExpress CRM"
                            width="80%"></b></a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Sign in to start your session </p>
                <!-- flash-message -->
                <div class="flash-message">

                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                        @if (Session::has('alert-' . $msg))
                            <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a
                                    href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            </p>
                        @endif
                    @endforeach
                </div> <!-- end .flash-message -->

                <form action="{{ url('admin/login') }}" method="post">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="Email" value="{{ old('email') }}">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" id="myPassword"
                            placeholder="Password">
                        <div onclick="toggleInput()" class="input-group-append" style="cursor:pointer">
                            <div class="input-group-text">
                                <span class="fas fa-eye"></span>
                            </div>
                        </div>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mt-4 mb-4">
                                <div class="captcha">
                                    <span>{!! captcha_img() !!}</span>
                                    <button type="button" class="btn btn-danger" class="reload" id="reload">
                                        &#x21bb;
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input id="captcha" type="text" name="captcha"
                            class="form-control @error('captcha') is-invalid @enderror" 
                            placeholder="Enter Captcha">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                        @error('captcha')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="row">
                        <div class="col-8">
                            {{-- <div class="icheck-primary">
                                <input type="checkbox" name="remember" id="remember">
                                <label for="remember">
                                  
                                </label>
                            </div> --}}
                            &nbsp;
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                {{-- <div class="social-auth-links text-center mt-2 mb-3">
                    <a href="#" class="btn btn-block btn-primary">
                        <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
                    </a>
                    <a href="#" class="btn btn-block btn-danger">
                        <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
                    </a>
                </div> --}}
                <!-- /.social-auth-links -->

                {{-- <p class="mb-1">
                    <a href="forgot-password.html">I forgot my password</a>
                </p> --}}
                {{-- <p class="mb-0">
                    <a href="{{url('/admin/register')}}" class="text-center">Register a new membership</a>
                </p> --}}
                @if (Route::has('password.request'))
                    <span class="mb-0">
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    </span>
                @endif
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="{{ url('adminpanel/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ url('adminpanel/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ url('adminpanel/dist/js/adminlte.min.js') }}"></script>
    <script type="text/javascript">
        function toggleInput() {
            var x = document.getElementById("myPassword");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        $('#reload').click(function() {
            $.ajax({
                type: 'GET',
                url: "{{route('reloadCaptcha')}}",
                success: function(data) {
                    $(".captcha span").html(data.captcha);
                }
            });
        });
    </script>
</body>

</html>
