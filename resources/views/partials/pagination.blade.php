<style>
    /* --- PAGINATION --- */
    .pagination .page-link {
        border-radius: 10px; /* Slightly rounder */
        margin: 0 4px;
        color: var(--green);
        border: 1px solid #f1f5f9; 
        background: var(--white);
        font-weight: 600;
        transition: all 0.2s ease;
        padding: 10px 16px;
    }

    .pagination .page-link:hover {
        background-color: var(--green);
        color: white;
        transform: translateY(-2px);
    }

    .pagination .page-item.active .page-link {
        background: var(--green);
        color: white;
        border-color: var(--green);
        box-shadow: 0 4px 10px rgba(45, 106, 79, 0.2) !important;
    }

    .pagination .page-item.disabled .page-link {
        opacity: 0.5;
        background: #f8fafc;
    }
</style>
@if ($paginator->hasPages())
    <nav class="mt-5">
        <ul class="pagination justify-content-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled"><span class="page-link shadow-sm"><i class="fa-solid fa-chevron-left"></i></span></li>
            @else
                <li class="page-item"><a class="page-link shadow-sm" href="{{ $paginator->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a></li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active"><span class="page-link shadow-sm">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link shadow-sm" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item"><a class="page-link shadow-sm" href="{{ $paginator->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a></li>
            @else
                <li class="page-item disabled"><span class="page-link shadow-sm"><i class="fa-solid fa-chevron-right"></i></span></li>
            @endif
        </ul>
    </nav>
@endif