@extends('layouts.internal')

@section('title', 'User Profile')

@section('content')
<style>
    /* user profile styles */
    .profile-header {
        background: white;
        border-radius: 20px;
        border: 2px solid var(--green);
        position: relative;
        overflow: hidden;
        width: 100%;
        max-width: 800px; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .profile-cover {
        height: 160px;
        background: linear-gradient(135deg, #2e8b57 0%, #3ba36b 100%);
        border-bottom: 2px solid var(--green);
        background-size: cover;
        background-position: center;
        border-bottom: 2px solid var(--green);
    }

    .profile-img-wrapper {
        width: 120px;
        height: 120px;
        margin-top: -70px; 
        position: relative;
        display: inline-block;
        margin-left: 40px; 
    }

    .profile-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 6px solid white;
        background: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }

    .btn-edit-avatar {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: var(--green);
        color: white;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid white;
        cursor: pointer;
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #6c757d;
        margin-bottom: 2px;
    }

    .info-value {
        font-weight: 600;
        color: #2d3436;
        margin-bottom: 15px;
    }
</style>

<main  class="profile-page">
<div id="main-content">
    <div class="container">
            <div class="page-header-card d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-4">
            <div class="d-flex align-items-center">
                <div class="header-icon-bg me-3">
                   <i class="fa-solid fa-address-card"></i></i>
                </div>
                <div>
                    <h1 class="mb-1 header-title">User Profile</h1>
                    <p class="text-muted mb-0 header-subtitle">
                        Manage your own profile and account details.
                    </p>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            {{-- Adjust 'col-lg-9' to make the card wider or narrower --}}
            <div class="col-12 col-lg-9">
                <div class="profile-header bg-white shadow-sm mb-4" style="border-radius: 20px; overflow: hidden;">
                    <div class="profile-cover"></div>
                    <div class="px-4 pb-4">
                        <div class="row align-items-end mb-4">
                            <div class="col-md-auto text-center text-md-start">
                                <div class="profile-img-wrapper">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->worker->full_name) }}&background=2e8b57&color=fff&size=128" 
                                    class="profile-img" 
                                    alt="User Initials">
                                </div>
                            </div>
                            
                            <div class="col pt-3">
                                <div class="text-center text-md-start">
                                    <h2 class="fw-bold mb-1 text-dark text-nowrap">
                                        {{ auth()->user()->worker->full_name ?? 'User' }}
                                    </h2>
                                </div>

                                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mt-2">
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success px-3 py-2">
                                        {{ ucfirst(auth()->user()->worker->wrk_role ?? 'Worker') }}
                                    </span>

                                    <div class="ms-md-auto d-flex gap-2 mt-2 mt-md-0">
                                        <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                            <i class="fa-solid fa-user-pen me-1"></i> EDIT PROFILE
                                        </button>
                                        <button class="btn btn-outline-dark rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                            <i class="fa-solid fa-lock me-1"></i> PASSWORD
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="text-muted opacity-25">

                        <div class="row pt-2 px-2">
                            <div class="col-md-4">
                                <p class="info-label mb-1 text-muted small fw-bold">Full Name</p>
                                <p class="info-value fw-semibold text-dark">{{ auth()->user()->worker->full_name ?? 'User' }}</p>
                                
                                <p class="info-label mb-1 text-muted small fw-bold">Username</p>
                                <p class="info-value"><code>{{ auth()->user()->username ?? 'N/A'}}</code></p>
                            </div>
                            <div class="col-md-4">
                                <p class="info-label mb-1 text-muted small fw-bold">Contact Number</p>
                                <p class="info-value">
                                    <a href="tel:{{ auth()->user()->worker->wrk_contact_number }}" class="text-decoration-none text-dark fw-semibold">
                                        {{ auth()->user()->worker->wrk_contact_number ?? 'N/A' }}
                                    </a>
                                </p>

                                <p class="info-label mb-1 text-muted small fw-bold">Address</p>
                                <p class="info-value fw-semibold">{{ auth()->user()->worker->address->full_address ?? 'N/A'}}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="info-label mb-1 text-muted small fw-bold">Assigned Center</p>
                                <p class="info-value fw-semibold">Brgy. {{ auth()->user()->worker->address->addr_barangay ?? 'N/A'}} Health Center</p>

                                <p class="info-label mb-1 text-muted small fw-bold">Account Created</p>
                                <p class="info-value fw-semibold">{{ auth()->user()->worker->created_at ? auth()->user()->worker->created_at->format('M d, Y') : 'N/A'}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-success text-white py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-id-card me-2"></i>Edit Profile Information</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editProfileForm" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <p class="text-success fw-bold border-bottom pb-1 mb-3">Personal Details</p>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">First Name</label>
                            <input type="text" name="wrk_first_name" class="form-control border-2 rounded-pill" value="{{ auth()->user()->worker->wrk_first_name }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Middle Name</label>
                            <input type="text" name="wrk_middle_name" class="form-control border-2 rounded-pill" value="{{ auth()->user()->worker->wrk_middle_name }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Last Name</label>
                            <input type="text" name="wrk_last_name" class="form-control border-2 rounded-pill" value="{{ auth()->user()->worker->wrk_last_name }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control border-2 rounded-pill" value="{{ auth()->user()->username }}">
                            @error('username') <div class="text-danger extra-small ms-2">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Contact Number</label>
                            <input type="text" name="wrk_contact_number" class="form-control border-2 rounded-pill" value="{{ auth()->user()->worker->wrk_contact_number }}">
                        </div>
                    </div>

                    <p class="text-success fw-bold border-bottom pb-1 mt-3 mb-3">Address Information</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Street Address</label>
                            <input type="text" name="addr_line_1" class="form-control border-2 rounded-pill" value="{{ auth()->user()->worker->address->addr_line_1 }}">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Bldg / Village / Landmark (Optional)</label>
                            <input type="text" name="addr_line_2" class="form-control border-2 rounded-pill" value="{{ auth()->user()->worker->address->addr_line_2 }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">Barangay</label>
                            <input type="text" name="addr_barangay" class="form-control border-2 rounded-pill" value="{{ auth()->user()->worker->address->addr_barangay }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small fw-bold">City/Municipality</label>
                            <input type="text" name="addr_city_municipality" class="form-control border-2 rounded-pill" value="{{ auth()->user()->worker->address->addr_city_municipality }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label small fw-bold">Province</label>
                            <input type="text" name="addr_province" class="form-control border-2 rounded-pill" value="{{ auth()->user()->worker->address->addr_province }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Zip Code</label>
                            <input type="text" name="addr_zip_code" class="form-control border-2 rounded-pill" value="{{ auth()->user()->worker->address->addr_zip_code }}">
                        </div>
                    </div>

                    <input type="hidden" name="wrk_role" value="{{ auth()->user()->worker->wrk_role }}">

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm">SAVE CHANGES</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-dark text-white py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold small"><i class="fa-solid fa-shield-halved me-2"></i>Change Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">CURRENT PASSWORD</label>
                        <input type="password" 
                               name="current_password" 
                               class="form-control border-2 rounded-pill @error('current_password') is-invalid @enderror" 
                               placeholder="••••••••"
                               required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NEW PASSWORD</label>
                        <input type="password" 
                               name="password" 
                               class="form-control border-2 rounded-pill @error('password') is-invalid @enderror" 
                               placeholder="Min. 8 characters"
                               required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">CONFIRM NEW PASSWORD</label>
                        <input type="password" 
                               name="password_confirmation" 
                               class="form-control border-2 rounded-pill @error('password_confirmation') is-invalid @enderror" 
                               placeholder="Repeat password"
                               required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <button type="submit" class="btn btn-dark w-100 py-2 rounded-pill fw-bold">UPDATE PASSWORD</button>
                </form>
            </div>
        </div>
    </div>
</div>
</main>

<script>
    // If there are validation errors, automatically re-open the modal
    /*@if($errors->any())*/
        document.addEventListener('DOMContentLoaded', function () {
            var myModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
            myModal.show();
        });
    /*@endif*/
</script>

@endsection