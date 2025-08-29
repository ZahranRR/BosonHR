@if ($paginator->hasPages())
    <ul class="pagination">
        {{-- Previous Page Link --}}
        <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
            <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="@lang('pagination.previous')">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="disabled"><span class="page-link">{{ $element }}</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                    </li>
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
            <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="@lang('pagination.next')">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
@endif
