<!-- {{-- 
    REUSABLE PAGINATION COMPONENT
    
    CARA PAKAI:
    @include('partial.pagination', ['data' => $transaksi])
    
    CATATAN:
    - $data harus berupa object LengthAwarePaginator (hasil dari ->paginate())
    - Pastikan pagination.css & pagination.js sudah di-include di layout utama
--}}

@if($data->hasPages())
    <div class="pagination-container">
        {{-- Info Text --}}
        <div class="pagination-info">
            Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} 
            dari {{ $data->total() }} data
        </div>

        {{-- Pagination Buttons --}}
        <div class="pagination-wrapper">
            {{-- Previous Button --}}
            @if ($data->onFirstPage())
                <span class="disabled">
                    <i class='bx bx-chevron-left'></i>
                </span>
            @else
                <a href="{{ $data->previousPageUrl() }}">
                    <i class='bx bx-chevron-left'></i>
                </a>
            @endif

            {{-- Smart Page Numbers (dengan dots) --}}
            @php
                $currentPage = $data->currentPage();
                $lastPage = $data->lastPage();
                $start = max(1, $currentPage - 2);
                $end = min($lastPage, $currentPage + 2);
            @endphp

            {{-- First Page --}}
            @if($start > 1)
                <a href="{{ $data->url(1) }}">1</a>
                @if($start > 2)
                    <span class="dots">...</span>
                @endif
            @endif

            {{-- Middle Pages --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $currentPage)
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $data->url($page) }}">{{ $page }}</a>
                @endif
            @endfor

            {{-- Last Page --}}
            @if($end < $lastPage)
                @if($end < $lastPage - 1)
                    <span class="dots">...</span>
                @endif
                <a href="{{ $data->url($lastPage) }}">{{ $lastPage }}</a>
            @endif

            {{-- Next Button --}}
            @if ($data->hasMorePages())
                <a href="{{ $data->nextPageUrl() }}">
                    <i class='bx bx-chevron-right'></i>
                </a>
            @else
                <span class="disabled">
                    <i class='bx bx-chevron-right'></i>
                </span>
            @endif
        </div>
    </div>
@endif -->