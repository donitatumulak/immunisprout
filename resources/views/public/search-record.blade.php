@extends('layouts.search')

@section('title', 'Search Immunization Record')

@section('content')

<main class="search-records">

    <section class="section-default overlay-orange search-bg-custom text-center min-vh-100 d-flex align-items-center">
        <div class="container" data-aos="zoom-in">
             <h1 class="display-4 fw-bold mb-4 section-heading">Digitized Child Immunization Records</h1>
            <p class="lead mx-auto mb-5" style="max-width: 800px;">Review your child's immunization record, showing vaccines administered and upcoming doses to stay informed about your child's health.</p>
            <a href="#search-form" class="btn btn-custom-green btn-lg shadow-sm">Start Search</a>
        </div>

        <a href="#search-form" class="scroll-down">
            <i class="fa-solid fa-chevron-down bounce"></i>
            <span>Scroll Down</span>
        </a>
    </section>

    <section id="search-form" class="section-default bg-cream">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center mb-5" data-aos="fade-up">
                    <h2 class="section-heading fw-bold">View Your Child's Record</h2>
                    <p>To keep health information safe, please provide the details below to verify the correct record.</p>
                </div>
                
                <div class="col-lg-7" data-aos="fade-up">
                    {{-- Show error message if search failed --}}
                    @if(session('error'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="login-outline-card bg-white mx-auto p-4 p-md-5">
                        <form action="{{ route('public.search-results') }}" method="POST">
                            @csrf
                            
                            {{-- First Name --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold" style="color: var(--green);">
                                    <i class="fas fa-user me-2"></i>First Name *
                                </label>
                                <input type="text" name="chd_first_name" 
                                       class="form-control rounded-pill border-2" 
                                       value="{{ old('chd_first_name') }}" 
                                       placeholder="e.g. Juan" 
                                       required>
                            </div>

                            {{-- Middle Name --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold" style="color: var(--green);">
                                    <i class="fas fa-user me-2"></i>Middle Name
                                </label>
                                <input type="text" name="chd_middle_name" 
                                       class="form-control rounded-pill border-2" 
                                       value="{{ old('chd_middle_name') }}" 
                                       placeholder="e.g. Flores (Optional)">
                                <small class="text-muted">Leave blank if none</small>
                            </div>

                            {{-- Last Name --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold" style="color: var(--green);">
                                    <i class="fas fa-user me-2"></i>Last Name *
                                </label>
                                <input type="text" name="chd_last_name" 
                                       class="form-control rounded-pill border-2" 
                                       value="{{ old('chd_last_name') }}" 
                                       placeholder="e.g. Dela Cruz" 
                                       required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold" style="color: var(--green);">
                                        <i class="fas fa-birthday-cake me-2"></i>Date of Birth *
                                    </label>
                                    <input type="date" name="chd_date_of_birth" 
                                           class="form-control rounded-pill border-2" 
                                           value="{{ old('chd_date_of_birth') }}" 
                                           max="{{ date('Y-m-d') }}"
                                           required>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold" style="color: var(--green);">
                                        <i class="fas fa-barcode me-2"></i>Reference Number *
                                    </label>
                                    <input type="text" name="reference_no" 
                                           class="form-control rounded-pill border-2" 
                                           value="{{ old('reference_no') }}" 
                                           placeholder="IS-2024-0001" 
                                           required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-login w-100 py-3 mt-3">
                                <i class="fas fa-search me-2"></i>SEARCH RECORD
                            </button>
                        </form>

                        {{-- Info Box --}}
                        <div class="alert alert-info mt-4" style="background: #e7f3ff; border-left: 4px solid #0dcaf0;">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-info-circle text-info me-3 mt-1" style="font-size: 1.5rem;"></i>
                                <div>
                                    <strong>Where to find your Reference Number:</strong>
                                    <ul class="mb-0 mt-2 small">
                                        <li>Check your vaccination card or registration receipt</li>
                                        <li>Contact Barangay Health Center if lost</li>
                                        <li>Format: IS-YYYY-#### (e.g., IS-2024-0001)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="section-default bg-cream" style="border-top: 2px solid var(--green);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center mb-5" data-aos="fade-up">
                    <h2 class="section-heading fw-bold">Frequently Asked Questions</h2>
                    <p>Everything you need to know about your child's digital health records.</p>
                </div>
                <div class="col-lg-10">
                    <div class="accordion accordion-flush shadow-sm rounded-4 overflow-hidden" id="faqImmuniSprout">
                        
                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    <i class="fa-solid fa-circle-question me-3 text-orange"></i> Who can view this vaccination record?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqImmuniSprout">
                                <div class="accordion-body text-muted">
                                    Access is restricted to parents or guardians who possess the specific Reference Number provided by the Barangay Health Center. We do not show records to the general public.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    <i class="fa-solid fa-certificate me-3 text-orange"></i> Can I use this for school enrollment?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqImmuniSprout">
                                <div class="accordion-body text-muted">
                                    Most schools accept this digital printout for initial enrollment. However, for travel or legal purposes, you may still need a signed physical card from the Health Officer.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    <i class="fa-solid fa-pen-to-square me-3 text-orange"></i> What if the information is incorrect or missing?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqImmuniSprout">
                                <div class="accordion-body text-muted">
                                    Updates are encoded by our staff. If you notice a discrepancy, please bring your physical vaccination card to the health center for a data correction request.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    <i class="fa-solid fa-shield-halved me-3 text-orange"></i> How is my child's data protected?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqImmuniSprout">
                                <div class="accordion-body text-muted">
                                    We comply with the Data Privacy Act of 2012. All records are stored in an encrypted database accessible only by authorized health personnel.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item border-bottom">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    <i class="fa-solid fa-rotate me-3 text-orange"></i> How often is the record updated?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqImmuniSprout">
                                <div class="accordion-body text-muted">
                                    Records are updated immediately after each vaccination session. Digital synchronization typically occurs within 24 hours of the clinic visit.
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                    <i class="fa-solid fa-key me-3 text-orange"></i> I lost my Reference Number, how do I get it?
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqImmuniSprout">
                                <div class="accordion-body text-muted">
                                    For security reasons, we cannot provide reference numbers online. Please visit the health center with a valid ID to retrieve your child's unique system ID.
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="section-default overlay-orange search-bg-custom">
        <div class="container text-center" data-aos="fade-up">
            <h2 class="display-6 fw-bold mb-4 section-heading">Get In Touch</h2>
            <p class="fs-5 mb-5 mx-auto" style="max-width: 700px;">Still have questions? Our health center staff is ready to assist you with your child's immunization needs.</p>
            
            <div class="row g-4 justify-content-center">
                <div class="col-md-4">
                    <div class="surface-white p-4 h-100 card-hover border-bottom border-orange border-4">
                        <div class="icon-2d bg-light shadow-sm"><i class="fa-solid fa-location-dot"></i></div>
                        <h5 class="fw-bold mt-3">Visit Us</h5>
                        <p class="small text-muted mb-0">Brgy. Pusok Health Center<br>Lapu-Lapu City, Cebu</p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="surface-white p-4 h-100 card-hover border-bottom border-orange border-4">
                        <div class="icon-2d bg-light shadow-sm"><i class="fa-solid fa-phone"></i></div>
                        <h5 class="fw-bold mt-3">Call Us</h5>
                        <p class="small text-muted mb-0">Landline: (032) 123-4567<br>Mobile: +63 912 345 6789</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="surface-white p-4 h-100 card-hover border-bottom border-orange border-4">
                        <div class="icon-2d bg-light shadow-sm"><i class="fa-solid fa-clock"></i></div>
                        <h5 class="fw-bold mt-3">Office Hours</h5>
                        <p class="small text-muted mb-0">Monday - Friday<br>8:00 AM - 5:00 PM</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <button type="button" class="btn btn-back-to-top shadow" id="btn-back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>
</main>
@endsection