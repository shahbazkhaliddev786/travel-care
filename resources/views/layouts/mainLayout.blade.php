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
    <link rel="stylesheet" href="{{ asset('css/partials/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/partials/footer.css') }}">
    @yield('css')
    {{-- <link rel="stylesheet" href="{{ asset('css/category.css') }}">
    <link rel="stylesheet" href="{{ asset('css/public-dlp.css') }}">
    <link rel="stylesheet" href="{{ asset('css/appointment.css') }}">
    <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chats.css') }}"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @yield('head-scripts')
</head>
<body>
    @include('partials.navbar')
    
    <main>
        @yield('content')
    </main>
    
    @include('partials.footer')
    
    <script src="{{ asset('js/partials/navbar.js') }}"></script>
    @yield('script')
</body>
</html>