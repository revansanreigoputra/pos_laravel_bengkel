<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $nama_bengkel ?? 'Project Bengkel' }}</title>
    <link rel="icon" href="{{ $logo_path }}" type="image/x-icon">
    @include('includes.style')

</head>

<body>
    @yield('content')
    @include('includes.script')
</body>

</html>
