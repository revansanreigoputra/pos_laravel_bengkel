<aside class="navbar navbar-vertical navbar-expand-lg">
    <div class="container-fluid">
<<<<<<< HEAD
        <!-- BEGIN NAVBAR TOGGLER -->
=======
        <!-- NAVBAR TOGGLER -->
>>>>>>> 690f4e2 (Refactor Sparepart and Supplier Models and Controllers)
        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- END NAVBAR TOGGLER -->
        <!-- BEGIN NAVBAR LOGO -->
        <div class="navbar-brand navbar-brand-autodark">
            <a href="#" aria-label="Tabler" class="d-flex align-items-center">
                <!-- Logo Image -->
                <img src="{{ asset('assets/logo.png') }}" alt="Logo BengkelKu" class="me-2" style="height: 50px;">
                <!-- Text -->
                <span>BengkelKu</span>
            </a>
        </div>
        <!-- END NAVBAR LOGO -->
        <div class="navbar-collapse collapse" id="sidebar-menu" style="">
            <!-- BEGIN NAVBAR MENU -->
            <ul class="navbar-nav pt-lg-3">
                <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Download SVG icon from http://tabler.io/icons/icon/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-1">
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0"></path>
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7"></path>
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6"></path>
                            </svg>
                        </span>
                        <span class="nav-link-title"> Dashboard </span>
                    </a>
                </li>
<<<<<<< HEAD
                <li
                    class="nav-item dropdown {{ request()->is('roles*') || request()->is('kategori*') || request()->is('supplier*') || request()->is('konsumen*') || request()->is('user*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                        data-bs-auto-close="false" role="button" aria-expanded="false">
=======

                <!-- Master Data -->
                <li
                    class="nav-item dropdown {{ request()->is('kategori*') || request()->is('sparepart*') || request()->is('service*') || request()->is('supplier*') || request()->is('konsumen*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" aria-expanded="false">
>>>>>>> 690f4e2 (Refactor Sparepart and Supplier Models and Controllers)
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-1">
                                <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5"></path>
                                <path d="M12 12l8 -4.5"></path>
                                <path d="M12 12l0 9"></path>
                                <path d="M12 12l-8 -4.5"></path>
                                <path d="M16 5.25l-8 4.5"></path>
                            </svg></span>
                        <span class="nav-link-title"> Master Data </span>
                    </a>
                    <div
<<<<<<< HEAD
                        class="dropdown-menu {{ request()->is('roles*') || request()->is('kategori*') || request()->is('supplier*') || request()->is('konsumen*') || request()->is('user*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @can('category.view')
                                <a class="dropdown-item {{ request()->is('kategori*') ? 'active' : '' }}"
                                    href="{{ route('category.index') }}"> Kategori </a>
                                @endcan
                                <a class="dropdown-item" href="./markdown.html"> Produk </a>
                                @can('user.view')
                                <a class="dropdown-item {{ request()->is('user*') ? 'active' : '' }}"
                                    href="{{ route('user.index') }}"> User </a>
                                @endcan
                                @can('customer.view')
                                <a class="dropdown-item {{ request()->is('konsumen*') ? 'active' : '' }}"
                                    href="{{ route('customer.index') }}"> Konsumen </a>
                                @endcan
                                @can('supplier.view')
                                <a class="dropdown-item {{ request()->is('supplier*') ? 'active' : '' }}"
                                    href="{{ route('supplier.index') }}"> Supplier </a>
                                @endcan
                                @can('role.view')
                                <a class="dropdown-item {{ request()->is('roles*') ? 'active' : '' }}"
                                    href="{{ route('roles.index') }}"> Hak Akses </a>
=======
                        class="dropdown-menu {{ request()->is('kategori*') || request()->is('sparepart*') || request()->is('service*') || request()->is('supplier*') || request()->is('konsumen*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @can('category.view')
                                    <a class="dropdown-item {{ request()->is('kategori*') ? 'active' : '' }}"
                                        href="{{ route('category.index') }}">Kategori</a>
                                @endcan
                                @can('sparepart.view')
                                    <a class="dropdown-item {{ request()->is('sparepart*') ? 'active' : '' }}"
                                        href="{{ route('sparepart.index') }}">Sparepart</a>
                                @endcan
                                @can('service.view')
                                    <a class="dropdown-item {{ request()->is('service*') ? 'active' : '' }}"
                                        href="{{ route('service.index') }}">Servis</a>
                                @endcan
                                @can('supplier.view')
                                    <a class="dropdown-item {{ request()->is('supplier*') ? 'active' : '' }}"
                                        href="{{ route('supplier.index') }}">Supplier</a>
                                @endcan
                                @can('customer.view')
                                    <a class="dropdown-item {{ request()->is('konsumen*') ? 'active' : '' }}"
                                        href="{{ route('customer.index') }}">Konsumen</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Transaksi -->
                <li
                    class="nav-item dropdown {{ request()->is('pembelian*') || request()->is('transaction*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon Transaksi -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-1" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 12h18" />
                                <path d="M3 6h18" />
                                <path d="M3 18h18" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Transaksi</span>
                    </a>
                    <div
                        class="dropdown-menu {{ request()->is('pembelian*') || request()->is('transaction*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item {{ request()->is('stock-handle*') ? 'active' : '' }}"
                                    href="{{ route('stock-handle.index') }}">Pembelian Sparepart</a>
                                @can('transaction.view')
                                    <a class="dropdown-item {{ request()->is('transaction*') ? 'active' : '' }}"
                                        href="{{ route('transaction.index') }}">Penjualan Sparepart</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Laporan -->
                <li class="nav-item dropdown {{ request()->is('laporan*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon Laporan -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-1" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 3v18h18" />
                                <path d="M9 17l3-3l4 4" />
                                <path d="M13 13l2-2l3 3" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Laporan</span>
                    </a>
                    <div class="dropdown-menu {{ request()->is('laporan*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @can('report.transaction')
                                    <a class="dropdown-item {{ request()->is('laporan/transaksi') ? 'active' : '' }}"
                                        href="{{ route('laporan.transaksi') }}">Laporan Transaksi</a>
                                @endcan
                                @can('report.purchase')
                                    <a class="dropdown-item {{ request()->is('laporan/pembelian') ? 'active' : '' }}"
                                        href="{{ route('laporan.pembelian') }}">Laporan Pembelian</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </li>

                <!-- Manajemen Pengguna -->
                <li class="nav-item dropdown {{ request()->is('user*') || request()->is('roles*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" aria-expanded="false">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <!-- Icon User -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-1" width="24"
                                height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M8 3.13a4 4 0 0 0 0 7.75" />
                                <path d="M2 21v-2a4 4 0 0 1 4-4h12a4 4 0 0 1 4 4v2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Manajemen Pengguna</span>
                    </a>
                    <div class="dropdown-menu {{ request()->is('user*') || request()->is('roles*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @can('user.view')
                                    <a class="dropdown-item {{ request()->is('user*') ? 'active' : '' }}"
                                        href="{{ route('user.index') }}">User</a>
                                @endcan
                                @can('role.view')
                                    <a class="dropdown-item {{ request()->is('roles*') ? 'active' : '' }}"
                                        href="{{ route('roles.index') }}">Hak Akses</a>
>>>>>>> 690f4e2 (Refactor Sparepart and Supplier Models and Controllers)
                                @endcan
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <!-- END NAVBAR MENU -->
        </div>
    </div>
</aside>
<<<<<<< HEAD
=======

<!-- SCRIPT -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropdowns = document.querySelectorAll('.nav-item.dropdown');

        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.nav-link.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');

            toggle.addEventListener('click', function(e) {
                e.preventDefault();

                const isShown = menu.classList.contains('show');

                // Tutup semua dropdown lain
                dropdowns.forEach(other => {
                    if (other !== dropdown) {
                        other.querySelector('.dropdown-menu')?.classList.remove('show');
                        other.querySelector('.dropdown-toggle')?.setAttribute(
                            'aria-expanded', 'false');
                    }
                });

                // Toggle menu sekarang
                if (!isShown) {
                    menu.classList.add('show');
                    toggle.setAttribute('aria-expanded', 'true');
                } else {
                    menu.classList.remove('show');
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        });

        // Klik di luar sidebar, tutup semua
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.navbar-vertical')) {
                dropdowns.forEach(dropdown => {
                    dropdown.querySelector('.dropdown-menu')?.classList.remove('show');
                    dropdown.querySelector('.dropdown-toggle')?.setAttribute('aria-expanded',
                        'false');
                });
            }
        });
    });
</script>
>>>>>>> 690f4e2 (Refactor Sparepart and Supplier Models and Controllers)
