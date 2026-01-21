<div class="modal fade" id="viewPersonnelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-success text-white py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-address-card me-2"></i>Personnel Profile</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar-lg bg-light rounded-circle d-inline-flex align-items-center justify-content-center border" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-user-doctor fa-2x text-success"></i>
                    </div>
                    <h4 class="mt-2 mb-2 fw-bold text-capitalize" id="view_full_name"></h4>
                    <span class="badge bg-success rounded-pill text-capitalize" id="view_role"></span>
                </div>

                <div class="row g-3">
                    <p class="text-success fw-bold border-bottom pb-1 mb-3">Personal Details</p>
                    <div class="col-6">
                        <label class="small text-muted d-block">Username</label>
                        <strong id="view_username"></strong>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted d-block">Contact</label>
                        <strong id="view_contact"></strong>
                    </div>
                    <hr class="my-2">
                    <div class="col-12">
                        <label class="small text-muted d-block">Full Address</label>
                        <p class="mb-0" id="view_address"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openViewModal(worker, address, username) {
        document.getElementById('view_full_name').textContent = `${worker.wrk_first_name} ${worker.wrk_middle_name ? worker.wrk_middle_name + ' ' : ''}${worker.wrk_last_name}`;
        document.getElementById('view_role').textContent = worker.wrk_role;
        document.getElementById('view_username').textContent = username;
        document.getElementById('view_contact').textContent = worker.wrk_contact_number;
        document.getElementById('view_address').textContent = `${address.addr_line_1}, Brgy. ${address.addr_barangay}, ${address.addr_city_municipality}, ${address.addr_province} ${address.addr_zip_code}`;
        
        new bootstrap.Modal(document.getElementById('viewPersonnelModal')).show();
    }
</script>