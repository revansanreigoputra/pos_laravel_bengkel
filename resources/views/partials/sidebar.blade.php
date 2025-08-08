<aside class="navbar navbar-vertical navbar-expand-lg">
    <div class="container-fluid">
        <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-brand navbar-brand-autodark">
            <a href="#" class="d-flex align-items-center">
                <img src="{{ asset('assets/logo.png') }}" alt="Logo BengkelKu" class="me-2" style="height: 50px;">
                <span>BengkelKu</span>
            </a>
        </div>

        <div class="navbar-collapse collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">

                <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-1" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12l-2 0l9 -9l9 9l-2 0" />
                                <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item dropdown {{ request()->is('kategori*', 'sparepart*', 'service*', 'supplier*', 'konsumen*', 'jenis-kendaraan*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" 
                       aria-expanded="{{ request()->is('kategori*', 'sparepart*', 'service*', 'supplier*', 'konsumen*', 'jenis-kendaraan*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-1" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3l8 4.5v9l-8 4.5l-8-4.5v-9z" />
                                <path d="M12 12l8-4.5" />
                                <path d="M12 12v9" />
                                <path d="M12 12l-8-4.5" />
                                <path d="M16 5.25l-8 4.5" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Master Data</span>
                    </a>
                    <div class="dropdown-menu {{ request()->is('kategori*', 'sparepart*', 'service*', 'supplier*', 'konsumen*', 'jenis-kendaraan*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @can('category.view')
                                <a class="dropdown-item {{ request()->is('kategori*') ? 'active' : '' }}"
                                    href="{{ route('category.index') }}">Kategori</a>
                                @endcan
                                @can('sparepart.view')
                                <a class="dropdown-item {{ request()->is('sparepart*') ? 'active' : '' }}"
                                    href="{{ route('spareparts.index') }}">Sparepart</a>
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
                                    href="{{ route('customer.index') }}">Pelanggan</a>
                                @endcan
                                @can('jenis-kendaraan.view')
                                <a class="dropdown-item {{ request()->is('jenis-kendaraan*') ? 'active' : '' }}"
                                    href="{{ route('jenis-kendaraan.index') }}">Jenis Kendaraan</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </li>

                <li class="nav-item dropdown {{ request()->is('purchase_orders*', 'transaction*', 'stock-handle*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" 
                       aria-expanded="{{ request()->is('purchase_orders*', 'transaction*', 'stock-handle*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
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
                    <div class="dropdown-menu {{ request()->is('purchase_orders*', 'transaction*', 'stock-handle*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @can('purchase_order.view')
                                <a class="dropdown-item {{ request()->is('purchase_orders*') ? 'active' : '' }}"
                                    href="{{ route('purchase_orders.index') }}">
                                    Transaksi Pembelian
                                </a>
                                @endcan
                                <a href="{{ route('purchase_orders.create') }}" class="dropdown-item {{ request()->is('purchase_orders/create') ? 'active' : '' }} 
                                    add-link"><span class="add-badge">+</span> Tambah</a>
                            </div>
                            <div class="dropdown-menu-column">
                                @can('transaction.view')
                                <a class="dropdown-item {{ request()->is('transaction*') ? 'active' : '' }}"
                                    href="{{ route('transaction.index') }}">Transaksi Penjualan</a>
                                @endcan
                                <a href="{{ route('transaction.create') }}" class="dropdown-item {{ request()->is('transaction/create') ? 'active' : '' }} 
                                    add-link"> <span class="add-badge">+</span> Tambah</a>

                            </div>
                        </div>
                    </div>
                </li>

                {{-- laporan --}}
                <li class="nav-item dropdown {{ request()->is('laporan/penjualan') || request()->is('stok-sparepart') || request()->is('laporan/pembelian') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" 
                    aria-expanded="{{ request()->is('laporan/penjualan') || request()->is('stok-sparepart') || request()->is('laporan/pembelian') ? 'true' : 'false' }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
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
                    <div class="dropdown-menu {{ request()->is('laporan/penjualan') || request()->is('stok-sparepart') || request()->is('laporan/pembelian') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @can('report.purchase')
                                <a class="dropdown-item {{ request()->is('laporan/pembelian') ? 'active' : '' }}"
                                href="{{ route('report.purchase') }}">
                                    Laporan Pembelian
                                </a>
                                @endcan

                                @can('report.transaction')
                                <a class="dropdown-item {{ request()->is('laporan/penjualan') ? 'active' : '' }}"
                                href="{{ route('report.transaction') }}">
                                    Laporan Penjualan
                                </a>
                                @endcan

                                @can('report.sparepart-report')
                                <a class="dropdown-item {{ request()->is('stok-sparepart') ? 'active' : '' }}"
                                href="{{ route('report.sparepart-report') }}">
                                    Laporan Stok Sparepart
                                </a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </li>


                {{-- history --}}
                <li class="nav-item dropdown {{ request()->is('logs*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" 
                       aria-expanded="{{ request()->is('logs*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-analytics" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                               <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                               <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                               <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
                               <path d="M9 17l0 -5"></path>
                               <path d="M12 17l0 -1"></path>
                               <path d="M15 17l0 -3"></path>
                            </svg>
                        </span>
                        <span class="nav-link-title">Riwayat</span>
                    </a>
                    <div class="dropdown-menu {{ request()->is('logs*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                <a class="dropdown-item {{ request()->is('logs/pembelian') ? 'active' : '' }}"
                                   href="{{ route('logs.pembelian') }}">Riwayat Pembelian</a>
                                <a class="dropdown-item {{ request()->is('logs/penjualan') ? 'active' : '' }}"
                                   href="{{ route('logs.penjualan') }}">Riwayat Penjualan</a>
                                {{-- <a class="dropdown-item {{ request()->is('logs/stok') ? 'active' : '' }}"
                                   href="{{ route('logs.stok') }}">Riwayat Stok</a> --}}
                                <a class="dropdown-item {{ request()->is('logs/sparepart') ? 'active' : '' }}"
                                   href="{{ route('logs.sparepart') }}">Riwayat Stok</a>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="nav-item dropdown {{ request()->is('user*', 'roles*') ? 'active' : '' }}">
                    <a class="nav-link dropdown-toggle" href="#" 
                       aria-expanded="{{ request()->is('user*', 'roles*') ? 'true' : 'false' }}">
                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-1" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                <path d="M8 3.13a4 4 0 0 0 0 7.75" />
                                <path d="M2 21v-2a4 4 0 0 1 4-4h12a4 4 0 0 1 4 4v2" />
                            </svg>
                        </span>
                        <span class="nav-link-title">Manajemen Pengguna</span>
                    </a>
                    <div class="dropdown-menu {{ request()->is('user*', 'roles*') ? 'show' : '' }}">
                        <div class="dropdown-menu-columns">
                            <div class="dropdown-menu-column">
                                @can('user.view')
                                <a class="dropdown-item {{ request()->is('user*') ? 'active' : '' }}"
                                    href="{{ route('user.index') }}">User</a>
                                @endcan
                                @can('role.view')
                                <a class="dropdown-item {{ request()->is('roles*') ? 'active' : '' }}"
                                    href="{{ route('roles.index') }}">Hak Akses</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const dropdowns = document.querySelectorAll('.nav-item.dropdown');

        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.nav-link.dropdown-toggle');
            const menu = dropdown.querySelector('.dropdown-menu');

            // Set status awal aria-expanded berdasarkan class 'show' dari Blade.
            if (menu.classList.contains('show')) {
                toggle.setAttribute('aria-expanded', 'true');
            }

            toggle.addEventListener('click', function (e) {
                e.preventDefault();

                const isShown = menu.classList.contains('show');

                // Tutup semua dropdown lain
                dropdowns.forEach(other => {
                    if (other !== dropdown) {
                        other.querySelector('.dropdown-menu')?.classList.remove('show');
                        other.querySelector('.dropdown-toggle')?.setAttribute('aria-expanded', 'false');
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
        
        // Perbaikan: Menghapus event listener global yang agresif.
        // Logika penutupan menu sekarang akan ditangani oleh sistem navigasi halaman.
        // Saat halaman dimuat ulang, server akan me-render menu dengan status 'show' yang benar.
        // Bagian di bawah ini adalah yang dihapus dari kode Anda sebelumnya:
        /*
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.navbar-vertical')) {
                dropdowns.forEach(dropdown => {
                    dropdown.querySelector('.dropdown-menu')?.classList.remove('show');
                    dropdown.querySelector('.dropdown-toggle')?.setAttribute('aria-expanded', 'false');
                });
            }
        });
        */
    });
</script>