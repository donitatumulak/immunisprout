<div class="modal fade" id="passwordModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header bg-warning py-3" style="border-radius: 20px 20px 0 0;">
            <h5 class="modal-title fw-bold"><i class="fa-solid fa-user-gear me-2"></i>Account Settings</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="passwordResetForm" method="POST">
                @csrf
                @method('PUT') <div class="modal-body p-4">
                    <p>Updating account for <strong id="pw_target_name"></strong>.</p>
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">USERNAME</label>
                        <input type="text" name="username" id="pw_username" class="form-control rounded-pill" required>
                    </div>

                    <hr class="my-4">
                    <div class="alert alert-primary border-0 shadow-sm d-flex align-items-center mb-3">
                        <i class="fa-solid fa-circle-info me-3 fs-4"></i>
                        <div>
                            <p class="mb-0 small fw-bold">Note:</p>
                            <p class="mb-0 small">Leave the password fields blank if you do not want to change the current password.</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">NEW PASSWORD</label>
                        <input type="password" name="password" class="form-control rounded-pill" minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">CONFIRM NEW PASSWORD</label>
                        <input type="password" name="password_confirmation" class="form-control rounded-pill">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold w-100">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPasswordModal(userId, firstName, currentUsername) {
    const form = document.getElementById('passwordResetForm');
    form.action = `/user-management/${userId}/password`;
    
    document.getElementById('pw_target_name').textContent = firstName;
    document.getElementById('pw_username').value = currentUsername; 
    
    new bootstrap.Modal(document.getElementById('passwordModal')).show();
}
</script>
