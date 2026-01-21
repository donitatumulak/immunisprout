{{-- MODAL: CONFIRM DELETE BATCH --}}
<div class="modal fade" id="deleteInventoryModal{{ $item->inv_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body p-4 text-center">
                <div class="text-danger mb-3">
                    <i class="fa-solid fa-triangle-exclamation fa-4x"></i>
                </div>
                
                <h5 class="fw-bold">Delete Batch?</h5>
                <p class="text-muted small">
                    Are you sure you want to delete Batch:<br>
                    <strong class="text-dark">{{ $item->inv_batch_number }}</strong> 
                    ({{ $item->vaccine->vacc_vaccine_name ?? 'Unknown Vaccine' }})?
                    <br>
                    <span class="text-danger">This will also remove all logs.</span>
                </p>

                <form action="{{ route('inventory.destroy', $item->inv_id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger rounded-pill fw-bold">YES, DELETE</button>
                        <button type="button" class="btn btn-light rounded-pill fw-bold text-muted" data-bs-dismiss="modal">CANCEL</button>
                    </div>
                </form>
            </div> {{-- End Modal Body --}}
        </div> {{-- End Modal Content --}}
    </div> {{-- End Modal Dialog --}}
</div> {{-- End Modal Fade --}}