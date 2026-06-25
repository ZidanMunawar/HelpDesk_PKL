{{-- resources/views/activity-logs/partials/pagination.blade.php --}}
@if ($logs->hasPages())
    <ul class="pagination">
        {{-- Previous --}}
        @if ($logs->onFirstPage())
            <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
        @else
            <li class="page-item"><a class="page-link ajax-page" href="javascript:void(0)"
                    data-page="{{ $logs->currentPage() - 1 }}"><i class="fas fa-chevron-left"></i></a></li>
        @endif

        {{-- Numbers --}}
        @php
            $start = max(1, $logs->currentPage() - 2);
            $end = min($logs->lastPage(), $logs->currentPage() + 2);
        @endphp

        @if ($start > 1)
            <li class="page-item"><a class="page-link ajax-page" href="javascript:void(0)" data-page="1">1</a></li>
            @if ($start > 2)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif
        @endif

        @for ($i = $start; $i <= $end; $i++)
            <li class="page-item {{ $logs->currentPage() == $i ? 'active' : '' }}">
                <a class="page-link ajax-page" href="javascript:void(0)"
                    data-page="{{ $i }}">{{ $i }}</a>
            </li>
        @endfor

        @if ($end < $logs->lastPage())
            @if ($end < $logs->lastPage() - 1)
                <li class="page-item disabled"><span class="page-link">...</span></li>
            @endif
            <li class="page-item"><a class="page-link ajax-page" href="javascript:void(0)"
                    data-page="{{ $logs->lastPage() }}">{{ $logs->lastPage() }}</a></li>
        @endif

        {{-- Next --}}
        @if ($logs->hasMorePages())
            <li class="page-item"><a class="page-link ajax-page" href="javascript:void(0)"
                    data-page="{{ $logs->currentPage() + 1 }}"><i class="fas fa-chevron-right"></i></a></li>
        @else
            <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>
        @endif
    </ul>
@endif
