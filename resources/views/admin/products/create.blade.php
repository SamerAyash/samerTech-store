@extends('admin.layout.app')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <h5 class="text-dark font-weight-bold my-1 mr-5">Create Product</h5>
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="card card-custom">
                <form method="POST" action="{{ route('admin.products.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Ref Code</label>
                                <input type="text" name="ref_code" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>English Name</label>
                                <input type="text" name="en_name" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Arabic Name</label>
                                <input type="text" name="ar_name" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>English Brand</label>
                                <input type="text" name="en_brand" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Arabic Brand</label>
                                <input type="text" name="ar_brand" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Product Type</label>
                                <select name="product_type_id" class="form-control">
                                    <option value="">-- Select Type --</option>
                                    @foreach($productTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <hr>
                        <h5>First Variant</h5>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>SKU</label>
                                <input type="text" name="variants[0][sku]" class="form-control">
                            </div>
                            <div class="form-group col-md-4">
                                <label>Price</label>
                                <input type="number" step="0.01" name="variants[0][price]" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Stock</label>
                                <input type="number" name="variants[0][stock]" class="form-control" min="0" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Attributes (JSON)</label>
                                <textarea name="variants[0][attributes]" class="form-control" rows="3" placeholder='{"color":"black","size":"L"}'></textarea>
                                <small class="text-muted">Use JSON object for dynamic attributes.</small>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Create Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
