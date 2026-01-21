@extends('layouts.internal')

@section('title', 'Inventory Log (Read-Only)')

@section('content')

<style>
.floating-back-btn {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 55px;
    height: 55px;
    z-index: 1000;
    transition: all 0.3s ease;
    border: 2px solid #fff;
    background: var(--orange);
}

.floating-back-btn:hover {
    transform: translateY(-5px); /* Subtle lift effect */
    background-color: #fda90d; /* Darker blue on hover */
    box-shadow: 0 10px 20px rgba(0,0,0,0.2) !important;
}
</style>
{{-- Optional: Add a summary card above the logs --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-box-open fa-2x text-primary mb-2"></i>
                <h6 class="text-muted small mb-0">Current Stock</h6>
                <h3 class="fw-bold mb-0">{{ $inventory->inv_quantity_available }}</h3>
                <small class="text-muted">vials</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-calendar-xmark fa-2x text-danger mb-2"></i>
                <h6 class="text-muted small mb-0">Expiry Date</h6>
                <h5 class="fw-bold mb-0">{{ \Carbon\Carbon::parse($inventory->inv_expiry_date)->format('M d, Y') }}</h5>
                <small class="text-{{ $inventory->inv_expiry_date < now() ? 'danger' : ($inventory->inv_expiry_date <= now()->addDays(30) ? 'warning' : 'success') }}">
                    @if($inventory->inv_expiry_date < now())
                        Expired
                    @elseif($inventory->inv_expiry_date <= now()->addDays(30))
                        {{ \Carbon\Carbon::parse($inventory->inv_expiry_date)->diffForHumans() }}
                    @else
                        {{ \Carbon\Carbon::parse($inventory->inv_expiry_date)->diffForHumans() }}
                    @endif
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-tag fa-2x text-info mb-2"></i>
                <h6 class="text-muted small mb-0">Batch Number</h6>
                <h5 class="fw-bold mb-0"><code>{{ $inventory->inv_batch_number }}</code></h5>
                <small class="text-muted">Primary Batch Reference</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <i class="fa-solid fa-truck fa-2x text-success mb-2"></i>
                <h6 class="text-muted small mb-0">Supplier</h6>
                <h5 class="fw-bold mb-0 small">{{ $inventory->inv_source ?? 'N/A' }}</h5>
                <small class="text-muted">Received: {{ \Carbon\Carbon::parse($inventory->inv_received_date)->format('M d, Y') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="fa-solid fa-clock-rotate-left me-2"></i>Stock Movement History
        </h5>
        <span class="badge bg-soft-primary text-primary rounded-pill px-3">
            {{ $inventory->logs->count() }} Total Activities
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-uppercase text-muted">
                        <th class="ps-4" style="width: 18%">Date & Time</th>
                        <th style="width: 15%">Activity</th>
                        <th class="text-center" style="width: 10%">Change</th>
                        <th style="width: 18%">Performed By</th>
                        <th class="pe-4" style="width: 39%">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventory->logs as $log)
                        <tr>
                            {{-- DATE & TIME --}}
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $log->created_at->format('M d, Y') }}</div>
                                <div class="small text-muted">{{ $log->created_at->format('h:i A') }}</div>
                            </td>

                            {{-- ACTIVITY TYPE --}}
                            <td>
                                @php
                                    $badgeConfig = [
                                        'restock'        => ['class' => 'bg-success-subtle text-success border border-success-subtle', 'icon' => 'fa-truck-ramp-box'],
                                        'usage'          => ['class' => 'bg-info-subtle text-info border border-info-subtle', 'icon' => 'fa-syringe'],
                                        'wastage'        => ['class' => 'bg-danger-subtle text-danger border border-danger-subtle', 'icon' => 'fa-trash'],
                                        'return'         => ['class' => 'bg-primary-subtle text-primary border border-primary-subtle', 'icon' => 'fa-rotate-left'],
                                        'found'          => ['class' => 'bg-success-subtle text-success border border-success-subtle', 'icon' => 'fa-magnifying-glass'],
                                        'correction_add' => ['class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'icon' => 'fa-plus'],
                                        'correction_sub' => ['class' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle', 'icon' => 'fa-minus'],
                                        'info_update'    => ['class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle', 'icon' => 'fa-pen'],
                                        'deletion'       => ['class' => 'bg-dark-subtle text-dark border border-dark-subtle', 'icon' => 'fa-trash-can']
                                    ];
                                    $config = $badgeConfig[$log->log_change_type] ?? ['class' => 'bg-secondary-subtle text-secondary', 'icon' => 'fa-circle-info'];
                                @endphp
                                <span class="badge {{ $config['class'] }} px-2 py-1 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                    <i class="fa-solid {{ $config['icon'] }} me-1"></i>
                                    {{ str_replace('_', ' ', $log->log_change_type) }}
                                </span>
                            </td>

                            {{-- QUANTITY CHANGE --}}
                            <td class="text-center">
                                @if($log->log_change_type == 'info_update' || $log->log_change_type == 'deletion')
                                    <span class="badge rounded-pill bg-secondary px-2">—</span>
                                @elseif(in_array($log->log_change_type, ['restock', 'return', 'found', 'correction_add']))
                                    <span class="badge rounded-pill bg-success px-2">+{{ $log->log_quantity_changed }}</span>
                                @else
                                    <span class="badge rounded-pill bg-danger px-2">-{{ $log->log_quantity_changed }}</span>
                                @endif
                            </td>

                            {{-- PERFORMED BY (Using log_user_id) --}}
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        @if($log->user)
                                            <i class="fa-solid fa-user text-primary" style="font-size: 0.85rem;"></i>
                                        @else
                                            <i class="fa-solid fa-robot text-muted" style="font-size: 0.85rem;"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="small fw-semibold">
                                            {{ $log->user->name ?? 'System' }}
                                        </div>
                                        @if($log->user)
                                            <div class="extra-small text-muted">{{ $log->user->email ?? $log->user->username ?? '' }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- REMARKS --}}
                            <td class="pe-4">
                                <span class="small text-muted">
                                    @if($log->log_remarks)
                                        {{ $log->log_remarks }}
                                    @else
                                        {{-- Fallback descriptions if no remarks --}}
                                        @switch($log->log_change_type)
                                            @case('restock')
                                                Initial stock entry for batch {{ $inventory->inv_batch_number }}.
                                                @break
                                            @case('usage')
                                                Vaccine doses used for administration.
                                                @break
                                            @case('wastage')
                                                Stock removed due to wastage/damage.
                                                @break
                                            @case('return')
                                                Stock returned to inventory.
                                                @break
                                            @case('found')
                                                Stock discrepancy corrected (found additional units).
                                                @break
                                            @case('correction_add')
                                                Manual quantity adjustment (increased).
                                                @break
                                            @case('correction_sub')
                                                Manual quantity adjustment (decreased).
                                                @break
                                            @case('info_update')
                                                Batch information updated.
                                                @break
                                            @case('deletion')
                                                Batch removed from inventory.
                                                @break
                                            @default
                                                Stock adjustment recorded.
                                        @endswitch
                                    @endif
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-center">
                                    <i class="fa-solid fa-inbox fa-4x text-muted opacity-25 mb-3"></i>
                                    <p class="text-muted mb-0">No transaction history found for this batch.</p>
                                    <p class="small text-muted">All stock movements will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('inventory.index') }}" 
    class="btn rounded-circle shadow-lg floating-back-btn d-flex align-items-center justify-content-center"
    title="Back to Inventory">
    <i class="fa-solid fa-arrow-left"></i>
</a>
</div>
@endsection