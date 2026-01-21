@extends('layouts.result')

@section('title', 'Immunization Record - ' . $child->full_name)

@section('content')

<style>

    @media print {
    body * {
        visibility: hidden;
    }
    .document-body, .document-body * {
        visibility: visible;
    }
    .document-body {
        position: absolute;
        left: 0;
        top: 0;
    }
}
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        padding: 1.5rem;
        border-top: 2px solid #e9ecef;
        flex-wrap: wrap;
    }

    .btn-custom-green {
        background: var(--green, #198754);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        transition: all 0.3s;
    }

    .btn-custom-green:hover {
        background: #157347;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
    }

    .btn-outline-secondary {
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
    }

    /* Public notice banner */
    .public-notice {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 1rem;
        margin-bottom: 2rem;
        border-radius: 4px;
    }
</style>

<div id="main-content">
    <div class="container">
        <div class="document-container">
            {{-- Document Header --}}
            <div class="document-header d-print-none">
                <div>
                    <h4>
                        <i class="fas fa-file-medical me-2"></i>
                        Official Immunization Record
                    </h4>
                    <small>{{ $child->full_name }}</small>
                </div>
                <a href="{{ route('public.search-record') }}" class="btn btn-sm btn-light">
                    <i class="fas fa-arrow-left me-2"></i>New Search
                </a>
            </div>

            {{-- Document Body --}}
            <div class="document-body">
                {{-- Public Notice --}}
                <div class="public-notice d-print-none">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-shield-alt text-warning me-3 mt-1" style="font-size: 1.5rem;"></i>
                        <div>
                            <strong>Privacy Notice:</strong> This is a verified digital copy of your child's vaccination record. 
                            Keep this information confidential. For any corrections or concerns, please visit your Barangay Health Center.
                        </div>
                    </div>
                </div>

                 {{-- Official Header --}}
            <div class="official-header">
                <div class="row align-items-center">
                    <div class="col-md-6 d-flex align-items-center mb-3 mb-md-0">
                        <img src="{{ asset('images/logo-grn.png') }}" alt="Logo" class="me-3" style="width: 100px; height: auto;">
                        <div>
                            <h5 class="mb-0">ImmuniSprout Registry</h5>
                            <p class="mb-0 small">Brgy. {{ auth()->user()->worker->address->addr_barangay ?? 'Pusok' }} Health Center</p>
                            <p class="mb-0 small text-uppercase">City of {{ auth()->user()->worker->address->addr_city_municipality ?? 'Lapu-Lapu' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p class="mb-1">
                            <strong>Reference No:</strong> 
                            <span class="text-warning fw-bold">IS-{{ date('Y') }}-{{ str_pad($child->chd_id, 4, '0', STR_PAD_LEFT) }}</span>
                        </p>
                        <p class="mb-0">
                            <strong>Status:</strong> 
                            <span class="badge bg-{{ $child->status_details['color'] }}">
                                {{ ucfirst($child->chd_status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Child Information Grid --}}
            <div class="child-info-grid">
                <div class="info-item">
                    <p class="info-label">Child's Name</p>
                    <p class="info-value border-bottom pb-1">{{ $child->full_name }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Guardian's Name</p>
                    <p class="info-value border-bottom pb-1">{{ $child->guardian->full_name ?? 'N/A' }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Contact Number</p>
                    <p class="info-value border-bottom pb-1">{{ $child->guardian->grd_contact_number ?? 'N/A' }}</p>
                </div>

                <div class="info-item">
                    <p class="info-label">Sex</p>
                    <p class="info-value border-bottom pb-1">{{ ucfirst($child->chd_sex) }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Age</p>
                    <p class="info-value border-bottom pb-1">
                        @php
                            $birthDate = \Carbon\Carbon::parse($child->chd_date_of_birth);
                            $diff = $birthDate->diff(\Carbon\Carbon::now());
                            $ageStr = $diff->y > 0 ? $diff->y . 'y ' . $diff->m . 'm' : $diff->m . ' months';
                        @endphp
                        {{ $ageStr }} old
                    </p>
                </div>
                <div class="info-item">
                    <p class="info-label">Date of Birth</p>
                    <p class="info-value border-bottom pb-1">{{ \Carbon\Carbon::parse($child->chd_date_of_birth)->format('F d, Y') }}</p>
                </div>

                <div class="info-item">
                    <p class="info-label">Place of Birth</p>
                    <p class="info-value border-bottom pb-1">{{ $child->address->addr_city_municipality ?? 'N/A' }}</p>
                </div>
                <div class="info-item address-field">
                    <p class="info-label">Address</p>
                    <p class="info-value border-bottom pb-1">{{ $child->address->full_address ?? 'N/A' }}</p>
                </div>
            </div>

                {{-- Vaccination Records Table --}}
                <div class="table-responsive">
                    <table class="vaccination-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Vaccine Name</th>
                                <th style="width: 7%;">Dose</th>
                                <th style="width: 18%;">Date</th>
                                <th style="width: 25%;">Administered By</th>
                                <th style="width: 10%;">Status</th>
                                <th style="width: 15%;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($immunizationData as $vaccineData)
                                @foreach($vaccineData['doses'] as $index => $dose)
                                    <tr>
                                        @if($index === 0)
                                            <td class="vaccine-name {{ count($vaccineData['doses']) > 1 ? 'bg-light-subtle' : '' }}" 
                                                rowspan="{{ count($vaccineData['doses']) }}">
                                                {{ $vaccineData['vaccine']->vacc_vaccine_name }}
                                            </td>
                                        @endif
                                        
                                        <td class="{{ $index > 0 ? 'border-start' : '' }}">
                                            {{ $dose['schedule']->nip_dose_number }}
                                        </td>
                                        
                                        <td>
                                            @if($dose['record'])
                                                {{ \Carbon\Carbon::parse($dose['record']->rec_date_administered)->format('M d, Y') }}
                                            @else
                                                <span class="text-muted print-hide">-- / -- / --</span>
                                            @endif
                                        </td>
                                        
                                       <td>
                                            @if($dose['record'] && $dose['record']->administrator)
                                                {{-- Use the actual name column from your health_workers table --}}
                                                {{ $dose['record']->administrator->wrk_name ?? $dose['record']->administrator->full_name ?? 'Staff' }}
                                            @else
                                                <span class="text-muted print-hide">---</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            @if($dose['status'] === 'completed')
                                                <span class="badge-complete">COMPLETE</span>
                                            @elseif($dose['status'] === 'overdue')
                                                <span class="badge-overdue">OVERDUE</span>
                                            @elseif($dose['status'] === 'upcoming')
                                                <span class="badge-upcoming">UPCOMING</span>
                                            @else
                                                <span class="badge-not-yet-due">Not Yet Due</span>
                                            @endif
                                        </td>
                                        
                                        <td>
                                            <span class="print-hide">{{ $dose['record']->rec_remarks ?? '---' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Completion Summary (Public Only) --}}
                <div class="d-print-none mb-4">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h3 class="text-success mb-0">{{ $completionPercentage }}%</h3>
                                <small class="text-muted">Completed</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h3 class="mb-0">{{ $completedDoses }}/{{ $totalDoses }}</h3>
                                <small class="text-muted">Doses Given</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h3 class="mb-0">{{ floor($childAgeDays / 30) }}</h3>
                                <small class="text-muted">Months Old</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer Signatures (for print only) --}}
                <div class="d-none d-print-block mt-5">
                    <div class="row">
                        <div class="col-6 text-center">
                            <div style="border-bottom: 1px solid #333; width: 70%; margin: 0 auto 10px;"></div>
                            <p class="small fw-bold">HEALTH WORKER SIGNATURE</p>
                        </div>
                        <div class="col-6 text-center">
                            <div style="border-bottom: 1px solid #333; width: 70%; margin: 0 auto 10px;"></div>
                            <p class="small fw-bold">OFFICIAL CENTER STAMP</p>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Generated on: {{ now()->format('F d, Y h:i A') }} | 
                            System ID: IS-{{ uniqid() }}
                        </small>
                        <p class="x-small text-muted mt-2">
                            * This is an official digital-to-physical record from the ImmuniSprout Registry. 
                            Please present this card during every health center visit.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="action-buttons d-print-none">
                <button type="button" onclick="window.print()" class="btn-custom-green">
                    <i class="fa-solid fa-print me-2"></i> Print Official Record
                </button>
                <a href="{{ route('public.search-record') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-search me-2"></i> Search Another Record
                </a>
            </div>
        </div>
    </div>
</div>

@endsection