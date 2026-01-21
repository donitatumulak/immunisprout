<div class="modal fade" id="editChildModal{{ $child->chd_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-success text-white py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-pen me-2"></i>Update Child Information</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">

                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="fa-solid fa-circle-exclamation me-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('children.update', $child->chd_id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <p class="text-success fw-bold border-bottom pb-1 mb-3">Personal Details</p>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">First Name</label>
                            <input type="text" name="chd_first_name" class="form-control border-2 rounded-pill" value="{{ $child->chd_first_name }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Middle Name</label>
                            <input type="text" name="chd_middle_name" class="form-control border-2 rounded-pill" value="{{ $child->chd_middle_name }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Last Name</label>
                            <input type="text" name="chd_last_name" class="form-control border-2 rounded-pill" value="{{ $child->chd_last_name }}">
                        </div>
                    </div>

                    <div class="row">
                         <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Sex</label>
                            <select name="chd_sex" class="form-select border-2 rounded-pill">
                                <option value="male" {{ $child->chd_sex == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ $child->chd_sex == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Date of Birth</label>
                            <input type="date" name="chd_date_of_birth" class="form-control border-2 rounded-pill" value="{{ $child->chd_date_of_birth }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Residency Status</label>
                            <select name="chd_residency_status" class="form-select border-2 rounded-pill">
                                <option value="established resident" {{ $child->chd_residency_status == 'established resident' ? 'selected' : '' }}>Established Resident</option>
                                <option value="transient resident" {{ $child->chd_residency_status == 'transient resident' ? 'selected' : '' }}>Transient Resident</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label small fw-bold">Record Status</label>
                            <select name="chd_status" class="form-select border-2 rounded-pill">
                                <option value="active" {{ $child->chd_status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="archived" {{ $child->chd_status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="completed" {{ $child->chd_status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="transferred" {{ $child->chd_status == 'transferred' ? 'selected' : '' }}>Transferred</option>
                            </select>
                        </div>
                    </div>

                    <p class="text-success fw-bold border-bottom pb-1 mt-3 mb-3">Guardian Information</p>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">First Name</label>
                                <input type="text" name="grd_first_name" class="form-control border-2 rounded-pill" 
                                    value="{{ $child->guardian->grd_first_name }}" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">Middle Name</label>
                                <input type="text" name="grd_middle_name" class="form-control border-2 rounded-pill" 
                                    value="{{ $child->guardian->grd_middle_name }}">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">Last Name</label>
                                <input type="text" name="grd_last_name" class="form-control border-2 rounded-pill" 
                                    value="{{ $child->guardian->grd_last_name }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Contact Number</label>
                                <input type="text" name="grd_contact_number" class="form-control border-2 rounded-pill" 
                                    value="{{ $child->guardian->grd_contact_number }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Relationship to Child</label>
                                <select name="grd_relationship" class="form-select border-2 rounded-pill">
                                    <option value="" disabled>Select Relationship</option>
                                    
                                    @php
                                        $relationships = ['Mother', 'Father', 'Grandparent', 'Other'];
                                    @endphp

                                    @foreach($relationships as $rel)
                                        <option value="{{ $rel }}" 
                                            {{ (old('grd_relationship', $child->guardian->grd_relationship) == $rel) ? 'selected' : '' }}>
                                            {{ $rel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <p class="text-success fw-bold border-bottom pb-1 mt-3 mb-3">Residential Address</p>
                            <div class="row">
                                <input type="hidden" name="addr_id" value="{{ $child->address->addr_id }}">

                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">Street / House No.</label>
                                    <input type="text" name="addr_line_1" class="form-control border-2 rounded-pill" 
                                        value="{{ $child->address->addr_line_1 }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">Apartment / Unit No.</label>
                                    <input type="text" name="addr_line_2" class="form-control border-2 rounded-pill" 
                                        value="{{ $child->address->addr_line_2 }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">Barangay</label>
                                    <input type="text" name="addr_barangay" class="form-control border-2 rounded-pill" 
                                        value="{{ $child->address->addr_barangay }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">City</label>
                                    <input type="text" name="addr_city_municipality" class="form-control border-2 rounded-pill" 
                                        value="{{ $child->address->addr_city_municipality }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">Province</label>
                                    <input type="text" name="addr_province" class="form-control border-2 rounded-pill" 
                                        value="{{ $child->address->addr_province }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">Zip Code</label>
                                    <input type="text" name="addr_zip_code" class="form-control border-2 rounded-pill" 
                                        value="{{ $child->address->addr_zip_code }}">
                                </div>
                            </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm">UPDATE RECORD</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // JS for this specific modal instance
    document.getElementById('editToggleNewAddr{{ $child->chd_id }}').addEventListener('change', function() {
        const existingSection = document.getElementById('editExistingAddrSection{{ $child->chd_id }}');
        const newFields = document.getElementById('editNewAddrFields{{ $child->chd_id }}');
        const selectEl = document.getElementById('editAddrSelect{{ $child->chd_id }}');

        if (this.checked) {
            existingSection.style.display = 'none';
            newFields.style.display = 'block';
            selectEl.removeAttribute('required');
        } else {
            existingSection.style.display = 'block';
            newFields.style.display = 'none';
            selectEl.setAttribute('required', 'required');
        }
    });
</script>

   @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Replace 'addChildModal' with your actual Modal ID
            var myModal = new bootstrap.Modal(document.getElementById('addChildModal'));
            myModal.show();
        });
    </script>
    @endif
</div>