{{-- MODAL: EDIT inventory --}}
<div class="modal fade" id="editStockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-success text-white py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Batch Details</h5>
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

                <form id="editInventoryForm" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="alert alert-info border-0 mb-3">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <small><strong>Note:</strong> Changes to quantity will be logged in the audit trail.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Vaccine Name <span class="text-danger">*</span></label>
                            <select name="inv_vaccine_id" id="edit_vaccine_id" class="form-select border-2 rounded-pill" required>
                                @foreach($vaccineList as $vaccine)
                                    <option value="{{ $vaccine->vacc_id }}">{{ $vaccine->vacc_vaccine_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Stock Qty (Vials) <span class="text-danger">*</span></label>
                            <input type="number" name="inv_quantity_available" id="edit_qty" 
                                   class="form-control border-2 rounded-pill" 
                                   required min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Date Received</label>
                            <input type="date" name="inv_received_date" id="edit_received" 
                                   class="form-control border-2 rounded-pill"
                                   max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Expiration Date <span class="text-danger">*</span></label>
                            <input type="date" name="inv_expiry_date" id="edit_expiry" 
                                   class="form-control border-2 rounded-pill" 
                                   required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Batch Number <span class="text-danger">*</span></label>
                            <input type="text" name="inv_batch_number" id="edit_batch" 
                                   class="form-control border-2 rounded-pill" 
                                   required
                                   maxlength="100">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">Supplier / Source</label>
                            <input type="text" name="inv_source" id="edit_source" 
                                   class="form-control border-2 rounded-pill"
                                   maxlength="150">
                        </div>

                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success w-100 py-3 rounded-pill fw-bold">
                            <i class="fa-solid fa-check me-2"></i>UPDATE INVENTORY
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.edit-btn');
        const editForm = document.getElementById('editInventoryForm');

        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Get data from button attributes
                const id = this.getAttribute('data-id');
                const vaccineId = this.getAttribute('data-vaccine');
                const batch = this.getAttribute('data-batch');
                const qty = this.getAttribute('data-qty');
                const expiry = this.getAttribute('data-expiry');
                const received = this.getAttribute('data-received');
                const source = this.getAttribute('data-source');

                // Set the Form Action URL to match route
                editForm.action = `/inventory/${id}`;

                // Fill the inputs
                document.getElementById('edit_vaccine_id').value = vaccineId || '';
                document.getElementById('edit_batch').value = batch || '';
                document.getElementById('edit_qty').value = qty || '';
                document.getElementById('edit_expiry').value = expiry || '';
                document.getElementById('edit_received').value = received || '';
                document.getElementById('edit_source').value = source || '';
            });
        });
    });
</script>

{{-- Auto-open edit modal if validation errors exist and we're editing --}}
@if ($errors->any() && session('editing'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var editStockModal = new bootstrap.Modal(document.getElementById('editStockModal'));
        editStockModal.show();
    });
</script>
@endif