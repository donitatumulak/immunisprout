@extends('layouts.internal')

@section('title', 'Immunization Records')

@section('content')

<style>
    /* Table Styling */
    .records-table {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .records-table thead {
        background-color: var(--green) !important;
        color: white !important;
    }

    .records-table thead th {
        padding: 1rem;
        font-weight: 600;
        border: none;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .records-table tbody tr {
        transition: all 0.2s;
        border-bottom: 1px solid #f1f5f9;
    }

    .records-table tbody tr:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .records-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .avatar-initials {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e8f5e9;
        color: var(--green);
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        border: 1px solid rgba(25, 135, 84, 0.1);
    }

  /* Base Badge Style - Shared properties */
    [class^="badge-"] {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-complete {
        background-color: #d1fae5; /* Light green */
        color: #065f46;            /* Dark green text */
    }

    .badge-active {
        background-color: #dbeafe; /* Light blue */
        color: #1e40af;            /* Dark blue text */
    }

    .badge-inactive {
        background-color: #fef3c7; /* Light amber */
        color: #92400e;            /* Dark amber text */
    }

    .badge-transferred {
        background-color: #fee2e2; /* Light red */
        color: #991b1b;            /* Dark red text */
    }
    /* Action Buttons */
    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
    }

    .btn-view, .bth-print {
        background: var(--green);
        color: white;
    }

    .btn-print:hover, .btn-view:hover {
        background: #157347;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    .empty-state h4 {
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #94a3b8;
    }
</style>

<div id="main-content" class="px-4">
    {{-- Header Section --}}
    <div class="page-header-card d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center">
            <div class="header-icon-bg me-3">
                <i class="fa-solid fa-syringe"></i>
            </div>
            <div>
                <h1 class="mb-1 header-title">Immunization Registry</h1>
                <p class="text-muted mb-0 header-subtitle">
                    Manage and Track Child Vaccination Records
                </p>
            </div>
        </div>
        <div class="header-actions">
            <a href="{{ route('children.index', ['add' => 'true']) }}" class="btn btn-success rounded-pill px-4 fw-bold" 
                style="background: var(--green);">
                <i class="fa-solid fa-plus me-2"></i>Add New Child
            </a>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="filter-card bg-white rounded-4 shadow-sm border p-3 mb-3">
        <form method="GET" action="{{ route('vaccinations.index') }}" id="filterForm">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold small">Search Child</label>
                            <div class="search-wrapper position-relative">
                            <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%);"></i>
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control search-input shadow-sm ps-5" placeholder="Search by name..." style="border-radius: 50px;">
                        </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small">Status</label>
                    <select name="status" class="form-select rounded-pill">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                        <option value="completed" {{ request('status') == 'complete' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small">Age Range</label>
                    <select name="age_range" class="form-select rounded-pill">
                        <option value="">All Ages</option>
                        <option value="0-6" {{ request('age_range') == '0-6' ? 'selected' : '' }}>0-6 months</option>
                        <option value="7-12" {{ request('age_range') == '7-12' ? 'selected' : '' }}>7-12 months</option>
                        <option value="13-24" {{ request('age_range') == '13-24' ? 'selected' : '' }}>13-24 months</option>
                        <option value="25+" {{ request('age_range') == '25+' ? 'selected' : '' }}>25+ months</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-bold small">Sort By</label>
                    <select name="sort" class="form-select rounded-pill">
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                        <option value="age_asc" {{ request('sort') == 'age_asc' ? 'selected' : '' }}>Age (Youngest)</option>
                        <option value="age_desc" {{ request('sort') == 'age_desc' ? 'selected' : '' }}>Age (Oldest)</option>
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest Vaccine</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-success flex-fill rounded-pill">
                        <i class="fa-solid fa-filter me-2"></i>Apply Filter
                    </button>
                    <a href="{{ route('vaccinations.index') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Records Table --}}
    <div class="records-table">
        <div class="table-responsive">
                @if($children->count() > 0)
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Reference No.</th>
                            <th>Child Name</th>
                            <th>Date of Birth</th>
                            <th>Age</th>
                            <th class="text-center">Status</th>
                            <th>Latest Vaccine</th>
                            <th>Completion</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($children as $child)
                            @php
                                // 1. Calculate Detailed Age
                                $birthDate = \Carbon\Carbon::parse($child->chd_date_of_birth);
                                $diff = $birthDate->diff(\Carbon\Carbon::now());
                                
                                if ($diff->y > 0) {
                                    $ageDisplay = $diff->y . 'y ' . $diff->m . 'm';
                                } else {
                                    $ageDisplay = $diff->m . ' month' . ($diff->m != 1 ? 's' : '');
                                }

                                // 2. Get Latest Vaccine
                                $latestVaccine = $child->vaccinationRecords()
                                    ->where('rec_status', 'completed')
                                    ->latest('rec_date_administered')
                                    ->first();
                                
                                // 3. Calculate completion
                                $totalDoses = \App\Models\NipSchedule::count();
                                $completedDoses = $child->vaccinationRecords()
                                    ->where('rec_status', 'completed')
                                    ->count();
                                $completionPercentage = $totalDoses > 0 ? round(($completedDoses / $totalDoses) * 100) : 0;
                            @endphp
                            <tr>
                                <td class="fw-bold text-success">IS-{{ date('Y') }}-{{ str_pad($child->chd_id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initials me-2">
                                            {{ substr($child->chd_first_name, 0, 1) }}{{ substr($child->chd_last_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $child->full_name }}</div>
                                            <small class="text-muted">
                                                <i class="fas fa-venus-mars me-1"></i>{{ ucfirst($child->chd_sex) }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($child->chd_date_of_birth)->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        {{ $ageDisplay }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-{{ $child->chd_status }}">
                                        <i class="fas {{ $child->status_details['icon'] }} me-1"></i>
                                        {{ ucfirst($child->chd_status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($latestVaccine)
                                        <div class="small">
                                            <strong>{{ $latestVaccine->vaccine->vacc_vaccine_name }}</strong>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                {{ \Carbon\Carbon::parse($latestVaccine->rec_date_administered)->format('M d, Y') }}
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">No vaccines yet</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 8px; width: 80px;">
                                            <div class="progress-bar bg-success" 
                                                style="--progress-width: {{ $completionPercentage }}%; width: var(--progress-width);">
                                            </div>
                                        </div>
                                        <span class="small fw-bold text-success">{{ $completionPercentage }}%</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('vaccinations.immunization-card', $child) }}" 
                                    class="btn btn-action btn-view rounded-3 btn-sm  me-2" 
                                    title="View Immunization Card">
                                        <i class="fa-solid fa-file-medical"></i> View Card
                                    </a>
                                    @if(auth()->user()->canDelete())
                                    <a href="{{ route('children.index', ['delete_id' => $child->chd_id]) }}" 
                                        class="btn btn-danger btn-sm rounded-3 me-2">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fa-solid fa-folder-open"></i>
                <h4>No Records Found</h4>
                <p>There are no immunization records matching your filters.</p>
                @if(request()->hasAny(['search', 'status', 'age_range']))
                    <a href="{{ route('vaccinations.index') }}" class="btn btn-outline-primary mt-3">
                        <i class="fa-solid fa-rotate-right me-2"></i>Clear Filters
                    </a>
                @endif
            </div>
        @endif
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
</div>
</div>

<script>
// Auto-submit form on filter change
document.querySelectorAll('#filterForm select').forEach(select => {
    select.addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
});
</script>

@endsection