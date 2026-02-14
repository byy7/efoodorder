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
        <h6 class="back-title mb-0">NEED AND NOTES CAFFE</h6>
    </div>
</header>
<!--end header-->

<!--main content-->
<main class="main-content">
    <div class="card shadow-none bg-transparent bg-none">
        <div class="card-body">
            <div class="text-center">
                <img src="{{ asset('assets/img/logo.webp') }}" class="img-fluid" alt="Logo" width="150">
            </div>
            <div class="row">
                <h4 class="mb-1">Selamat Datang 👋</h4>
                <p>Silahkan pilih jenis layanan</p>
                <div class="mt-2">
                    <div class="col-12 mb-4">
                        <div class="d-grid">
                            <a href="#" class="btn btn-grd btn-lg btn-grd-primary">DINE IN</a>
                        </div>
                    </div>
                    <div class="col-12 mb-4">
                        <div class="d-grid">
                            <a href="#" class="btn btn-grd btn-lg btn-grd-info">TAKE AWAY</a>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <p>Selamat menikmati layanan kami 😄</p>
                </div>
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
