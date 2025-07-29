<header class="navbar navbar-expand-md d-flex d-print-none fixed-top"
    style="background-color: rgba(255, 255, 255, 0.7);">
    <div class="container-xl ">
        <div class="flex-row navbar-nav order-md-last w-100 justify-content-between justify-content-md-end">
            <div class="d-md-flex">
                <div class="nav-item dropdown d-flex relative">
                    <a href="#" class="px-0 nav-link" data-bs-toggle="dropdown" tabindex="-1"
                        aria-label="Show notifications" data-bs-auto-close="outside" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-1">
                            <path
                                d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6">
                            </path>
                            <path d="M9 17v1a3 3 0 0 0 6 0v-1"></path>
                        </svg>
                        <span class="badge bg-red"></span>
                    </a>
                    <div
                        class="dropdown-menu dropdown-menu-arrow dropdown-menu-end dropdown-menu-card position-md-absolute mobile-dropup w-full w-md-auto min-w-md-300px end-0 start-auto mb-2 mb-md-0">
                        <div class="card">
                            <div class="card-header d-flex">
                                <h3 class="card-title">Notifications</h3>
                                <div class="btn-close ms-auto" data-bs-dismiss="dropdown"></div>
                            </div>
                            <div class="list-group list-group-flush list-group-hoverable">
                                <div class="list-group-item">
                                    <div class="row align-items-center">
                                        <div class="col-auto"><span
                                                class="status-dot status-dot-animated bg-red d-block"></span></div>
                                        <div class="col text-truncate">
                                            <a href="#" class="text-body d-block">Example 1</a>
                                            <div class="d-block text-secondary text-truncate mt-n1">Change deprecated
                                                html tags to text decoration classes (#29604)</div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="#" class="list-group-item-actions">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon text-muted icon-2">
                                                    <path
                                                        d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="nav-item dropdown relative">
                <a href="#" class="p-0 px-2 nav-link d-flex lh-1" data-bs-toggle="dropdown"
                    aria-label="Open user menu">
                    <span class="avatar avatar-sm" > 
                        <img src="{{ asset('assets/Person.png') }}"  alt="Person">

                    </span>

                    <div class="d-none d-xl-block ps-2">
                        <div>{{ auth()->user() ? auth()->user()->name : '' }}</div>
                        <div class="mt-1 small text-secondary">
                            {{ auth()->user() ? auth()->user()->getRoleNames()->first() : '' }}</div>
                    </div>
                </a>
                <div
                    class="dropdown-menu dropdown-menu-end dropdown-menu-arrow position-md-absolute mobile-dropup w-full w-md-auto min-w-md-200px end-0 start-auto mb-2 mb-md-0">
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">Profile</a>
                    <a href="#" class="dropdown-item">Settings</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                            class="dropdown-item">Logout</a>
                    </form>
                </div>
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
            </div>
    </div>
</header>

<style>
    /* Global/Default Styles */
    body {
        /* Default padding-top for desktop */
        padding-top: 60px; /* Sesuaikan ini dengan tinggi navbar Anda */
    }

    /* Mobile styles */
    @media (max-width: 767.98px) {
        /* Navbar positioning for mobile (fixed-bottom) */
        .fixed-top { /* Override fixed-top for mobile to move it to bottom */
            top: auto !important;
            bottom: 0 !important;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            width: 100%; /* Pastikan lebar penuh */
            z-index: 1030; /* Pastikan di atas konten lain */
        }

        /* Dropdown positioning for mobile (opens upwards) */
        .mobile-dropup {
            position: fixed !important;
            bottom: 60px !important; /* Height of navbar */
            left: 0 !important;
            right: 0 !important;
            top: auto !important;
            width: 100% !important;
            max-height: 60vh;
            overflow-y: auto;
            transform: none !important;
            margin: 0 !important;
        }

        /* Dropdown styling for mobile */
        .dropdown-menu-card {
            border-radius: 0 !important;
            border-top-left-radius: var(--tblr-border-radius-lg) !important;
            border-top-right-radius: var(--tblr-border-radius-lg) !important;
            border-bottom-left-radius: 0 !important;
            border-bottom-right-radius: 0 !important;
        }

        /* Adjust body padding for mobile (to prevent content hiding behind bottom navbar) */
        body {
            padding-top: 0 !important; /* Hapus padding-top di mobile */
            padding-bottom: 60px; /* Add padding-bottom for the fixed-bottom navbar */
        }
    }

    /* Desktop styles */
    @media (min-width: 768px) {
        /* Ensure fixed-top is at the top for desktop */
        .fixed-top {
            top: 0 !important;
            bottom: auto !important;
        }

        /* Dropdown positioning for desktop (opens downwards as usual) */
        .mobile-dropup {
            bottom: auto !important;
            top: 100% !important;
            position: absolute !important; /* Kembali ke perilaku default dropdown */
            width: auto !important; /* Kembali ke lebar default */
        }
    }
</style>