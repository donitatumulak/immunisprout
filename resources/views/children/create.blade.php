<div class="modal fade" id="registerChildModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-success text-white py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-child-reaching me-2"></i>Register New Child</h5>
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

                <form action="{{ route('children.store') }}" method="POST">
                    @csrf
                    <p class="text-success fw-bold border-bottom pb-1 mb-3">Child's Personal Details</p>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">First Name</label>
                            <input type="text" name="chd_first_name" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Middle Name (Optional)</label>
                            <input type="text" name="chd_middle_name" class="form-control border-2 rounded-pill">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Last Name</label>
                            <input type="text" name="chd_last_name" class="form-control border-2 rounded-pill" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Date of Birth</label>
                            <input type="date" name="chd_date_of_birth" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Sex</label>
                            <select name="chd_sex" class="form-select border-2 rounded-pill" required>
                                <option value="" selected disabled>Select Sex</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label small fw-bold">Residency Status</label>
                            <select name="chd_residency_status" class="form-select border-2 rounded-pill" required>
                                <option value="established resident">Established Resident</option>
                                <option value="transitional resident">Transitional Resident</option>
                            </select>
                        </div>
                    </div>

                    <p class="text-success fw-bold border-bottom pb-1 mt-3 mb-3">Guardian Information</p>

                    <div class="form-check form-switch mb-3 ps-5">
                        <input class="form-check-input" type="checkbox" id="toggleNewGuardian" name="new_guardian" value="1">
                        <label class="form-check-label fw-bold text-success" for="toggleNewGuardian">Registering a new Guardian?</label>
                    </div>

                    <div id="existingGuardianSection">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Select Guardian</label>
                            <select name="chd_guardian_id" id="guardianSelect" class="form-select border-2 rounded-pill">
                                <option value="" selected disabled>Choose from records...</option>
                                @foreach($guardians as $guardian)
                                    <option value="{{ $guardian->grd_id }}">{{ $guardian->grd_first_name }} {{ $guardian->grd_last_name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text extra-small ms-2">Address will be inherited from the guardian profile.</div>
                        </div>
                    </div>

                    <div id="newGuardianFields" style="display: none;" class="bg-light p-3 rounded-4 border">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">First Name</label>
                                <input type="text" name="grd_first_name" class="form-control border-2 rounded-pill">
                            </div>
                              <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">Middle Name</label>
                                <input type="text" name="grd_middle_name" class="form-control border-2 rounded-pill">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label small fw-bold">Last Name</label>
                                <input type="text" name="grd_last_name" class="form-control border-2 rounded-pill">
                            </div>
                        </div>

                       <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Contact Number</label>
                                <input type="text" name="grd_contact_number" class="form-control border-2 rounded-pill" placeholder="e.g. 09123456789">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold">Relationship to Child</label>
                                <select name="grd_relationship" class="form-select border-2 rounded-pill">
                                    <option value="" selected disabled>Select Relationship</option>
                                    <option value="mother">Mother</option>
                                    <option value="father">Father</option>
                                    <option value="legal guardian">Legal Guardian</option>
                                    <option value="grandparent">Grandparent</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <p class="small fw-bold text-success border-bottom mt-3">Guardian Current Address</p>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="extra-small fw-bold">House/Street </label>
                                <input type="text" name="addr_line_1" class="form-control form-control-sm rounded-pill border-2">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="extra-small fw-bold">Unit/Bldg (optional)</label>
                                <input type="text" name="addr_line_2" class="form-control form-control-sm rounded-pill border-2">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="extra-small fw-bold">Barangay</label>
                                <input type="text" name="addr_barangay" class="form-control form-control-sm rounded-pill border-2">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="extra-small fw-bold">City/Municipality</label>
                                <input type="text" name="addr_city" class="form-control form-control-sm rounded-pill border-2">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="extra-small fw-bold">Province</label>
                                <input type="text" name="addr_province" class="form-control form-control-sm rounded-pill border-2">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="extra-small fw-bold">Zip Code (optional)</label>
                                <input type="text" name="addr_zip_code" class="form-control form-control-sm rounded-pill border-2">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm">REGISTER CHILD</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleNewGrd = document.getElementById('toggleNewGuardian');
            const existingSection = document.getElementById('existingGuardianSection');
            const newFieldsSection = document.getElementById('newGuardianFields');
            const guardianSelect = document.getElementById('guardianSelect');
            
            // Get all input fields in the new guardian section
            const newGuardianInputs = newFieldsSection.querySelectorAll('input, select');

            toggleNewGrd.addEventListener('change', function() {
                if (this.checked) {
                    newFieldsSection.style.display = 'block';
                    existingSection.style.display = 'none';
                    guardianSelect.removeAttribute('required');
                    guardianSelect.disabled = true;
                    
                    // Enable new guardian fields
                    newGuardianInputs.forEach(input => {
                        input.disabled = false;
                    });
                } else {
                    newFieldsSection.style.display = 'none';
                    existingSection.style.display = 'block';
                    guardianSelect.setAttribute('required', 'required');
                    guardianSelect.disabled = false;
                    
                    // Disable new guardian fields to prevent them from being submitted
                    newGuardianInputs.forEach(input => {
                        input.disabled = true;
                    });
                }
            });
            
            // Initialize - disable new guardian fields on page load
            newGuardianInputs.forEach(input => {
                input.disabled = true;
            });
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