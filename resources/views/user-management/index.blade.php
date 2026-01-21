@extends('layouts.internal')

@section('title', 'User Management')

@section('content')

<style>
    .admin-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        border: 2px solid var(--green);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .table-custom thead {
        background-color: var(--green) !important;
        color: white !important;
    }
    
    .table-custom thead th {
        border: none;
        padding: 10px;
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--green);
        background-color: var(--cream);
    }

    /* Role Badge Colors */
    .role-badge-nurse { background-color: #e3f2fd; color: #1565c0; border-color: #1565c0; }
    .role-badge-midwife { background-color: #f3e5f5; color: #6a1b9a; border-color: #6a1b9a; }
    .role-badge-admin { background-color: #fff3e0; color: #e65100; border-color: #e65100; }
    .role-badge-bhw { background-color: #e8f5e9; color: #2e7d32; border-color: #2e7d32; }
</style>

<div id="main-content" class="px-4">
    {{-- Header --}}
    <div class="page-header-card d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center">
            <div class="header-icon-bg me-3">
               <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <h1 class="mb-1 header-title">User Management</h1>
                <p class="text-muted mb-0 header-subtitle">Manage personnel profiles and system access.</p>
            </div>
        </div>
        <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createPersonnelModal">
            <i class="fa-solid fa-user-plus me-2"></i>Add New Personnel
        </button>
    </div>

   {{-- User Management Search & Filter --}}
    <form action="{{ route('user-management.index') }}" method="GET" class="mb-4">
        <div class="filter-card bg-white rounded-4 shadow-sm border p-3">
            <div class="row g-2 align-items-center">
                
                {{-- Search Bar: Changed to col-md-4 to save space --}}
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Search User</label>
                    <div class="search-wrapper position-relative">
                        <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input shadow-sm ps-5" placeholder="Name or username..." style="border-radius: 50px;">
                    </div>
                </div>

                {{-- Role Filter: Changed to col-md-3 --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Role</label>
                    <select name="role" class="form-select rounded-pill" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter: Added new col-md-3 --}}
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Status</label>
                    <select name="status" class="form-select rounded-pill" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Reset Button: Changed to col-md-2 --}}
                <div class="col-md-2 d-flex justify-content-end align-items-end">
                     <div class="col-md-3">
                            <label class="form-label d-block">&nbsp;</label>
                            <a href="{{ route('user-management.index') }}" class="btn btn-outline-secondary rounded-pill" title="Reset Filters">
                                <i class="fa-solid fa-rotate-right"></i>
                            </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-4 shadow-sm border overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-custom">
                <thead>
                    <tr>
                        <th class="ps-4">FULL NAME</th>
                        <th>USERNAME</th>
                        <th>ROLE</th>
                        <th>CONTACT</th>
                        <th>STATUS</th>
                        <th>TOGGLE</th>
                        <th class="text-center pe-4">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->worker->wrk_first_name . ' ' . $user->worker->wrk_last_name) }}&background=E8F5E9&color=2e8b57" 
                                        class="user-avatar me-3 shadow-sm" alt="Avatar">
                                    <div>
                                        <div class="fw-bold text-dark lh-1 mb-1">{{ $user->worker->wrk_first_name }} {{ $user->worker->wrk_last_name }}</div>
                                        <small class="text-muted"><i class="fa-solid fa-location-dot me-1"></i>Brgy. {{ $user->worker->address->addr_barangay ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><code class="text-primary">{{ $user->username }}</code></td>
                            <td><span class="badge rounded-pill border role-badge-{{ strtolower($user->worker->wrk_role) }}">{{ ucfirst($user->worker->wrk_role) }}</span></td>
                            <td>{{ $user->worker->wrk_contact_number }}</td>
                            <td>
                                @if($user->user_status == 'active')
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3">
                                        <i class="fa-solid fa-circle-check me-1"></i> Active
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Inactive
                                    </span>
                                @endif
                            </td>

                            <td>
                                <form action="{{ route('users.toggle', $user->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                                            onclick="return confirm('Are you sure you want to change this user\'s status?');">
                                        @if($user->user_status == 'active')
                                            <i class="fa-solid fa-user-slash me-1"></i> Deactivate
                                        @else
                                            <i class="fa-solid fa-user-check me-1"></i> Activate
                                        @endif
                                    </button>
                                </form>
                            </td>
                            
                            <td class="text-center pe-4"> 
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    {{-- Actions using Data Attributes to avoid Editor Errors --}}
                                    <button type="button" 
                                        class="btn btn-sm btn-outline-info rounded-circle btn-view" 
                                        data-worker="{{ json_encode($user->worker) }}"
                                        data-address="{{ json_encode($user->worker->address) }}"
                                        data-username="{{ $user->username }}"
                                        title="View Profile">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>

                                    <button type="button" 
                                        class="btn btn-sm btn-outline-success rounded-circle btn-edit" 
                                        data-user="{{ json_encode($user) }}"
                                        data-worker="{{ json_encode($user->worker) }}"
                                        data-address="{{ json_encode($user->worker->address) }}"
                                        title="Edit Profile">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <button type="button" 
                                        class="btn btn-sm btn-outline-warning rounded-circle btn-password" 
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->worker->wrk_first_name }}"
                                        data-username="{{ $user->username }}"
                                        title="Change Password">
                                        <i class="fa-solid fa-key"></i>
                                    </button>

                                    <button type="button" 
                                        class="btn btn-sm btn-outline-danger rounded-circle btn-delete" 
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->worker->wrk_first_name }} {{ $user->worker->wrk_last_name }}"
                                        title="Delete User">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">No users found matching your criteria.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

       {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $users->links('partials.pagination') }}
        </div>
</div>

@include('user-management.create')
@include('user-management.edit')
@include('user-management.delete')
@include('user-management.view')    
@include('user-management.password') 

<script>
    // Event Listeners for better Modal handling
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function() {
            const worker = JSON.parse(this.dataset.worker);
            const address = JSON.parse(this.dataset.address);
            const username = this.dataset.username;
            openViewModal(worker, address, username);
        });
    });

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const user = JSON.parse(this.dataset.user);
            const worker = JSON.parse(this.dataset.worker);
            const address = JSON.parse(this.dataset.address);
            openEditModal(user, worker, address);
        });
    });

    document.querySelectorAll('.btn-password').forEach(btn => {
        btn.addEventListener('click', function() {
           openPasswordModal(this.dataset.id, this.dataset.name, this.dataset.username);
        });
    });

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            openDeleteModal(this.dataset.id, this.dataset.name);
        });
    });
</script>
@endsection