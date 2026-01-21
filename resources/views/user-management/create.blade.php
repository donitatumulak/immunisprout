<div class="modal fade" id="createPersonnelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-success text-white py-3" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-house-chimney-medical"></i> Register New Health Personnel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                <form action="{{ route('user-management.store') }}" method="POST">
                    @csrf
                    
                    {{-- Personal Details --}}
                    <h6 class="text-success fw-bold border-bottom pb-1 mb-3">Personal Details</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="small fw-bold">First Name</label>
                            <input type="text" name="wrk_first_name" class="form-control border-2 rounded-pill @error('wrk_first_name') is-invalid @enderror" value="{{ old('wrk_first_name') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">Middle Name (optional)</label>
                            <input type="text" name="wrk_middle_name" class="form-control border-2 rounded-pill" value="{{ old('wrk_middle_name') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">Last Name</label>
                            <input type="text" name="wrk_last_name" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Contact Number</label>
                            <input type="text" name="wrk_contact_number" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Assigned Work Role</label>
                            <select name="wrk_role" class="form-select border-2 rounded-pill" required>
                                <option value="">Choose...</option>
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
                            <input type="text" name="addr_line_1" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Unit/Bldg (optional)</label>
                            <input type="text" name="addr_line_2" class="form-control border-2 rounded-pill">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Barangay</label>
                            <input type="text" name="addr_barangay" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">City/Municipality</label>
                            <input type="text" name="addr_city_municipality" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Province</label>
                            <input type="text" name="addr_province" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Zip Code (optional)</label>
                            <input type="text" name="addr_zip_code" class="form-control border-2 rounded-pill">
                        </div>
                    </div>

                    {{-- Account Credentials --}}
                    <h6 class="text-success fw-bold border-bottom pb-1 mb-3">Account Credentials</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="small fw-bold">Username</label>
                            <input type="text" name="username" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control border-2 rounded-pill" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small fw-bold">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control border-2 rounded-pill" required>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-success w-100 py-2 rounded-pill fw-bold shadow-sm">COMPLETE REGISTRATION</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>