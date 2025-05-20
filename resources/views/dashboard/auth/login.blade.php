<!DOCTYPE html>
<html lang="en" data-textdirection="ltr" class="loading">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Robust admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, robust admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <title>Login Page</title>
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('dashboard/app-assets/images/ico/apple-icon-60.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('dashboard/app-assets/images/ico/apple-icon-76.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('dashboard/app-assets/images/ico/apple-icon-120.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('dashboard/app-assets/images/ico/apple-icon-152.png') }}">

    <!-- Favicons -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('dashboard/app-assets/images/ico/favicon.ico') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('dashboard/app-assets/images/ico/favicon-32.png') }}">

    <!-- Meta for Apple Web App -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <!-- VENDOR CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/app-assets/fonts/icomoon.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/app-assets/fonts/flag-icon-css/css/flag-icon.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/app-assets/vendors/css/extensions/pace.css') }}">

    <!-- ROBUST CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/app-assets/css/app.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/app-assets/css/colors.css') }}">

    <!-- PAGE LEVEL CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/app-assets/css/core/menu/menu-types/vertical-overlay-menu.css') }}">

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('dashboard/assets/css/style.css') }}">
    <!-- END Custom CSS-->
</head>
<body data-open="click" data-menu="vertical-menu" data-col="1-column" class="vertical-layout vertical-menu 1-column blank-page blank-page">
<!-- ////////////////////////////////////////////////////////////////////////////-->
<div class="app-content content container-fluid">
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body"><section class="flexbox-container">
                <div class="col-md-4 offset-md-4 col-xs-10 offset-xs-1  box-shadow-2 p-0">
                    <div class="card border-grey border-lighten-3 m-0">
                        <div class="card-header no-border">
                            <div class="card-title text-xs-center">
                                <div class="p-1"><img src="{{ asset('dashboard/app-assets/images/logo/robust-logo-dark.png') }}" alt="branding logo"></div>
                            </div>
                            <h6 class="card-subtitle line-on-side text-muted text-xs-center font-small-3 pt-2"><span>{{ __('Login') }}</span></h6>
                        </div>
                        <div class="card-body collapse in">
                            <div class="card-block">
                                <form class="form-horizontal form-simple" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <fieldset class="form-group position-relative has-icon-left mb-0">
                                        <input type="email" class="form-control form-control-lg input-lg @error('email') is-invalid @enderror"
                                               id="email" name="email" value="{{ old('email') }}"
                                               placeholder="Your Email Address" required autocomplete="email" autofocus>
                                        <div class="form-control-position">
                                            <i class="icon-head"></i>
                                        </div>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </fieldset>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <input type="password" class="form-control form-control-lg input-lg @error('password') is-invalid @enderror"
                                               id="password" name="password"
                                               placeholder="Enter Password" required autocomplete="current-password">
                                        <div class="form-control-position">
                                            <i class="icon-key3"></i>
                                        </div>
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </fieldset>
                                    <fieldset class="form-group row">
                                        <div class="col-md-6 col-xs-12 text-xs-center text-md-left">
                                            <fieldset>
                                                <input type="checkbox" id="remember" name="remember" class="chk-remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label for="remember"> {{ __('Remember Me') }}</label>
                                            </fieldset>
                                        </div>
                                        <div class="col-md-6 col-xs-12 text-xs-center text-md-right">
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="card-link">{{ __('Forgot Your Password?') }}</a>
                                            @endif
                                        </div>
                                    </fieldset>
                                    <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="icon-unlock2"></i> {{ __('Login') }}</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="">
                                @if (Route::has('password.request'))
                                    <p class="float-sm-left text-xs-center m-0">
                                        <a href="{{ route('password.request') }}" class="card-link">{{ __('Recover password') }}</a>
                                    </p>
                                @endif
                                @if (Route::has('register'))
                                    <p class="float-sm-right text-xs-center m-0">
                                        New to site? <a href="{{ route('register') }}" class="card-link">{{ __('Sign Up') }}</a>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>
<!-- ////////////////////////////////////////////////////////////////////////////-->
<!-- BEGIN VENDOR JS-->
<script src="{{ asset('dashboard/app-assets/js/core/libraries/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/ui/tether.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('dashboard/app-assets/js/core/libraries/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/ui/perfect-scrollbar.jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/ui/unison.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/ui/blockUI.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/ui/jquery.matchHeight-min.js') }}" type="text/javascript"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/ui/screenfull.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('dashboard/app-assets/vendors/js/extensions/pace.min.js') }}" type="text/javascript"></script>
<!-- END VENDOR JS-->

<!-- BEGIN ROBUST JS-->
<script src="{{ asset('dashboard/app-assets/js/core/app-menu.js') }}" type="text/javascript"></script>
<script src="{{ asset('dashboard/app-assets/js/core/app.js') }}" type="text/javascript"></script>
<!-- END ROBUST JS-->
<!-- BEGIN PAGE LEVEL JS-->
<!-- END PAGE LEVEL JS-->
</body>
</html>
