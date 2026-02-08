<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="semi-dark">
<head>
    @include('partials.head')
</head>
<body>
<!--start header-->
@include('partials.navbar')
<!--end top header-->

<!--start sidebar-->
@include('partials.sidebar')
<!--end sidebar-->

<!--start main wrapper-->
<main class="main-wrapper">
    {{ $slot }}
</main>
<!--end main wrapper-->

<!--start footer-->
<footer class="page-footer">
    <p class="mb-0">Copyright © 2026. All right reserved.</p>
</footer>
<!--end footer-->

@include('partials.foot')
</body>
</html>
