<div class="modal fade" id="deletePersonnelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content" style="border-radius: 20px;">
            <form id="deleteUserForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body p-5 text-center">
                    <div class="text-danger mb-4"><i class="fa-solid fa-triangle-exclamation fa-4x"></i></div>
                    <h4 class="fw-bold">Delete Account?</h4>
                    <p class="text-muted">You are about to delete <strong id="deleteTargetName"></strong>. This action will remove the user account, health worker profile, and address records.</p>
                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Yes, Delete All</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openDeleteModal(userId, fullName) {
        document.getElementById('deleteUserForm').action = `/user-management/${userId}`;
        document.getElementById('deleteTargetName').textContent = fullName;
        new bootstrap.Modal(document.getElementById('deletePersonnelModal')).show();
    }
</script>