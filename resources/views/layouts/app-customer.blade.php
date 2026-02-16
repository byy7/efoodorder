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
    <link rel="stylesheet"
          href="{{ asset('assets/template/vertical-menu/assets/plugins/notifications/css/lobibox.min.css') }}">
    <script src="{{ asset('assets/template/mobile-app/demo/assets/js/jquery.min.js') }}"></script>

</head>

<body>

<!--main content-->
<main class="main-content">
    {{ $slot }}
</main>
<!--main content-->

<!--bootstrap js-->
<script src="{{ asset('assets/template/mobile-app/demo/assets/js/bootstrap.bundle.min.js') }}"></script>

<script src="{{ asset('assets/template/vertical-menu/assets/plugins/notifications/js/lobibox.min.js') }}"
        data-navigate-once></script>
<script
    src="{{ asset('assets/template/vertical-menu/assets/plugins/notifications/js/notifications.min.js') }}"
    data-navigate-once></script>

@include('partials.custom-script')
</body>

</html>
