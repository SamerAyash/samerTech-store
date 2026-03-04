@extends('admin.layout.app')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-2">
                <h5 class="text-dark font-weight-bold my-1 mr-5">Sort Child Categories</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.categories.index') }}" class="text-muted">Categories</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="text-muted">Sort Children</span>
                    </li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.categories.sort') }}" class="btn btn-light font-weight-bold mr-2">
                    <i class="flaticon2-list"></i> Sort Parents
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
                        <h3 class="card-label">Select Parent Category</h3>
                        <span class="text-muted d-block mt-1">Choose a parent category to sort its children.</span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    @if($parents->isEmpty())
                        <div class="text-center py-5">
                            <i class="flaticon2-file text-muted" style="font-size: 4rem;"></i>
                            <h4 class="mt-3">No Parent Categories Found</h4>
                            <p class="text-muted">Get started by creating your first category.</p>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mt-3">
                                <i class="flaticon2-plus"></i> Add Category
                            </a>
                        </div>
                    @else
                        <div class="row">
                            @foreach($parents as $parent)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <h5 class="card-title font-weight-bold">{{ optional($parent->translate('en'))->name ?? $parent->name }}</h5>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <span class="text-muted">
                                                    <i class="flaticon2-list"></i> 
                                                    {{ $parent->children->count() }} 
                                                    {{ $parent->children->count() == 1 ? 'child' : 'children' }}
                                                </span>
                                                @if($parent->status)
                                                    <span class="badge badge-success">Active</span>
                                                @else
                                                    <span class="badge badge-danger">Inactive</span>
                                                @endif
                                            </div>
                                            <a href="{{ route('admin.categories.sortChildrenOf', $parent->id) }}" 
                                               class="btn btn-primary btn-sm btn-block">
                                                <i class="flaticon2-sort"></i> Sort Children
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
