@extends('admin.layout.app')

@push('style')
    <style>
        .order-items-table td { vertical-align: middle; }
        .item-row-total { font-weight: bold; }
        #billing-fields { display: none; }
    </style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="subheader py-2 py-lg-4 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <div class="d-flex align-items-center flex-wrap mr-2">
                <h5 class="text-dark font-weight-bold my-1 mr-5">Create Guest Order</h5>
                <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                    <li class="breadcrumb-item"><a href="{{ route('admin.home') }}" class="text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}" class="text-muted">Orders</a></li>
                    <li class="breadcrumb-item"><span class="text-muted">Create</span></li>
                </ul>
            </div>
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-light font-weight-bold">
                    <i class="flaticon2-left-arrow-1"></i> <span class="d-none d-sm-inline">Back</span>
                </a>
            </div>
        </div>
    </div>

    <div class="d-flex flex-column-fluid">
        <div class="container">
            @includeIf('admin.component.alert')
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                </div>
            @endif

            <form action="{{ route('admin.orders.store') }}" method="POST" id="guest-order-form">
                @csrf

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card card-custom mb-5">
                            <div class="card-header">
                                <h3 class="card-title">Guest Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Guest Name <span class="text-danger">*</span></label>
                                        <input type="text" name="guest_name" class="form-control @error('guest_name') is-invalid @enderror" value="{{ old('guest_name') }}" required maxlength="255">
                                        @error('guest_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Guest Email <span class="text-danger">*</span></label>
                                        <input type="email" name="guest_email" class="form-control @error('guest_email') is-invalid @enderror" value="{{ old('guest_email') }}" required>
                                        @error('guest_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Guest Phone <span class="text-danger">*</span></label>
                                        <input type="text" name="guest_phone" class="form-control @error('guest_phone') is-invalid @enderror" value="{{ old('guest_phone') }}" required>
                                        @error('guest_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold">Currency <span class="text-danger">*</span></label>
                                        <select name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror" required>
                                            @foreach($currencies as $c)
                                                <option value="{{ $c }}" {{ old('currency', 'QAR') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                            @endforeach
                                        </select>
                                        @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-custom mb-5">
                            <div class="card-header">
                                <h3 class="card-title">Shipping Address</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="shipping_first_name" class="form-control" value="{{ old('shipping_first_name') }}" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="shipping_last_name" class="form-control" value="{{ old('shipping_last_name') }}" required>
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label class="font-weight-bold">Company</label>
                                        <input type="text" name="shipping_company" class="form-control" value="{{ old('shipping_company') }}">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label class="font-weight-bold">Address <span class="text-danger">*</span></label>
                                        <input type="text" name="shipping_address" class="form-control" value="{{ old('shipping_address') }}" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Apartment</label>
                                        <input type="text" name="shipping_apartment" class="form-control" value="{{ old('shipping_apartment') }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">City <span class="text-danger">*</span></label>
                                        <input type="text" name="shipping_city" class="form-control" value="{{ old('shipping_city') }}" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Country <span class="text-danger">*</span></label>
                                        <input type="text" name="shipping_country" class="form-control" value="{{ old('shipping_country') }}" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Postal Code</label>
                                        <input type="text" name="shipping_postal_code" class="form-control" value="{{ old('shipping_postal_code') }}">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label class="font-weight-bold">Phone <span class="text-danger">*</span></label>
                                        <input type="text" name="shipping_phone" class="form-control" value="{{ old('shipping_phone') }}" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="checkbox">
                                            <input type="checkbox" name="use_same_billing_address" value="1" id="use_same_billing" {{ old('use_same_billing_address', true) ? 'checked' : '' }}>
                                            <span></span> Same as billing address
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-custom mb-5" id="billing-fields">
                            <div class="card-header">
                                <h3 class="card-title">Billing Address</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">First Name</label>
                                        <input type="text" name="billing_first_name" class="form-control" value="{{ old('billing_first_name') }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Last Name</label>
                                        <input type="text" name="billing_last_name" class="form-control" value="{{ old('billing_last_name') }}">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label class="font-weight-bold">Address</label>
                                        <input type="text" name="billing_address" class="form-control" value="{{ old('billing_address') }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">City</label>
                                        <input type="text" name="billing_city" class="form-control" value="{{ old('billing_city') }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Country</label>
                                        <input type="text" name="billing_country" class="form-control" value="{{ old('billing_country') }}">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label class="font-weight-bold">Phone</label>
                                        <input type="text" name="billing_phone" class="form-control" value="{{ old('billing_phone') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-custom mb-5">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title mb-0">Order Items</h3>
                                <button type="button" class="btn btn-sm btn-primary" id="add-item-row">
                                    <i class="flaticon2-plus"></i> Add Item
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table order-items-table" id="order-items-table">
                                        <thead>
                                            <tr>
                                                <th>Product (SKU / Name)</th>
                                                <th width="180">Variant (Color / Size – Qty)</th>
                                                <th width="80">Qty</th>
                                                <th width="100">Unit Price</th>
                                                <th width="100">Subtotal</th>
                                                <th width="50"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="items-tbody">
                                            <tr class="item-row" data-index="0">
                                                <td>
                                                    <input type="text" class="form-control form-control-sm item-search" placeholder="Search product..." autocomplete="off" value="{{ old('items.0.product_sku') ? old('items.0.product_sku').' - '.old('items.0.product_name') : '' }}">
                                                    <input type="hidden" name="items[0][product_sku]" class="item-sku" value="{{ old('items.0.product_sku') }}">
                                                    <input type="hidden" name="items[0][product_name]" class="item-name" value="{{ old('items.0.product_name') }}">
                                                    <span class="text-muted small selected-product-name">{{ old('items.0.product_sku') ? old('items.0.product_sku').' - '.old('items.0.product_name') : '' }}</span>
                                                </td>
                                                <td>
                                                    <select class="form-control form-control-sm item-variant" disabled title="Select product first">
                                                        <option value="">— Select variant —</option>
                                                    </select>
                                                    <input type="hidden" name="items[0][variant_id]" class="item-variant-id" value="{{ old('items.0.variant_id') }}">
                                                    <input type="hidden" name="items[0][color]" class="item-color" value="{{ old('items.0.color') }}">
                                                    <input type="hidden" name="items[0][size]" class="item-size" value="{{ old('items.0.size') }}">
                                                    <span class="text-muted small item-variant-qty"></span>
                                                </td>
                                                <td><input type="number" name="items[0][quantity]" class="form-control form-control-sm item-qty" min="1" value="{{ old('items.0.quantity', 1) }}" title="Max from inventory"></td>
                                                <td><input type="number" name="items[0][price]" class="form-control form-control-sm item-price" step="0.01" min="0" value="{{ old('items.0.price', '0') }}" readonly></td>
                                                <td class="item-row-total">0.00</td>
                                                <td><button type="button" class="btn btn-icon btn-sm btn-light-danger remove-item-row"><i class="flaticon2-trash"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card card-custom mb-5">
                            <div class="card-header">
                                <h3 class="card-title">Shipping & Totals</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Shipping Method <span class="text-danger">*</span></label>
                                    <select name="shipping_method" id="shipping_method" class="form-control" required>
                                        @foreach($shippingMethods as $m)
                                            <option value="{{ $m['id'] }}" data-cost="{{ $m['cost'] }}" {{ old('shipping_method') == $m['id'] ? 'selected' : '' }}>{{ $m['name'] }} ({{ number_format($m['cost'], 2) }} {{ old('currency', 'QAR') }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Shipping Cost <span class="text-danger">*</span></label>
                                    <input type="number" name="shipping_cost" id="shipping_cost" class="form-control" step="0.01" min="0" value="{{ old('shipping_cost', $shippingMethods[0]['cost'] ?? 0) }}" required>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Discount Amount</label>
                                    <input type="number" name="discount_amount" id="discount_amount" class="form-control" step="0.01" min="0" value="{{ old('discount_amount', 0) }}">
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="summary-subtotal">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span id="summary-shipping">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Discount:</span>
                                    <span id="summary-discount">0.00</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between font-weight-bold font-size-h5">
                                    <span>Total:</span>
                                    <span id="summary-total">0.00</span>
                                </div>
                            </div>
                        </div>

                        <div class="card card-custom mb-5">
                            <div class="card-header">
                                <h3 class="card-title">Order Status</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Status <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        @foreach(['pending'=>'Pending','processing'=>'Processing','shipped'=>'Shipped','delivered'=>'Delivered','cancelled'=>'Cancelled'] as $v => $l)
                                            <option value="{{ $v }}" {{ old('status', 'pending') == $v ? 'selected' : '' }}>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Payment Status <span class="text-danger">*</span></label>
                                    <select name="payment_status" class="form-control" required>
                                        @foreach(['pending'=>'Pending','paid'=>'Paid','failed'=>'Failed','refunded'=>'Refunded'] as $v => $l)
                                            <option value="{{ $v }}" {{ old('payment_status', 'pending') == $v ? 'selected' : '' }}>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Payment Method</label>
                                    <input type="text" name="payment_method" class="form-control" value="{{ old('payment_method', 'manual') }}">
                                </div>
                                <div class="form-group">
                                    <label class="font-weight-bold">Notes</label>
                                    <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary font-weight-bold btn-block">
                            <i class="flaticon2-check-mark"></i> Create Order
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="product-search-dropdown" class="list-group position-fixed" style="display:none; z-index:1050; max-height:250px; overflow-y:auto; background:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.15);"></div>
@endsection

@push('scripts')
<script>
(function() {
    var itemIndex = 1;
    var searchUrl = "{{ route('admin.orders.searchProducts') }}";
    var shippingMethodsUrl = "{{ route('admin.orders.shippingMethods') }}";
    var currencySymbol = "{{ old('currency', 'QAR') }}";
    var searchTimeout;

    $('#currency').on('change', function() {
        var c = $(this).val();
        currencySymbol = c;
        $.get(shippingMethodsUrl, { currency: c }, function(res) {
            var $sel = $('#shipping_method');
            var current = $sel.val();
            $sel.empty();
            (res.shipping_methods || []).forEach(function(m) {
                $sel.append($('<option></option>').val(m.id).attr('data-cost', m.cost).text(m.name + ' (' + parseFloat(m.cost).toFixed(2) + ' ' + c + ')'));
            });
            if (current && $sel.find('option[value="' + current + '"]').length) $sel.val(current);
            else if ($sel.find('option').length) {
                $sel.get(0).selectedIndex = 0;
                $('#shipping_cost').val($sel.find('option:selected').data('cost'));
            }
            updateSummary();
        });
    });

    $('#use_same_billing').on('change', function() {
        $('#billing-fields').toggle(!this.checked);
    });
    if (!$('#use_same_billing').is(':checked')) $('#billing-fields').show();

    $('#shipping_method').on('change', function() {
        var cost = $(this).find('option:selected').data('cost');
        if (cost != null) $('#shipping_cost').val(cost);
        updateSummary();
    });
    $('#shipping_cost, #discount_amount').on('input', updateSummary);

    var productVariantsUrlBase = "{{ url('romano/orders/product-variants') }}";

    function addItemRow() {
        var row = '<tr class="item-row" data-index="' + itemIndex + '">' +
            '<td><input type="text" class="form-control form-control-sm item-search" placeholder="Search product..." autocomplete="off">' +
            '<input type="hidden" name="items[' + itemIndex + '][product_sku]" class="item-sku">' +
            '<input type="hidden" name="items[' + itemIndex + '][product_name]" class="item-name">' +
            '<span class="text-muted small selected-product-name"></span></td>' +
            '<td><select class="form-control form-control-sm item-variant" disabled><option value="">— Select variant —</option></select>' +
            '<input type="hidden" name="items[' + itemIndex + '][variant_id]" class="item-variant-id">' +
            '<input type="hidden" name="items[' + itemIndex + '][color]" class="item-color">' +
            '<input type="hidden" name="items[' + itemIndex + '][size]" class="item-size">' +
            '<span class="text-muted small item-variant-qty"></span></td>' +
            '<td><input type="number" name="items[' + itemIndex + '][quantity]" class="form-control form-control-sm item-qty" min="1" value="1"></td>' +
            '<td><input type="number" name="items[' + itemIndex + '][price]" class="form-control form-control-sm item-price" step="0.01" min="0" value="0" readonly></td>' +
            '<td class="item-row-total">0.00</td>' +
            '<td><button type="button" class="btn btn-icon btn-sm btn-light-danger remove-item-row"><i class="flaticon2-trash"></i></button></td></tr>';
        $('#items-tbody').append(row);
        bindItemRow($('#items-tbody tr.item-row').last());
        itemIndex++;
    }

    function bindItemRow($row) {
        $row.find('.item-qty, .item-price').on('input', function() { updateRowTotal($row); updateSummary(); });
        $row.find('.remove-item-row').on('click', function() {
            $row.remove();
            reindexItems();
            updateSummary();
        });
        bindSearch($row);
        updateRowTotal($row);
    }

    function bindSearch($row) {
        var $input = $row.find('.item-search');
        $input.on('input', function() {
            var q = $(this).val().trim();
            if (q.length < 2) { hideSearch(); return; }
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                $.get(searchUrl, { q: q }, function(res) {
                    var $dd = $('#product-search-dropdown');
                    $dd.empty().hide();
                    (res.results || []).forEach(function(p) {
                        $dd.append('<a href="#" class="list-group-item list-group-item-action product-option" data-sku="' + (p.ref_code || '') + '" data-name="' + (p.name || '').replace(/"/g, '&quot;') + '">' + (p.ref_code || '') + ' - ' + (p.name || '') + '</a>');
                    });
                    if ($dd.children().length) {
                        var r = $input[0].getBoundingClientRect();
                        $dd.css({ position: 'fixed', top: r.bottom + 'px', left: r.left + 'px', width: r.width + 'px' }).show();
                        $dd.find('.product-option').on('click', function(e) {
                            e.preventDefault();
                            var sku = $(this).data('sku'), name = $(this).data('name');
                            $row.find('.item-sku').val(sku);
                            $row.find('.item-name').val(name);
                            $row.find('.selected-product-name').text(sku + ' - ' + name);
                            $row.find('.item-price').val('0');
                            $row.find('.item-variant-id').val('');
                            $row.find('.item-color').val('');
                            $row.find('.item-size').val('');
                            $row.find('.item-qty').attr('max', '');
                            $row.find('.item-variant-qty').text('');
                            $input.val('');
                            $dd.hide();
                            loadVariantsForRow($row, sku);
                            updateRowTotal($row);
                            updateSummary();
                        });
                    }
                });
            }, 300);
        });
    }

    function loadVariantsForRow($row, refCode) {
        var $sel = $row.find('.item-variant');
        $sel.empty().append('<option value="">— Select variant —</option>').prop('disabled', true);
        var url = productVariantsUrlBase + '/' + encodeURIComponent(refCode);
        $.get(url, function(res) {
            var variants = res.variants || [];
            if (variants.length === 0) {
                $sel.append('<option value="">No variants available</option>').prop('disabled', false);
                return;
            }
            variants.forEach(function(v, i) {
                var label = (v.color_desc || v.color_code || '-') + ' / ' + (v.size_code || '-') + ' — Qty: ' + v.qty;
                $sel.append($('<option></option>').val(i).attr('data-variant-id', v.variant_id || '').attr('data-color', v.color_code || '').attr('data-size', v.size_code || '').attr('data-price', v.price).attr('data-qty', v.qty).text(label));
            });
            $sel.prop('disabled', false).on('change', function() {
                var $opt = $(this).find('option:selected');
                if ($opt.val() === '') return;
                $row.find('.item-variant-id').val($opt.data('variant-id') || '');
                $row.find('.item-color').val($opt.data('color') || '');
                $row.find('.item-size').val($opt.data('size') || '');
                $row.find('.item-price').val(parseFloat($opt.data('price')) || 0);
                var maxQty = parseInt($opt.data('qty'), 10) || 9999;
                $row.find('.item-qty').attr('max', maxQty);
                $row.find('.item-variant-qty').text('Max: ' + maxQty);
                var cur = parseInt($row.find('.item-qty').val(), 10) || 1;
                if (cur > maxQty) $row.find('.item-qty').val(maxQty);
                updateRowTotal($row);
                updateSummary();
            });
        });
    }

    $(document).on('click', function() { $('#product-search-dropdown').hide(); });
    $('#product-search-dropdown').on('click', function(e) { e.stopPropagation(); });

    function hideSearch() { $('#product-search-dropdown').hide().empty(); }

    function updateRowTotal($row) {
        var qty = parseFloat($row.find('.item-qty').val()) || 0;
        var price = parseFloat($row.find('.item-price').val()) || 0;
        $row.find('.item-row-total').text((qty * price).toFixed(2));
    }

    function reindexItems() {
        $('#items-tbody tr.item-row').each(function(i) {
            $(this).attr('data-index', i);
            $(this).find('input[name^="items["]').each(function() {
                var n = $(this).attr('name');
                if (n) $(this).attr('name', n.replace(/^items\[\d+\]/, 'items[' + i + ']'));
            });
        });
        itemIndex = $('#items-tbody tr.item-row').length;
    }

    function updateSummary() {
        var subtotal = 0;
        $('#items-tbody tr.item-row').each(function() {
            var q = parseFloat($(this).find('.item-qty').val()) || 0;
            var p = parseFloat($(this).find('.item-price').val()) || 0;
            subtotal += q * p;
        });
        var shipping = parseFloat($('#shipping_cost').val()) || 0;
        var discount = parseFloat($('#discount_amount').val()) || 0;
        var total = subtotal + shipping - discount;
        $('#summary-subtotal').text(subtotal.toFixed(2) + ' ' + currencySymbol);
        $('#summary-shipping').text(shipping.toFixed(2) + ' ' + currencySymbol);
        $('#summary-discount').text('-' + discount.toFixed(2) + ' ' + currencySymbol);
        $('#summary-total').text(total.toFixed(2) + ' ' + currencySymbol);
    }

    $('#add-item-row').on('click', addItemRow);
    $('#items-tbody tr.item-row').each(function() { bindItemRow($(this)); });
    updateSummary();
})();
</script>
@endpush
