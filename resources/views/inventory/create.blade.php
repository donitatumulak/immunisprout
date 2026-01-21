{{-- MODAL: create inventory record --}}
<div class="modal fade" id="addStockModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-success text-white py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-truck-ramp-box me-2"></i>Record New Shipment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                {{-- Display Validation Errors --}}
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="fa-solid fa-circle-exclamation me-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Vaccine Name <span class="text-danger">*</span></label>
                            <select name="inv_vaccine_id" class="form-select border-2 rounded-pill" required>
                                <option value="" selected disabled>Select Vaccine...</option>
                                @foreach($vaccineList as $vaccine)
                                    <option value="{{ $vaccine->vacc_id }}" {{ old('inv_vaccine_id') == $vaccine->vacc_id ? 'selected' : '' }}>
                                        {{ $vaccine->vacc_vaccine_name }} ({{ $vaccine->vacc_description }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Stock Qty (Vials) <span class="text-danger">*</span></label>
                            <input type="number" name="inv_quantity_available" 
                                   class="form-control border-2 rounded-pill" 
                                   placeholder="0" 
                                   value="{{ old('inv_quantity_available') }}"
                                   required min="1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small">Batch Number <span class="text-danger">*</span></label>
                            <input type="text" name="inv_batch_number" 
                                   class="form-control border-2 rounded-pill" 
                                   placeholder="Enter code" 
                                   value="{{ old('inv_batch_number') }}"
                                   required 
                                   maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Date Received</label>
                            <input type="date" name="inv_received_date" 
                                   class="form-control border-2 rounded-pill" 
                                   value="{{ old('inv_received_date', date('Y-m-d')) }}" 
                                   max="{{ date('Y-m-d') }}">
                            <small class="text-muted">Defaults to today if left blank</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Expiration Date <span class="text-danger">*</span></label>
                            <input type="date" name="inv_expiry_date" 
                                   class="form-control border-2 rounded-pill" 
                                   value="{{ old('inv_expiry_date') }}"
                                   required 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small">Supplier / Source</label>
                            <input type="text" name="inv_source" 
                                   class="form-control border-2 rounded-pill" 
                                   placeholder="e.g. DOH Central Office, Pfizer-GSK"
                                   value="{{ old('inv_source') }}"
                                   maxlength="150">
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold">
                            <i class="fa-solid fa-save me-2"></i>SAVE TO INVENTORY
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Auto-open modal if validation errors exist --}}
@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var addStockModal = new bootstrap.Modal(document.getElementById('addStockModal'));
        addStockModal.show();
    });
</script>
@endif