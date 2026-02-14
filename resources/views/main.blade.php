<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="blue-theme">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="icon" href="{{ asset('assets/img/logo.webp') }}">
    <!-- loader-->
    <link href="{{ asset('assets/template/mobile-app/demo/assets/css/pace.min.css') }}" rel="stylesheet">
    <script src="{{ asset('assets/template/mobile-app/demo/assets/js/pace.min.js') }}"></script>

    <!-- Link Swiper's CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

    <!--bootstrap css-->
    <link href="{{ asset('assets/template/mobile-app/demo/assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
    <!--main css-->
    <link href="{{ asset('assets/template/mobile-app/demo/assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/template/mobile-app/demo/sass/main.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/template/mobile-app/demo/sass/dark-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/template/mobile-app/demo/sass/blue-theme.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/template/mobile-app/demo/sass/responsive.css') }}" rel="stylesheet">

    <script src="{{ asset('assets/template/mobile-app/demo/assets/js/jquery.min.js') }}"></script>

</head>

<body>

<!--start header-->
<header
    class="mobile-top-header border-bottom position-fixed top-0 start-0 end-0 d-flex align-items-center justify-content-between px-3 bg-grd-primary">
    <div class="d-flex align-items-center gap-3">
        <h6 class="back-title mb-0">ONLINE APPS NEED AND NOTES</h6>
    </div>
</header>
<!--end header-->

<!--main content-->
<main class="main-content m-3">
    <div class="card shadow-none bg-transparent bg-none">
        <div class="card-body">
            <div class="text-center">
                <img src="{{ asset('assets/img/logo.webp') }}" class="img-fluid" alt="Logo" width="120">
            </div>
            <div class="form-body">
                <h4 class="mb-1">Welcome Back</h4>
                <p>Login to your account to continue</p>
                <form class="mt-4">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="position-relative">
                                <label for="enterMobile" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control form-control-lg ps-5" id="enterMobile"
                                       placeholder="Mobile Number">
                                <span
                                    class="material-icons-outlined position-absolute top-50 start-0 translate-middle-x ms-4">phone_iphone</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="position-relative">
                                <label for="InputPassword" class="form-label">Password</label>
                                <input type="text" class="form-control form-control-lg ps-5" id="InputPassword"
                                       placeholder="Password">
                                <span
                                    class="material-icons-outlined position-absolute top-50 start-0 translate-middle-x ms-4">lock</span>
                                <span
                                    class="material-icons-outlined position-absolute top-50 end-0 translate-middle-x me-0">visibility_off</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="flexCheckDefault">
                                    <label class="form-check-label" for="flexCheckDefault">Remember</label>
                                </div>
                                <div><a href="verify-account.html" class="forgot-link">Forgot Password?</a></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="d-grid">
                                <a href="home.html" class="btn btn-grd btn-lg btn-grd-primary">Login</a>
                            </div>
                            <div class="separator my-4">
                                <div class="line"></div>
                                <p class="mb-0 fw-bold px-3">OR</p>
                                <div class="line"></div>
                            </div>
                            <div class="d-flex gap-3 justify-content-center">
                                <a href="javascript:;"
                                   class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-grd-danger">
                                    <i class="bi bi-google fs-5 text-white"></i>
                                </a>
                                <a href="javascript:;"
                                   class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-grd-deep-blue">
                                    <i class="bi bi-facebook fs-5 text-white"></i>
                                </a>
                                <a href="javascript:;"
                                   class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-grd-info">
                                    <i class="bi bi-linkedin fs-5 text-white"></i>
                                </a>
                                <a href="javascript:;"
                                   class="wh-42 d-flex align-items-center justify-content-center rounded-circle bg-grd-royal">
                                    <i class="bi bi-github fs-5 text-white"></i>
                                </a>
                            </div>
                            <div class="text-center mt-3">
                                <p class="mb-0 rounded"><span class="me-1">Dont have an account ?</span><a
                                        href="register.html">Register</a></p>
                            </div>
                        </div>

                    </div><!--end row-->
                </form>
            </div>
        </div>
    </div>
</main>
<!--main content-->

<!--bootstrap js-->
<script src="{{ asset('assets/template/mobile-app/demo/assets/js/bootstrap.bundle.min.js') }}"></script>

<!--jquery-->
<script src="{{ asset('assets/template/mobile-app/demo/assets/js/main.js') }}"></script>

</body>

</html>
