{{-- Logout Form --}}
<form id="logoutForm" method="POST" action="{{ route('logout') }}">
    @csrf
</form>

<script>
    /* Logout */
    $(document).on('click', '#logoutBtn', function (e) {
        e.preventDefault();
        $("#logoutForm").submit();
    });

    /* Notifications */
    window.addEventListener('show-notification', (event) => {
        const type = event.detail.type || 'success';
        const message = event.detail.message || 'Operation completed successfully!';

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

    /* Modal */
    let modal;

    window.addEventListener('show-modal', (event) => {
        let mode = event.detail.mode;

        if (mode === 'create' || mode === 'edit') {
            modal = new bootstrap.Modal(
                document.getElementById('formModal')
            );
        } else if (mode === 'delete') {
            modal = new bootstrap.Modal(
                document.getElementById('deleteModal')
            );
        }

        modal.show();
    });

    window.addEventListener('hide-modal', () => {
        modal?.hide();
    });
</script>
