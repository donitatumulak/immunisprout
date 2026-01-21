<div class="modal fade" id="deleteChildModal{{ $child->chd_id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body p-4 text-center">
                
                @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="fa-solid fa-circle-exclamation me-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="text-danger mb-3">
                    <i class="fa-solid fa-circle-exclamation fa-4x"></i>
                </div>
                
                <h5 class="fw-bold">Delete Record?</h5>
                <p class="text-muted small">
                    Are you sure you want to delete the record for <br>
                    <strong class="text-dark">{{ $child->chd_first_name }} {{ $child->chd_last_name }}</strong>? 
                    This action cannot be undone.
                </p>

                <form action="{{ route('children.destroy', $child->chd_id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger rounded-pill fw-bold">YES, DELETE</button>
                        <button type="button" class="btn btn-light rounded-pill fw-bold text-muted" data-bs-dismiss="modal">CANCEL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Replace 'addChildModal' with your actual Modal ID
            var myModal = new bootstrap.Modal(document.getElementById('addChildModal'));
            myModal.show();
        });
    </script>
@endif