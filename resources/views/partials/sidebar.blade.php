<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div class="logo-icon">
            <img src="{{ asset('assets/img/logo.webp') }}" class="logo-img" alt="logo">
        </div>
        <div class="logo-name flex-grow-1">
            <h5 class="mb-0">E FOOD</h5>
        </div>
        <div class="sidebar-close">
            <span class="material-icons-outlined">close</span>
        </div>
    </div>
    <div class="sidebar-nav">
        <ul class="metismenu" id="sidenav">
            <li class="menu-label">NAVIGASI</li>
            <li {{ request()->routeIs('dashboard') ?? 'mm-active' }}>
                <a href="{{ route('dashboard') }}" wire:navigate>
                    <div class="parent-icon"><i class="material-icons-outlined">home</i>
                    </div>
                    <div class="menu-title">Dashboard</div>
                </a>
            </li>
            <li {{ request()->routeIs('orders') ?? 'mm-active' }}>
                <a href="{{ route('orders') }}" wire:navigate>
                    <div class="parent-icon"><i class="material-icons-outlined">shop</i>
                    </div>
                    <div class="menu-title">Pesanan</div>
                </a>
            </li>
            <li {{ request()->routeIs('customers') ?? 'mm-active' }}>
                <a href="{{ route('customers') }}" wire:navigate>
                    <div class="parent-icon"><i class="material-icons-outlined">group</i>
                    </div>
                    <div class="menu-title">Pelanggan</div>
                </a>
            </li>
            <li {{ request()->routeIs('categories') ?? 'mm-active' }}>
                <a href="{{ route('categories') }}" wire:navigate>
                    <div class="parent-icon"><i class="material-icons-outlined">category</i>
                    </div>
                    <div class="menu-title">Kategori</div>
                </a>
            </li>
            <li {{ request()->routeIs('products') ?? 'mm-active' }}>
                <a href="{{ route('products') }}" wire:navigate>
                    <div class="parent-icon"><i class="material-icons-outlined">inventory_2</i>
                    </div>
                    <div class="menu-title">Produk</div>
                </a>
            </li>
            <li {{ request()->routeIs('customers') ?? 'mm-active' }}>
                <a href="{{ route('customers') }}" wire:navigate>
                    <div class="parent-icon"><i class="material-icons-outlined">table_bar</i>
                    </div>
                    <div class="menu-title">Meja</div>
                </a>
            </li>
            <li {{ request()->routeIs('users') ?? 'mm-active' }}>
                <a href="{{ route('users') }}" wire:navigate>
                    <div class="parent-icon"><i class="material-icons-outlined">person</i>
                    </div>
                    <div class="menu-title">Pengguna</div>
                </a>
            </li>
            <li {{ request()->routeIs('reports') ?? 'mm-active' }}>
                <a href="{{ route('reports') }}" wire:navigate>
                    <div class="parent-icon"><i class="material-icons-outlined">description</i>
                    </div>
                    <div class="menu-title">Laporan</div>
                </a>
            </li>
{{--            <li {{ request()->routeIs('dashboard') ?? 'mm-active' }}>--}}
{{--                <a href="#" wire:navigate>--}}
{{--                    <div class="parent-icon"><i class="material-icons-outlined">support</i>--}}
{{--                    </div>--}}
{{--                    <div class="menu-title">Panduan Pengguna</div>--}}
{{--                </a>--}}
{{--            </li>--}}
        </ul>
    </div>
</aside>
