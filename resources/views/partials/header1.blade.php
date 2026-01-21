<header class="public-header navbar navbar-expand-lg py-3">
    <div class="container" data-aos="fade-down" data-aos-duration="1000">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/logo-grn.png') }}" alt="ImmuniSprout Logo" height="50" style="margin-top: 15px;">
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="fa-solid fa-bars text-cream"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#why-matters">About</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('public.search-record') }}">Search</a></li>
               <li class="nav-item ms-lg-3">
                    <a href="{{ route('login') }}" class="btn px-4 btn-outline-custom">Login</a>
                </li>
            </ul>
        </div>
    </div>
</header>
