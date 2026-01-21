@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<header class="login-nav">
    <div class="container-fluid px-lg-5 py-3">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/logo-grn.png') }}" alt="ImmuniSprout Logo" height="50" style="margin-top: 15px;">
        </a>
    </div>
</header>

<section class="login-split-screen">
    <div class="container-fluid p-0">
        <div class="row g-0">
           <div class="col-lg-6 d-none d-lg-flex left-panel overlay-orange login-bg-custom align-items-center justify-content-end">
                    <div class="panel-content pe-5 text-end" data-aos="fade-right">
                        <div class="mt-4 pt-4 border-top border-green border-2 w-50 ms-auto">
                            <p class="mb-0 fw-bold" style="color: var(--green);">
                                <i class="fa-regular fa-calendar-check me-2"></i>
                                <?php echo date('F d, Y'); ?>
                            </p>
                        </div>
                        <h1 class="display-4 fw-bold" style="color: var(--green);">Barangay Health Center Login</h1>
                        <p class="fs-4" style="color: var(--cream); max-width: 500px; margin-left: auto;">
                            Access the system to manage and monitor child immunization records.
                        </p>
                        
                        <div class="mt-4 pt-4 border-top border-green border-2 w-50 ms-auto"></div>
                        <ul class="list-unstyled mt-3" style="color: var(--cream);">
                            <li class="mb-2">Digitalized Immunization Records <i class="fa-solid fa-check-double ms-2"></i></li>
                            <li class="mb-2">Vaccine Stock Monitoring <i class="fa-solid fa-check-double ms-2"></i></li>
                            <li>Community Immunity Analytics <i class="fa-solid fa-check-double ms-2"></i></li>
                        </ul>
                    </div>
            </div>

            <div class="col-lg-6 right-panel bg-cream d-flex align-items-center justify-content-center">
                <div class="login-outline-card p-4 p-md-5" data-aos="fade-left">
                    <h2 class="fw-bold mb-4" style="color: var(--green); text-align: center; font-size: 2.6rem">Welcome Back!</h2>
                    <p class="mb-4 text-muted">Please enter your credentials to access the system.</p>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @error('login_error')
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i>
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @enderror

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                           <label class="form-label fw-bold" style="color: var(--green);">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="color: var(--orange);">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input type="text" name="username" id="username" class="form-control border-start-0 shadow-none" placeholder="Enter username" required>
                            </div>
                            <small id="username-feedback" class="d-block mt-1"></small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--green);">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="color: var(--orange);">
                                    <i class="fa-solid fa-lock"></i>
                                </span>
                                <input type="password" name="password" id="password" class="form-control border-start-0 shadow-none" placeholder="Enter password" required>
                                <span class="input-group-text bg-white border-start-0" style="cursor:pointer;" id="toggle-password">
                                    <i class="fa-solid fa-eye" id="eye-icon"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input shadow-none" type="checkbox" name="remember">
                                <label class="form-check-label small" for="rememberMe" style="color: var(--green);">
                                    Remember me
                                </label>
                            </div>
                        </div>

                        <!-- ERROR MESSAGE -->
                        @error('login')
                            <div class="alert alert-danger text-center py-2">
                                {{ $message }}
                            </div>
                        @enderror

                        <button type="submit" class="btn btn-login w-100 shadow-sm py-2 mb-4">LOGIN</button>

                        <div class="help-section text-center pt-3 border-top">
                            <p class="small text-muted mb-0">
                                For help, contact your barangay health center system administrator.
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('toggle-password').addEventListener('click', function() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
});

document.querySelector('input[name="username"]').addEventListener('input', function() {
    let username = this.value;
    let msg = document.getElementById('username-feedback'); // Target the fixed ID
    
    if(username.length < 3) {
        msg.textContent = ""; // Clear message if too short
        return;
    }

    fetch(`/check-username?username=${username}`)
        .then(res => res.json())
        .then(data => {
            msg.textContent = data.exists ? 'User found' : 'Username not found';
            msg.style.color = data.exists ? 'green' : 'red';
        });
});

</script>

@endsection