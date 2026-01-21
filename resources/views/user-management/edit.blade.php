<div class="modal fade" id="editPersonnelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-success text-white py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-user-pen me-2"></i>Update Profile
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form id="editUserForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body p-4">
                    {{-- Personal Details --}}
                    <h6 class="text-success fw-bold border-bottom pb-1 mb-3">Personal Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="small fw-bold">First Name</label>
                            <input type="text" name="wrk_first_name" id="edit_first_name" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">Middle Name (optional)</label>
                            <input type="text" name="wrk_middle_name" id="edit_middle_name" class="form-control border-2 rounded-pill">
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">Last Name</label>
                            <input type="text" name="wrk_last_name" id="edit_last_name" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Contact Number</label>
                            <input type="text" name="wrk_contact_number" id="edit_contact_number" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Assigned Work Role</label>
                            <select name="wrk_role" id="edit_role" class="form-select border-2 rounded-pill" required>
                                <option value="admin">Admin</option>
                                <option value="nurse">Nurse</option>
                                <option value="midwife">Midwife</option>
                                <option value="bhw">BHW</option>
                            </select>
                        </div>
                    </div>

                    {{-- Address Information --}}
                    <h6 class="text-success fw-bold border-bottom pb-1 mb-3">Address Information</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="small fw-bold">Street/House</label>
                            <input type="text" name="addr_line_1" id="edit_addr_line_1" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Unit/Bldg (optional)</label>
                            <input type="text" name="addr_line_2" id="edit_addr_line_2" class="form-control border-2 rounded-pill">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Barangay</label>
                            <input type="text" name="addr_barangay" id="edit_barangay" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">City/Municipality</label>
                            <input type="text" name="addr_city_municipality" id="edit_city_municipality" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Province</label>
                            <input type="text" name="addr_province" id="edit_province" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Zip Code (optional)</label>
                            <input type="text" name="addr_zip_code" id="edit_zip_code" class="form-control border-2 rounded-pill">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm">SAVE CHANGES</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openEditModal(user, worker, address) {
        const form = document.getElementById('editUserForm');
        form.action = `/user-management/${user.id}`;
        
        document.getElementById('edit_first_name').value = worker.wrk_first_name;
        document.getElementById('edit_middle_name').value = worker.wrk_middle_name;
        document.getElementById('edit_last_name').value = worker.wrk_last_name;
        document.getElementById('edit_contact_number').value = worker.wrk_contact_number;
        document.getElementById('edit_role').value = worker.wrk_role;

        if (address) {
            document.getElementById('edit_addr_line_1').value = address.addr_line_1;
            document.getElementById('edit_addr_line_2').value = address.addr_line_2;
            document.getElementById('edit_barangay').value = address.addr_barangay;
            document.getElementById('edit_city_municipality').value = address.addr_city_municipality;
            document.getElementById('edit_province').value = address.addr_province;
            document.getElementById('edit_zip_code').value = address.addr_zip_code;
        }
        

        new bootstrap.Modal(document.getElementById('editPersonnelModal')).show();
    }
</script>