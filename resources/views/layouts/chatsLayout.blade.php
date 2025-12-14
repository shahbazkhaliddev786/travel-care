<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/logo.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/partials/navbar.css') }}">
    @yield('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    @include('partials.navbar')
    
    <main>
        @yield('content')
    </main>
    
    <script src="{{ asset('js/partials/navbar.js') }}"></script>
    @yield('script')
</body>
</html>