<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/logo.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    
    <link rel="stylesheet" href="{{ asset('css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('css/getstarted.css') }}">

</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <a href="/">
            <img src="{{ asset('assets/logo-with-txt.svg') }}" alt="TravelCare Logo" class="nav-logo">
        </a>
        <div class="nav-links">
            <a href="{{ route('get-started') }}">Sign Up</a>
            <a href="{{ route('signin') }}">Sign In</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/global.js') }}"></script>
    @yield('scripts')
</body>
</html>