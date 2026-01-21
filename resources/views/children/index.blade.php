@extends('layouts.internal')

@section('title', 'Child Record Management')

@section('content')
<style>
    .child-card {
        background: var(--white);
        border-radius: 20px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
    }

    .child-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .card-top {
        background: var(--green);
        color: white;
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        padding: 20px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .avatar-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--light-green);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
         color: var(--green);
    }

   .status-badge {
        font-size: 0.75rem;
        padding: 5px 14px; 
        border-radius: 50px;
        font-weight: 600;
        background-color: rgba(0, 255, 136, 0.15); 
        color: white; 
        border: 1px solid rgba(25, 135, 84, 0.2);
        display: inline-block;
        white-space: nowrap;
    }

    .card-body-details {
        padding: 20px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .detail-label {
        color: #6b7280;
        font-weight: 500;
    }

    .detail-value {
        font-weight: 600;
        color: #1f2937;
        text-align: right;
    }

    .btn-view-records {
        background: var(--green);
        color: white;
        width: 100%;
        border-radius: 12px;
        padding: 10px;
        font-weight: 600;
        margin-top: 15px;
        border: none;
        transition: filter 0.2s;
    }

    .btn-view-records:hover {
        filter: brightness(90%);
        color: var(--green);
    }

    .card-actions {
        position: absolute;
        top: 15px;
        right: 15px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .child-card:hover .card-actions { opacity: 1; }

    .btn-action {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        color: var(--text-muted);
        border: 1px solid #eee;
    }
    .btn-action:hover { color: var(--green); transform: scale(1.1); }

    .avatar-female { color: #e91e63 !important; background: #fce4ec !important; }
    .avatar-male   { color: #0284c7 !important; background: #e0f2fe !important; }
    .avatar-default { color: #64748b !important; background: #f1f5f9 !important; }
</style>

<div id="main-content" class="px-4">
     <div class="page-header-card d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center">
                <div class="header-icon-bg me-3">
                    <i class="fa-solid fa-users-viewfinder"></i>
                </div>
                <div>
                    <h1 class="mb-1 header-title">Child Record Management</h1>
                    <p class="text-muted mb-0 header-subtitle">
                        Manage and track immunization for all registered children.
                    </p>
                </div>
            </div>
            <div class="header-actions d-flex align-items-center gap-3">
                <!-- Register Button (hide when viewing trash) -->
                @if(request('status') != 'trashed')
                    <button class="btn btn-success rounded-pill px-4 fw-bold" 
                            style="background: var(--green);"
                            data-bs-toggle="modal" 
                            data-bs-target="#registerChildModal"> 
                        <i class="fa-solid fa-plus me-2"></i>Register New Child
                    </button>
                @endif

                <!-- Toggle between Active and Trash view -->
                <div class="d-flex gap-2">
                    @if(auth()->user()->canDelete())
                        @if(request('status') == 'trashed')
                            <a href="{{ route('children.index') }}" class="btn btn-primary rounded-pill shadow-sm px-4">
                                <i class="fa-solid fa-arrow-left me-1"></i> Back to Active Records
                            </a>
                        @else
                            <a href="{{ route('children.index', ['status' => 'trashed']) }}" class="btn btn-outline-danger rounded-pill shadow-sm px-4">
                                <i class="fa-solid fa-trash-can me-1"></i> View Trash
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        <form action="{{ route('children.index') }}" method="GET" class="mb-4">
            <div class="filter-card bg-white rounded-4 shadow-sm border p-3">
                <div class="row g-2 align-items-center">
                    
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Search Child</label>
                        <div class="search-wrapper position-relative">
                            <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input shadow-sm ps-5" placeholder="Name or username..." style="border-radius: 50px;">
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Sex</label>
                        <select name="sex" class="form-select rounded-pill" onchange="this.form.submit()">
                            <option value="">All Sex</option>
                            <option value="male" {{ request('sex') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('sex') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Residency</label>
                        <select name="residency" class="form-select rounded-pill" onchange="this.form.submit()">
                            <option value="">All Residency</option>
                            <option value="established resident" {{ request('residency') == 'established resident' ? 'selected' : '' }}>Established</option>
                            <option value="transitional resident" {{ request('residency') == 'transitional resident' ? 'selected' : '' }}>Transitional</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" class="form-select rounded-pill" onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                   <div class="col-md-2 d-flex justify-content-end align-items-end gap-3 pb-1">
                        <span class="text-muted fw-bold" style="font-size: 0.8rem; white-space: nowrap;">
                            <i class="fa-solid fa-database me-1"></i> 
                            {{ $children->count() }} of {{ $children->total() }} Records
                        </span>

                        <div class="col-md-3">
                            <label class="form-label d-block">&nbsp;</label>
                            <a href="{{ route('children.index') }}" class="btn btn-outline-secondary rounded-pill">
                                <i class="fa-solid fa-rotate-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    <div class="row g-4">
        @forelse($children as $child)
        <div class="col-xl-4 col-md-6">
            <div class="child-card shadow-sm">
                <div class="card-actions">
                     @if(request('status') == 'trashed')
                    <!-- Restore Button -->
                     @if(auth()->user()->canDelete())
                    <form action="{{ route('children.restore', $child->chd_id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn-action text-success" title="Restore Record"
                                onclick="return confirm('Restore this child record?')">
                            <i class="fa-solid fa-rotate-left"></i>
                        </button>
                    </form>

                    <!-- Permanent Delete Button -->
                    <form action="{{ route('children.forceDelete', $child->chd_id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action text-danger" title="Delete Permanently"
                                onclick="return confirm('PERMANENTLY delete this record? This CANNOT be undone!')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                    @endif
                @else
                    <!-- Edit Button -->
                    <button class="btn-action me-1" title="Edit Record" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editChildModal{{ $child->chd_id }}">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>

                    <!-- Soft Delete Button -->
                     @if(auth()->user()->canDelete())
                    <button class="btn-action text-danger" title="Move to Trash" 
                            data-bs-toggle="modal"
                            data-bs-target="#deleteChildModal{{ $child->chd_id }}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                    @endif
                @endif
                </div>

                <div class="card-top">
                            @php
                                $genderClass = match(strtolower($child->chd_sex)) {
                                    'female' => 'avatar-female',
                                    'male'   => 'avatar-male',
                                    default  => 'avatar-default',
                                };
                            @endphp

                            <div class="avatar-circle {{ $genderClass }}">
                                <i class="fa-solid fa-baby" style="font-size: 24px;"></i>
                            </div>

                    <div class="flex-grow-1">
                       <h5 class="mb-2 fw-bold text-wrap text-break" style="max-width: 100%; line-height: 1.2;">
                            {{ $child->full_name ?? 'N/A' }}
                        </h5>
                       <span class="status-badge badge-{{ $child->status_details['color'] }}">
                            <i class="fa-solid {{ $child->status_details['icon'] }} me-1"></i>
                            {{ ucfirst($child->chd_status) }}
                        </span>
                    </div>
                </div>

                <div class="card-body-details">
                    <div class="detail-item">
                        <span class="detail-label">Sex:</span>
                        <span class="detail-value {{ $child->chd_sex == 'male' ? 'text-primary' : 'text-danger' }}">{{ ucfirst($child->chd_sex) }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Birthday:</span>
                        <span class="detail-value">{{ \Carbon\Carbon::parse($child->chd_date_of_birth)->format('M d, Y') ?? 'N/A'}}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Guardian:</span>
                        <span class="detail-value text-wrap text-break">
                            {{ $child->guardian->full_name ?? 'N/A' }} 
                            <span class="text-muted small">({{ ucfirst($child->guardian->grd_relationship) ?? 'N/A' }})</span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Contact:</span>
                        <span class="detail-value">{{ $child->guardian->grd_contact_number ?? 'N/A'}}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value text-end">
                            @if($child->address)
                                <div>{{ $child->address->formatted_lines['line1'] }}</div>
                                <div>{{ $child->address->formatted_lines['line2'] }}</div>
                                <div>{{ $child->address->formatted_lines['line3'] }}</div>
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Residency:</span>
                        <span class="detail-value">{{ ucfirst($child->chd_residency_status) ?? 'N/A' }}</span>
                    </div>
                   <a href="{{ route('vaccinations.immunization-card', $child->chd_id) }}" class="btn btn-view-records shadow-sm mt-3">
                        <i class="fa-solid fa-file-medical me-2"></i>View Records
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <img src="{{ asset('images/empty.svg') }}" style="width: 150px; opacity: 0.5">
            <p class="text-muted mt-3">No child records found.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-5">
        {{-- Pagination Links centered --}}
        <div class="d-flex justify-content-center">
            {{ $children->links('partials.pagination') }}
        </div>
        {{-- Meta info centered below --}}
        <div class="text-center text-muted small mt-2">
            Showing {{ $children->firstItem() }} to {{ $children->lastItem() }} of {{ $children->total() }} records
    </div>

    @include('children.create')

    @foreach($children as $child)
        @include('children.edit')
        @include('children.delete')
    @endforeach
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // 1. Handle "Add Child" Modal
        if (urlParams.get('add') === 'true') {
            const registerModal = document.getElementById('registerChildModal');
            if (registerModal) {
                new bootstrap.Modal(registerModal).show();
            }
        }

        // 2. Handle "Delete Child" Modal
        const deleteId = urlParams.get('delete_id');
        if (deleteId) {
            const deleteModalElement = document.getElementById('deleteChildModal' + deleteId);
            if (deleteModalElement) {
                new bootstrap.Modal(deleteModalElement).show();
            }
        }

        // 3. Clean the URL once (removes ?add=true or ?delete_id=...)
        if (urlParams.has('add') || urlParams.has('delete_id')) {
            window.history.replaceState({}, document.title, window.location.pathname);
        }
    });
</script>

@endsection