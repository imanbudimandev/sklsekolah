@if ($paginator->hasPages())
    <div class="pagination-info text-muted" style="font-size: 0.9rem; color: #64748b;">
        Menampilkan {{ $paginator->firstItem() ?? 0 }} - {{ $paginator->lastItem() ?? 0 }} dari {{ $paginator->total() }} data
    </div>
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="disabled" style="opacity: 0.5; pointer-events: none;">
                <span class="btn btn-secondary btn-sm"><i class="fa-solid fa-chevron-left"></i> Sebelum</span>
            </li>
        @else
            <li>
                <a class="btn btn-secondary btn-sm" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="fa-solid fa-chevron-left"></i> Sebelum</a>
            </li>
        @endif

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li>
                <a class="btn btn-secondary btn-sm" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya <i class="fa-solid fa-chevron-right"></i></a>
            </li>
        @else
            <li class="disabled" style="opacity: 0.5; pointer-events: none;">
                <span class="btn btn-secondary btn-sm">Berikutnya <i class="fa-solid fa-chevron-right"></i></span>
            </li>
        @endif
    </ul>
@endif
