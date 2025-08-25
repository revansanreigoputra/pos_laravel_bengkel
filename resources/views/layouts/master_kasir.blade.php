<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $nama_bengkel ?? 'Project Bengkel' }}</title>
    <link rel="icon" href="{{ $logo_path }}" type="image/x-icon">
    @include('includes.style')
    @stack('addon-style')

</head>

<body>
    <div class="page">
        @include('partials.sidebar_kasir')
        @include('partials.navbar')
        <div class="page-wrapper">
            @include('partials.header')
            <div class="page-body">
                <div class="container-xl">
                    @include('partials.alert')
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    @include('includes.script')
    @stack('addon-script')
</body>

</html>
