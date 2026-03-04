<div class="d-flex justify-content-end flex-wrap">
    <a href="{{ route('admin.orders.show', $order) }}" 
       class="btn btn-icon btn-light btn-hover-primary btn-sm mr-1" 
       data-toggle="tooltip" 
       title="View Order">
        <i class="flaticon-eye"></i>
    </a>
    <button type="button" 
            class="btn btn-icon btn-light btn-hover-info btn-sm mr-1" 
            data-toggle="tooltip" 
            title="Update Status"
            onclick="updateOrderStatus({{ $order->id }}, '{{ $order->status }}')">
        <i class="flaticon2-settings"></i>
    </button>
    <button type="button" 
            class="btn btn-icon btn-light btn-hover-success btn-sm mr-1" 
            data-toggle="tooltip" 
            title="Update Payment Status"
            onclick="updatePaymentStatus({{ $order->id }}, '{{ $order->payment_status }}')">
        <i class="flaticon2-check-mark"></i>
    </button>
</div>

