<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Laravel POS Project Bengkel</title>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    {{--  --}}
    {{-- <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script> --}}
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    @stack('scripts')
</body>

</html>
