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

{{-- Logout --}}
<form id="logoutForm" method="POST" action="{{ route('logout') }}">
    @csrf
</form>

<script>
    $(document).on('click', '#logoutBtn', function (e) {
        e.preventDefault();
        $("#logoutForm").submit();
    });

    window.addEventListener('show-notification', (event) => {
        const type = event.detail[0].type || 'success';
        const message = event.detail[0].message || 'Operation completed successfully!';

        Lobibox.notify(type, {
            pauseDelayOnHover: true,
            size: 'mini',
            rounded: true,
            icon: getNotificationIcon(type),
            delayIndicator: false,
            continueDelayOnInactiveTab: false,
            position: 'top right',
            msg: message
        });
    });

    function getNotificationIcon(type) {
        const icons = {
            'success': 'bi bi-check2-circle',
            'error': 'bi bi-x-circle',
            'warning': 'bi bi-exclamation-triangle',
            'info': 'bi bi-info-circle'
        };
        return icons[type] || 'bi bi-check2-circle';
    }

</script>

</body>
</html>
