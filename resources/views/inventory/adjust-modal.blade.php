<div class="modal fade" id="adjustStockModal{{ $item->inv_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-warning text-white" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-arrows-up-down me-2"></i>Quick Stock Adjustment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('inventory.adjust', $item->inv_id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-light border">
                        <strong>{{ $item->vaccine->vacc_vaccine_name }}</strong><br>
                        <small class="text-muted">Batch: {{ $item->inv_batch_number }}</small><br>
                        <small>Current Stock: <strong>{{ $item->inv_quantity_available }} vials</strong></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Adjustment Type</label>
                        <select name="adjustment_type" class="form-select rounded-pill" required>
                            <option value="">Select Type</option>
                            <option value="usage">Usage (Administered)</option>
                            <option value="wastage">Wastage (Damaged/Expired)</option>
                            <option value="return">Return (Restock)</option>
                            <option value="found">Found (Stock Count Correction)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Quantity</label>
                        <input type="number" name="quantity" class="form-control rounded-pill" 
                               min="1" max="{{ $item->inv_quantity_available }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Remarks (Optional)</label>
                        <textarea name="remarks" class="form-control" rows="2" 
                                  placeholder="Add notes about this adjustment..."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning text-white rounded-pill fw-bold">
                        <i class="fa-solid fa-check me-1"></i>Save Adjustment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>