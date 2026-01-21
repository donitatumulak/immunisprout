@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-0 shadow-sm border-0 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-circle-check me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
<div class="container-fluid px-4 mt-3">
    <div class="alert alert-access-denied alert-dismissible fade show d-flex align-items-center" role="alert">
        <div class="alert-icon-container me-3">
            <i class="fa-solid fa-shield-halved fa-lg"></i>
        </div>
        <div>
            <strong class="d-block">Security Restriction</strong>
            <span class="small">{{ session('error') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
@endif

@if($errors->any())
    <div class="alert alert-warning alert-dismissible fade show rounded-0 shadow-sm border-0 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif