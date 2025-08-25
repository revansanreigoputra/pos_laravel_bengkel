<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $nama_bengkel ?? 'Project Bengkel' }}</title>
    <link rel="icon" href="{{ $logo_path }}" type="image/x-icon">
    @include('includes.style')
    @stack('addon-style')
    {{-- select2 --}}
    @yield('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>

<body>
    <div class="page">
        @include('partials.sidebar')
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
  @stack('scripts')
</body>

</html>
