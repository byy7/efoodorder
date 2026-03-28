<header class="top-header">
    <nav class="navbar navbar-expand align-items-center gap-4">
        <div class="btn-toggle">
            <a href="javascript:;"><i class="material-icons-outlined">menu</i></a>
        </div>
        <div class="flex-grow-1">
        </div>
        <ul class="navbar-nav gap-1 nav-right-links align-items-center">
            <li class="nav-item dropdown">
                <a href="javascript:;" class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
                    <img src="{{ asset('assets/img/user.webp') }}" class="rounded-circle p-1 border" width="45"
                         height="45"
                         alt="user">
                </a>
                <div class="dropdown-menu dropdown-user dropdown-menu-end shadow">
                    <a class="dropdown-item  gap-2 py-2" href="javascript:;">
                        <div class="text-center">
                            <img src="{{ asset('assets/img/user.webp') }}" class="rounded-circle p-1 shadow mb-3"
                                 width="90"
                                 height="90"
                                 alt="user">
                            <h5 class="user-name mb-0 fw-bold">{{ auth()->user()->name  }}</h5>
                            <h6 class="user-name mb-0 fw-bold">{{ auth()->user()->getRoleNames()->first() }}</h6>
                        </div>
                    </a>
                    <hr class="dropdown-divider">
{{--                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;"><i--}}
{{--                            class="material-icons-outlined"></i>Profile</a>--}}
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="javascript:;" id="logoutBtn"><i
                            class="material-icons-outlined"></i>Logout</a>
                </div>
            </li>
        </ul>
    </nav>
</header>
