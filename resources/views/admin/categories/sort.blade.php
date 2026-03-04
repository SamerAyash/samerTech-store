@extends('admin.layout.app')
@push('style')
    <link rel="stylesheet" href="{{ asset('cp_assets/plugins/custom/prismjs/prismjs.bundle.css') }}">
@endpush
@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-2">
                <h5 class="text-dark font-weight-bold my-1 mr-5">Sort Parent Categories</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.categories.index') }}" class="text-muted">Categories</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">Sort</span>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.categories.sortChildren') }}" class="btn btn-primary font-weight-bold mr-2">
                    <i class="flaticon2-list"></i> Sort Children
                </a>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-light font-weight-bold">
                    <i class="flaticon2-left-arrow-1"></i> 
                    <span class="d-none d-sm-inline">Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container">
            @includeIf('admin.component.alert')
            
            <div class="card card-custom">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="card-label">Sort Parent Categories</h3>
                        <span class="text-muted d-block mt-1">Drag and drop to reorder parent categories only.</span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if($categories->isEmpty())
                        <div class="text-center py-5">
                            <i class="flaticon2-file text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">No Parent Categories Found</h4>
                            <p class="text-muted">Get started by creating your first category.</p>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mt-3">
                                <i class="flaticon2-plus"></i> Add Category
                            </a>
                        </div>
                    @else
                        <ul id="kt_draggable_category" class="list-group list-group-flush">
                            @foreach ($categories as $category)
                                <li class="list-group-item card-bordered my-2 bg-gray-100 d-flex justify-content-between align-items-center" data-id="{{ $category->id }}">
                                    <div class="d-flex align-items-center">
                                        <span class="mr-3 text-muted" style="cursor: grab;">
                                            <i class="flaticon-grid-menu"></i>
                                        </span>
                                        <span class="font-weight-bold">{{ optional($category->translate('en'))->name ?? $category->name }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        @if($category->status)
                                            <span class="badge badge-success mr-2">Active</span>
                                        @else
                                            <span class="badge badge-danger mr-2">Inactive</span>
                                        @endif
                                        @if($category->children->count() > 0)
                                            <span class="badge badge-light-primary">{{ $category->children->count() }} children</span>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Order Button -->
<button id="save-order-btn" class="btn btn-success position-fixed bottom-0 right-0 m-4 shadow-lg d-none">
    <i class="flaticon2-check-mark"></i> Save Order
</button>
@endsection

@push('scripts')
<script src="{{ asset('cp_assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
<script src="{{ asset('cp_assets/plugins/custom/draggable/draggable.bundle.js') }}"></script>
<script>
    "use strict";
    var KTDraggableCards = {
        init: function() {
            var containers = document.querySelectorAll("#kt_draggable_category");
            if (containers.length === 0) return false;
            
            // Check if Sortable is available
            if (typeof Sortable === 'undefined' || !Sortable.default) {
                console.error('Sortable library not loaded correctly');
                return false;
            }
            
            var sortableInstance = new Sortable.default(containers, {
                draggable: ".list-group-item",
                handle: ".list-group-item .flaticon-grid-menu",
                mirror: {
                    appendTo: "body",
                    constrainDimensions: true
                }
            });
            
            // Listen for sort events
            if (sortableInstance && typeof sortableInstance.on === 'function') {
                sortableInstance.on('sortable:sorted', function(event) {
                    window.hasChanges = true;
                    if (window.updateSaveButton) {
                        window.updateSaveButton();
                    }
                });
            }
            
            return true;
        }
    };

document.addEventListener("DOMContentLoaded", function () {
    // Wait for Sortable to be available
    function initSortable() {
        if (typeof Sortable === 'undefined' || !Sortable.default) {
            setTimeout(initSortable, 100);
            return;
        }
        
        KTDraggableCards.init();
        
        const container = document.querySelector("#kt_draggable_category");
        const saveBtn = document.getElementById('save-order-btn');
        
        if (!container || !saveBtn) return;
        
        // Helper: toggle save button
        let hasChanges = false;
        
        window.updateSaveButton = function() {
            if (!saveBtn) return;
            if (hasChanges) {
                saveBtn.classList.remove('d-none');
            } else {
                saveBtn.classList.add('d-none');
            }
        };
        
        // Save order function
        async function saveOrder() {
            const items = [];
            container.querySelectorAll(".list-group-item").forEach((el, idx) => {
                const categoryId = parseInt(el.getAttribute('data-id'));
                if (!isNaN(categoryId)) {
                    items.push({
                        id: categoryId,
                        parent_id: null,
                        sort_order: idx
                    });
                }
            });

            if (!items.length) {
                alert('No items to save.');
                return;
            }

            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="flaticon2-loading"></i> Saving...';
            }

            try {
                const res = await fetch('{{ route("admin.categories.updateOrder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ items: items })
                });

                if (!res.ok) {
                    const err = await res.json().catch(() => ({ message: 'Server error' }));
                    throw new Error(err.message || 'Server error');
                }

                const data = await res.json();
                if (!data.success) {
                    throw new Error(data.message || 'Failed to save');
                }

                hasChanges = false;
                window.updateSaveButton();

                alert(data.message || 'Category order updated successfully.');
                window.location.reload();
            } catch (e) {
                console.error('Save order error:', e);
                alert(e.message || 'Failed to save order.');
            } finally {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="flaticon2-check-mark"></i> Save Order';
                }
            }
        }
        
        saveBtn.addEventListener('click', saveOrder);

        // Monitor DOM changes to detect reordering
        const obs = new MutationObserver(function(mutationsList) {
            for (const m of mutationsList) {
                if (m.type === 'childList' && (m.addedNodes.length || m.removedNodes.length)) {
                    hasChanges = true;
                    window.updateSaveButton();
                    return;
                }
            }
        });

        obs.observe(container, { childList: true, subtree: false });

        // Also check for order changes on mouse/touch events
        let previousOrder = Array.from(container.querySelectorAll('.list-group-item')).map(el => el.dataset.id).join(',');
        function checkOrderChange() {
            const current = Array.from(container.querySelectorAll('.list-group-item')).map(el => el.dataset.id).join(',');
            if (current !== previousOrder) {
                previousOrder = current;
                hasChanges = true;
                window.updateSaveButton();
            }
        }
        
        ['mouseup', 'touchend', 'pointerup'].forEach(evt => {
            document.addEventListener(evt, function() {
                setTimeout(checkOrderChange, 50);
            }, { passive: true });
        });
        
        window.updateSaveButton();
    }
    
    initSortable();
});
</script>
@endpush
