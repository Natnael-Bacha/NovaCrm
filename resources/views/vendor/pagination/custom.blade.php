<style>
/* Custom Pagination */
.pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.15rem;
    margin: 2rem 0 2rem 0;
    padding: 0;
    list-style: none;
}

.pagination li {
    display: inline-flex;
    margin: 0;
    padding: 0;
}

/* Page numbers and navigation icons */
.pagination li a,
.pagination li span {
    display: inline-flex;
    align-items: center;
    justify-content: center;

    width: 34px;
    height: 34px;

    border-radius: 0.4rem;

    color: #64748b;
    background: transparent;

    font-size: 0.875rem;
    font-weight: 500;

    text-decoration: none;

    transition:
        background-color 0.15s ease,
        color 0.15s ease;
}

/* Page hover */
.pagination li a:hover {
    background: #f1f5f9;
    color: #334155;
}

/* Active page */
.pagination li.active span {
    background: #e5e7eb;
    color: #374151;
    font-weight: 600;
}

/* Previous / Next icons */
.pagination li a[aria-label="Previous"],
.pagination li a[aria-label="Next"],
.pagination li.disabled span[aria-label="Previous"],
.pagination li.disabled span[aria-label="Next"] {
    width: 34px;
    height: 34px;
}

/* Icon size */
.pagination svg {
    width: 18px;
    height: 18px;
}

/* Disabled icons */
.pagination li.disabled span {
    color: #cbd5e1;
    cursor: default;
    background: transparent;
}

/* Ellipsis */
.pagination li.disabled .dots {
    width: 30px;
    color: #94a3b8;
    background: transparent;
}
</style>    

@if ($paginator->hasPages())
    <ul class="pagination">

        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <li class="disabled">
                <span aria-label="Previous">
                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M12.5 15L7.5 10L12.5 5"
                              stroke="currentColor"
                              stroke-width="1.8"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                </span>
            </li>
        @else
            <li>
                <a href="{{ $paginator->previousPageUrl() }}"
                   rel="prev"
                   aria-label="Previous">
                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M12.5 15L7.5 10L12.5 5"
                              stroke="currentColor"
                              stroke-width="1.8"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                </a>
            </li>
        @endif

        {{-- Page Numbers --}}
        @foreach ($elements as $element)

            @if (is_string($element))
                <li class="disabled">
                    <span class="dots">{{ $element }}</span>
                </li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active">
                            <span>{{ $page }}</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $url }}">
                                {{ $page }}
                            </a>
                        </li>
                    @endif
                @endforeach
            @endif

        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}"
                   rel="next"
                   aria-label="Next">
                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M7.5 5L12.5 10L7.5 15"
                              stroke="currentColor"
                              stroke-width="1.8"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                </a>
            </li>
        @else
            <li class="disabled">
                <span aria-label="Next">
                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M7.5 5L12.5 10L7.5 15"
                              stroke="currentColor"
                              stroke-width="1.8"
                              stroke-linecap="round"
                              stroke-linejoin="round"/>
                    </svg>
                </span>
            </li>
        @endif

    </ul>
@endif