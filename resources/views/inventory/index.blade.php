@extends('layouts.internal')

@section('title', 'Inventory Management')

@section('content')
<style>
    .inventory-header-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        border: 2px solid var(--green);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .stat-pill {
        border-radius: 12px;
        padding: 15px;
        border: 1px solid #eee;
        background: var(--cream);
    }
    .table-inventory thead {
        background-color: var(--green);
        color: white;
    }

    .table-inventory thead th{
         padding: 10px;
    }

    .form-control:focus {
        border-color: var(--green);
        box-shadow: 0 0 0 0.25 rgba(46, 139, 87, 0.25);
    }
    .action-btn-group {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem; /* consistent spacing */
    }
    .action-btn {
        width: 27px;
        height: 27px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .action-btn:hover {
    transform: scale(1.05);
    }

</style>

<div id="main-content" class="px-4">
    <div class="page-header-card d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center">
            <div class="header-icon-bg me-3">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <h1 class="mb-1 header-title">Inventory Management</h1>
                <p class="text-muted mb-0 header-subtitle">
                    Manage stock levels, batch tracking, and expiration dates.
                </p>
            </div>
        </div>
        <div class="header-actions">
            <button class="btn btn-success rounded-pill px-4 fw-bold" 
                    style="background: var(--green);"
                    data-bs-toggle="modal" 
                    data-bs-target="#addStockModal"> 
                <i class="fa-solid fa-plus me-2"></i>Add New Entry
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-pill">
                <small class="text-muted text-uppercase fw-bold">Critical Stock</small>
                <h4 class="mb-0 text-danger fw-bold">{{ $criticalStockCount }} Batches</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-pill">
                <small class="text-muted text-uppercase fw-bold">Expiring (30 Days)</small>
                <h4 class="mb-0 text-warning fw-bold">{{ number_format($expiringSoonCount) }} Vials</h4>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-pill">
                <small class="text-muted text-uppercase fw-bold">Expired Stock</small>
                <h4 class="mb-0 text-danger fw-bold">{{ number_format($expiredCount) }} Vials</h4>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-4 shadow-sm border p-3 mb-3">
        <form action="{{ route('inventory.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Vaccine Type</label>
                <select name="vaccine_id" class="form-select rounded-pill">
                    <option value="all">All Vaccines</option>
                    @foreach($vaccineList as $vaccine)
                        <option value="{{ $vaccine->vacc_id }}" {{ request('vaccine_id') == $vaccine->vacc_id ? 'selected' : '' }}>
                            {{ $vaccine->vacc_vaccine_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">Stock Status</label>
                <select name="stock_status" class="form-select rounded-pill">
                    <option value="">All Levels</option>
                    <option value="critical" {{ request('stock_status') == 'critical' ? 'selected' : '' }}>Critical (≤10)</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low (11-50)</option>
                    <option value="good" {{ request('stock_status') == 'good' ? 'selected' : '' }}>Good (>50)</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold">Expiry Status</label>
                <select name="expiry_status" class="form-select rounded-pill">
                    <option value="">All Status</option>
                    <option value="expired" {{ request('expiry_status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="expiring_soon" {{ request('expiry_status') == 'expiring_soon' ? 'selected' : '' }}>Expiring Soon (30 days)</option>
                    <option value="good" {{ request('expiry_status') == 'good' ? 'selected' : '' }}>Good (>30 days)</option>
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-success rounded-pill w-100">
                    <i class="fa-solid fa-filter me-1"></i>Apply Filters
                    </button>
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary rounded-pill">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-4 shadow-sm border overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 table-inventory">
                <thead>
                    <tr class="small text-uppercase">
                        <th class="ps-4">Vaccine Details</th>
                        <th class="text-center">Stock Quantity</th>
                        <th>Batch / Lot #</th>
                        <th>Expiration</th>
                        <th>Supplier</th>
                        <th class="text-center pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody id="inventoryTableBody">
                    @forelse($inventory as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold">{{ $item->vaccine->vacc_vaccine_name }}</div>
                                <div class="small text-muted">Received: {{ \Carbon\Carbon::parse($item->inv_received_date)->format('M d, Y') }}</div>
                            </td>
                
                            <td class="text-center">
                                <div class="fw-bold @if($item->inv_quantity_available <= 10) text-danger @endif">
                                    {{ $item->inv_quantity_available }} Vials
                                </div>
                                <div class="progress mt-1" style="height: 4px; width: 80px; margin: 0 auto;">
                                    @php
                                        $stockLevel = ($item->inv_quantity_available / 100) * 100;
                                        $barColor = $item->inv_quantity_available <= 10 ? 'bg-danger' : 'bg-success';
                                    @endphp
                                    <div class="progress-bar {{ $barColor }}" style="width: {{ min($stockLevel, 100) }}%"></div>
                                </div>
                            </td>
                            <td><code class="text-dark">{{ $item->inv_batch_number }}</code></td>
                            <td>
                                <span class="fw-bold {{ $item->inv_expiry_date <= now() ? 'text-danger' : '' }}">
                                    {{ \Carbon\Carbon::parse($item->inv_expiry_date)->format('m/d/Y') }}
                                </span>
                            </td>
                            <td class="small">{{ $item->inv_source ?? 'N/A' }}</td>
                            <td class="text-center pe-4">
                            <div class="action-btn-group d-flex justify-content-center align-items-center gap-2">
                                {{-- Quick Adjust Button --}}
                                <button type="button" 
                                        class="btn btn-outline-warning rounded-circle action-btn"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#adjustStockModal{{ $item->inv_id }}"
                                        title="Quick Adjust">
                                    <i class="fa-solid fa-arrows-up-down"></i>
                                </button>

                                {{-- Edit Button --}}
                                <button type="button" 
                                        class="btn btn-outline-success rounded-circle edit-btn action-btn"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editStockModal"
                                        data-id="{{ $item->inv_id }}"
                                        data-vaccine="{{ $item->inv_vaccine_id }}"
                                        data-batch="{{ $item->inv_batch_number }}"
                                        data-qty="{{ $item->inv_quantity_available }}"
                                        data-expiry="{{ $item->inv_expiry_date }}"
                                        data-received="{{ $item->inv_received_date }}"
                                        data-source="{{ $item->inv_source }}">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                {{-- Delete Button --}}
                                @if(auth()->user()->canDelete())
                                    <button type="button" class="btn btn-outline-danger rounded-circle action-btn" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteInventoryModal{{ $item->inv_id }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                @endif

                                {{-- View Details Button --}}
                                <a href="{{ route('inventory.show', $item->inv_id) }}" 
                                class="btn btn-outline-primary rounded-circle action-btn"
                                title="View Logs">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                            
                            {{-- Quick Adjust Modal (placed here per item) --}}
                            @include('inventory.adjust-modal', ['item' => $item])
                            @include('inventory.delete', ['item' => $item])
                        </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No inventory records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-5">
            {{ $inventory->links('partials.pagination') }}
        </div>
</div>

{{-- ACCESSING THE MODAL FILES --}}
    @include('inventory.create') 
    @include('inventory.edit')
@endsection