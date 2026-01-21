@extends('layouts.app')

@section('title', 'Home')

@section('content')

<main class="home-page">

    <section id="home" class="hero-carousel overlay-orange">
        <div id="heroSlider" class="carousel slide carousel-fade" data-bs-ride="false" data-bs-interval="false">
            <div class="carousel-inner">
                
                <div class="carousel-item active">
                    <div class="hero-content-wrapper container text-center">
                        <div class="reveal-text">
                            <h1 class="display-3 fw-bold">Nurturing Immunity from the First Sprout</h1>
                            <p class="lead mx-auto mb-4">A digital immunization system designed to help barangay health workers protect every child — on time, every time.</p>
                            <a href="#how-it-helps" class="btn btn-custom-green btn-lg shadow-sm">Explore the System</a>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="hero-content-wrapper container text-center">
                        <h1 class="display-3 fw-bold">Good Records Save Lives</h1>
                        <p class="lead mx-auto mb-4">Replace lost paper cards with reliable, searchable, and printable digital immunization records.</p>
                        <a href="#why-matters" class="btn btn-custom-green btn-lg shadow-sm">Why It Matters</a>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="hero-content-wrapper container text-center">
                        <h1 class="display-3 fw-bold">Built for Barangays</h1>
                        <p class="lead mx-auto mb-4">Designed for real community health work — simple, compliant, and accessible on any device.</p>
                        <a href="{{ route('login') }}" class="btn btn-custom-green btn-lg shadow-sm">Access the Platform</a>
                    </div>
                </div>
            </div>
            
            <button class="carousel-control-prev custom-nav" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next custom-nav" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
        
        <a href="#why-matters" class="scroll-down">
            <i class="fa-solid fa-chevron-down bounce"></i>
            <span>Scroll Down</span>
        </a>
    </section>
    
    <section id="why-matters" class="section-default bg-cream">
        <div class="container" data-aos="fade-up">
            <div class="text-center mb-5">
                <h2 class="display-6 fw-bold section-heading">Why This Matters</h2>
                <p class="fs-5 mx-auto">Immunization only works when records are accurate, complete, and accessible.</p>
            </div>

            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right" data-aos-delay="200">
                    <ul class="custom-check-list">
                        <li><i class="fa-solid fa-circle-exclamation"></i> Paper records are easily lost</li>
                        <li><i class="fa-solid fa-clock"></i> Missed vaccine schedules</li>
                        <li><i class="fa-solid fa-user-nurse"></i> Health workers overloaded with paperwork</li>
                        <li><i class="fa-solid fa-file-invoice"></i> Parents lack reliable copies</li>
                    </ul>
                </div>
                <div class="col-lg-6" data-aos="fade-left" data-aos-delay="400">
                    <div class="surface-white p-5 shadow-sm border-start border-5 border-orange">
                        <h5 class="fw-bold">A Practical Digital Solution</h5>
                        <p>This system organizes immunization data securely, supports follow-ups, and helps prevent vaccine-preventable diseases.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="how-it-helps" class="section-default overlay-orange">
        <div class="container text-center" data-aos="zoom-in">
            <h2 class="display-6 fw-bold mb-5 section-heading">System Features</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="surface-white p-4 h-100 shadow card-hover">
                        <div class="icon-2d"><i class="fa-solid fa-database"></i></div>
                        <h5 class="fw-bold">Centralized Records</h5>
                        <p>All immunization data stored securely in one place.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="surface-white p-4 h-100 shadow card-hover">
                        <div class="icon-2d"><i class="fa-solid fa-bell"></i></div>
                        <h5 class="fw-bold">Automated Reminders</h5>
                        <p>Timely alerts for upcoming and missed vaccinations.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                    <div class="surface-white p-4 h-100 shadow card-hover">
                        <div class="icon-2d"><i class="fa-solid fa-chart-line"></i></div>
                        <h5 class="fw-bold">Reports & Monitoring</h5>
                        <p>Track immunization coverage and performance.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                    <div class="surface-white p-4 h-100 shadow card-hover">
                        <div class="icon-2d"><i class="fa-solid fa-mobile-screen"></i></div>
                        <h5 class="fw-bold">Digital Copy</h5>
                        <p>Instant access to digital immunization records for guardians.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-default bg-cream">
        <div class="container text-center" data-aos="fade-up">
            <h2 class="display-6 fw-bold mb-5 section-heading">Who It Is For</h2>
            <div class="row g-4">
                <div class="col-lg-4" data-aos="flip-left" data-aos-delay="100">
                    <div class="surface-white p-4 h-100 shadow card-hover">
                        <div class="icon-2d"><i class="fa-solid fa-person-breastfeeding"></i></div>
                        <h5 class="fw-bold">Parents & Guardians</h5>
                        <p>Access and verify your child’s vaccination history.</p>
                        <div class="mt-auto pt-4">
                          <a href="login.php" class="btn w-100 rounded-pill shadow-sm py-2 fw-bold" 
                            style="background-color: var(--green); color: var(--cream);">
                            Search Child Records
                          </a>
                      </div>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="flip-left" data-aos-delay="200">
                    <div class="surface-white p-4 h-100 shadow card-hover">
                        <div class="icon-2d"><i class="fa-solid fa-user-nurse"></i></div>
                        <h5 class="fw-bold">Health Workers</h5>
                        <p>Streamline tracking, monitoring, and reporting for a better health center.</p>
                        <div class="mt-auto pt-4">
                          <a href="login.php" class="btn w-100 rounded-pill shadow-sm py-2 fw-bold" 
                            style="background-color: var(--green); color: var(--cream);">
                            Authorized Access
                          </a>
                      </div>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="flip-left" data-aos-delay="300">
                    <div class="surface-white p-4 h-100 shadow card-hover">
                        <div class="icon-2d"><i class="fa-solid fa-earth-asia"></i></div>
                        <h5 class="fw-bold">Communities</h5>
                        <p>Improve immunization coverage and public health.</p>
                        <div class="mt-auto pt-4">
                          <a href="login.php" class="btn w-100 rounded-pill shadow-sm py-2 fw-bold" 
                            style="background-color: var(--green); color: var(--cream);">
                            Got Any Questions?
                          </a>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-default overlay-orange">
        <div class="container text-center" data-aos="fade-up">
            <h2 class="display-6 fw-bold mb-4 section-heading">Supporting the Immunization Law</h2>
            <div class="surface-white p-5 mx-auto shadow-lg" style="max-width:900px; border-radius: 20px;">
                <div class="mb-4 text-orange" style="font-size: 3rem;">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <h4 class="fw-bold mb-3">Republic Act No. 10152</h4>
                <p class="fs-5 mb-0">
                    This system supports the <strong>Mandatory Infants and Children Health Immunization Act</strong> by promoting complete and timely immunization through accurate, modern, and accessible digital record-keeping.
                </p>
                <a href="https://elibrary.judiciary.gov.ph/thebookshelf/showdocs/2/37483" class="text-decoration-none fw-bold mt-auto pt-3 d-inline-block" style="color: var(--orange);">
                    <i class="fa-solid fa-gavel"></i> Learn More About the Law <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="section-default bg-cream">
        <div class="container text-center" data-aos="fade-up">
            <h2 class="display-6 fw-bold mb-5 section-heading">Contributing to Global Goals</h2>
            <div class="row g-4">
                <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="surface-white p-4 h-100 shadow card-hover">
                        <img src="{{ asset('images/sdg03.png') }}" alt="SDG 3" height="100" class="mb-3">
                        <h5 class="fw-bold">SDG 3</h5>
                        <p style="font-size: 1.12rem;"><strong>Good Health:</strong> Ensuring healthy lives and promoting well-being for all children at all ages.</p>
                        <a href="https://sdgs.un.org/goals/goal3" class="text-decoration-none fw-bold mt-auto pt-3 d-inline-block" style="color: var(--orange);">
                            <i class="fa-solid fa-globe"></i> Learn More <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="surface-white p-4 h-100 shadow card-hover">
                        <img src="{{ asset('images/sdg10.png') }}" alt="SDG 10" height="100" class="mb-3">
                        <h5 class="fw-bold">SDG 10</h5>
                        <p style="font-size: 1.12rem;"><strong>Reduced Inequalities:</strong> Bridging the gap in health service access for every barangay in the city.</p>
                        <a href="https://sdgs.un.org/goals/goal10" class="text-decoration-none fw-bold mt-auto pt-3 d-inline-block" style="color: var(--orange);">
                            <i class="fa-solid fa-globe"></i> Learn More <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="surface-white p-4 h-100 shadow card-hover">
                        <img src="{{ asset('images/sdg16.png') }}" alt="SDG 16" height="100" class="mb-3">
                        <h5 class="fw-bold">SDG 16</h5>
                        <p style="font-size: 1.12rem;"><strong>Strong Institutions:</strong> Building effective, accountable health systems at the community level.</p>
                        <a href="https://sdgs.un.org/goals/goal16" class="text-decoration-none fw-bold mt-auto pt-3 d-inline-block" style="color: var(--orange);">
                            <i class="fa-solid fa-globe"></i> Learn More <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-default overlay-orange">
        <div class="container" data-aos="fade-right">
            <h2 class="display-6 fw-bold mb-4 section-heading">About This Project</h2>
            <p class="fs-5 mb-4">
                This digital immunization tracking system was developed as a <strong>BS Information Systems Web Application project</strong>, focusing on creating simple, thoughtful technology to support barangay health services.
            </p>
            <div class="p-4 border-start border-5 border-cream bg-white-transparent" data-aos="fade-left" data-aos-delay="300">
                 <p class="fst-italic fs-5 mb-0 text-cream">"Life's most persistent and urgent question is: What are you doing for others?"</p>
                 <small>— Martin Luther King, Jr.</small>
            </div>
        </div>
    </section>

    <button type="button" class="btn btn-back-to-top shadow" id="btn-back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>

</main>

@endsection
