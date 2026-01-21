@extends('layouts.internal')

@section('title', 'Immunization Record - ' . $child->full_name)

@section('content')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/print.css') }}">
@endpush

<style>
       @media print {
    .vaccination-table th,
    .vaccination-table td,
    .badge-complete,
    .badge-upcoming,
    .badge-overdue {
        line-height: 1.1 !important;
    }
}
    /* Editable Field */
    .editable-field {
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 4px;
        transition: all 0.2s;
        display: inline-block;
        min-width: 100px;
    }

    .editable-field:hover {
        background: #fff3cd;
        border: 1px dashed #ffc107;
    }

    .editable-field.editing {
        background: white;
        border: 2px solid #198754;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        padding: 1.5rem;
        border-top: 2px solid #e9ecef;
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
</style>

<div id="main-content">
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
            <a href="{{ route('vaccinations.index') }}" class="text-white fs-4"><i class="fa-regular fa-circle-xmark"></i></a>
        </div>

        {{-- Document Body --}}
        <div class="document-body">
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
                                            <span class="editable-field cursor-pointer" 
                                                onclick="openEditModal({{ $dose['record']->rec_id }}, 'rec_date_administered', '{{ $dose['record']->rec_date_administered }}')">
                                                {{ \Carbon\Carbon::parse($dose['record']->rec_date_administered)->format('m/d/Y') }}
                                            </span>
                                        @else
                                            @if($dose['can_administer'])
                                                <button class="btn-administer d-print-none" 
                                                        onclick="administerVaccine({{ $child->chd_id }}, {{ $vaccineData['vaccine']->vacc_id }}, {{ $dose['schedule']->nip_dose_number }})">
                                                    Administer
                                                </button>
                                            @endif
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if($dose['record'] && $dose['record']->administrator)
                                            {{ $dose['record']->administrator->user->worker->full_name }}
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
                                            <span class="text-muted print-hide">---</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if($dose['record'])
                                            <span class="editable-field cursor-pointer print-hide" 
                                                onclick="openEditModal({{ $dose['record']->rec_id }}, 'rec_remarks', '{{ addslashes($dose['record']->rec_remarks ?? '') }}')">
                                                {{ $dose['record']->rec_remarks ?: '---' }}
                                            </span>
                                        @else
                                            <span class="text-muted print-hide">---</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer Signatures (for print only) --}}
            <div class="d-none d-print-block print-signatures">
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
                <div class="text-center mt-2">
                    <small class="text-muted">
                        Generated: {{ now()->format('M d, Y h:i A') }} | ID: IS-{{ uniqid() }}
                    </small>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="action-buttons d-print-none">
            <button onclick="window.print()" class="btn-custom-green">
                <i class="fa-solid fa-print me-2"></i> Print Official Record
            </button>
            <a href="{{ route('vaccinations.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to Registry
            </a>
        </div>
    </div>
</div>

<div class="modal fade" id="administerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form action="{{ route('vaccinations.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header bg-success text-white py-3">
                <h5 class="modal-title"><i class="fa-solid fa-shield-virus"></i> Administer Vaccine Dose</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="rec_child_id" id="admin_child_id">
                <input type="hidden" name="rec_vaccine_id" id="admin_vacc_id">
                <input type="hidden" name="rec_dose_number" id="admin_dose_num">
                <input type="hidden" name="rec_administered_by" value="{{ auth()->user()->worker->wrk_id ?? 1 }}">

                <div class="mb-3">
                    <label class="form-label">Date Administered</label>
                    <input type="date" name="rec_date_administered" class="form-control border-2 rounded-pill" 
                           value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <textarea name="rec_remarks" class="form-control border-2 " rows="2" placeholder="Optional notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm">Confirm Administration</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="quickEditModal" tabindex="-1" aria-labelledby="quickEditModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title d-flex align-items-center" id="quickEditModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    Update Vaccination Record
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="edit_record_id">
                <input type="hidden" id="edit_field_name">
                
                <div class="alert alert-info border-0 d-flex align-items-start mb-3" role="alert">
                    <i class="fas fa-info-circle me-2 mt-1"></i>
                    <small>Click on the field to make changes to this vaccination record.</small>
                </div>
                
                <div id="edit_input_container">
                    <!-- Dynamic input will be inserted here -->
                </div>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-success rounded-pill px-4 shadow-sm" id="btnSaveQuickEdit">
                    <i class="fas fa-check me-2"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. ADMINISTER VACCINE LOGIC
        window.administerVaccine = function(childId, vaccineId, doseNumber) {
            document.getElementById('admin_child_id').value = childId;
            document.getElementById('admin_vacc_id').value = vaccineId;
            document.getElementById('admin_dose_num').value = doseNumber;
            
            new bootstrap.Modal(document.getElementById('administerModal')).show();
        };

        // 2. QUICK EDIT LOGIC - Define the openEditModal function
        const editModal = new bootstrap.Modal(document.getElementById('quickEditModal'));
        const container = document.getElementById('edit_input_container');

        window.openEditModal = function(recordId, fieldName, currentValue) {
            document.getElementById('edit_record_id').value = recordId;
            document.getElementById('edit_field_name').value = fieldName;

             // Generate the right input type with Bootstrap styling
                if (fieldName === 'rec_date_administered') {
                    container.innerHTML = `
                        <div class="mb-0">
                            <label class='form-label fw-semibold'>
                                <i class="fas fa-calendar-alt text-success me-2"></i>Date Administered
                            </label>
                            <input type="date" 
                                id="edit_input" 
                                class="form-control form-control-lg border-2 rounded-3" 
                                value="${currentValue}" 
                                max="{{ date('Y-m-d') }}"
                                required>
                            <small class="text-muted">Enter the date when this vaccine was given</small>
                        </div>`;
                } else if (fieldName === 'rec_remarks') {
                    container.innerHTML = `
                        <div class="mb-0">
                            <label class='form-label fw-semibold'>
                                <i class="fas fa-comment-medical text-success me-2"></i>Remarks
                            </label>
                            <textarea id="edit_input" 
                                    class="form-control form-control-lg border-2 rounded-3" 
                                    rows="4" 
                                    placeholder="Add any observations, reactions, or notes...">${currentValue || ''}</textarea>
                            <small class="text-muted">Optional notes about this vaccination</small>
                        </div>`;
                }

            editModal.show();
        };

        // 3. AJAX SAVE FOR QUICK EDIT
        document.getElementById('btnSaveQuickEdit').addEventListener('click', function() {
            const recordId = document.getElementById('edit_record_id').value;
            const fieldName = document.getElementById('edit_field_name').value;
            const newValue = document.getElementById('edit_input').value;

            // SAFETY CHECK: Prevent sending "undefined" to the server
            if (!recordId || recordId === "undefined" || !recordId.trim()) {
                alert('Error: Record ID is missing. Please refresh the page.');
                return;
            }

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

            fetch(`/vaccinations/${recordId}/quick-update`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ [fieldName]: newValue })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    editModal.hide();
                    location.reload(); // Refresh to update display
                } else {
                    alert('Update failed: ' + (data.message || 'Unknown error'));
                    this.disabled = false;
                    this.innerHTML = 'Save Changes';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving. Please try again.');
                this.disabled = false;
                this.innerHTML = 'Save Changes';
            });
        });
    });
</script>

@endsection