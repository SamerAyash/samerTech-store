
@if ($paginator->hasPages())
<!--begin::Pagination-->
<div class="d-flex justify-content-between align-items-center flex-wrap">
    <div class="d-flex flex-wrap py-2 mr-3">
        @if ($paginator->onFirstPage())
            <a href="#" class="btn btn-icon btn-circle btn-sm btn-light mr-2 my-1 disabled"  aria-label="@lang('pagination.previous')">
                <i class="ki ki-bold-arrow-back icon-xs"></i>
            </a>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-icon btn-circle btn-sm btn-light mr-2 my-1"  aria-label="@lang('pagination.previous')">
                <i class="ki ki-bold-arrow-back icon-xs"></i>
            </a>

        @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <a href="#" class="btn btn-icon btn-circle btn-sm border-0 btn-light mr-2 my-1 disabled" aria-disabled="true">{{ $element }}</a>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <a href="{{ $url }}" class="btn btn-icon btn-circle btn-sm border-0 btn-light btn-hover-primary active mr-2 my-1">{{ $page }}</a>

                        @else
                            <a href="{{ $url }}" class="btn btn-icon btn-circle btn-sm border-0 btn-light mr-2 my-1">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-icon btn-circle btn-sm btn-light mr-2 my-1"><i class="ki ki-bold-arrow-next icon-xs"></i></a>
            @else

                <a href="#" class="btn btn-icon btn-circle btn-sm btn-light mr-2 my-1 disabled">
                    <i class="ki ki-bold-arrow-next icon-xs"></i>
                </a>
            @endif


    </div>
    <div class="d-flex align-items-center py-3">
        <span class="text-muted">Display {{count($paginator->items())}} from {{$paginator->total()}} element </span>
    </div>
</div>
<!--end:: Pagination-->
@endif
