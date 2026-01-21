 <header class="public-header navbar navbar-expand-lg py-3">
        <div class="container" data-aos="fade-down">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('images/logo-grn.png') }}" alt="ImmuniSprout Logo"  height="50" style="margin-top: 15px;">
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                <span class="fa-solid fa-bars" style="color: var(--green);"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" style="color: var(--cream) !important;" href="{{ route('home') }}">Learn More</a></li>
                    <li class="nav-item"><a class="nav-link" style="color: var(--cream) !important;" href="#faq">Questions</a></li>
                    <li class="nav-item"><a class="nav-link" style="color: var(--cream) !important;" href="#contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </header>