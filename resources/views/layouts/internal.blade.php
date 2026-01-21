<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ImmuniSprout')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/faviconx.png') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    
    @stack('styles')
</head>
<body>
    <div id="sidebar-overlay"></div>

    <div class="d-flex">
        @include('partials.sidebar')
        
        <div id="main-wrapper">
            <header class="d-lg-none p-3 bg-white border-bottom d-flex align-items-center d-print-none">
                    <button class="mobile-nav-toggle btn" id="mobile-hamburger">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <span class="ms-auto fw-bold text-success">ImmuniSprout</span>
            </header>

                
            <div id="main-content">
                @include('partials.messages')
                @yield('content')
            </div>

            <footer>
                @include('partials.footer')
            </footer>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
</body>
</html>
