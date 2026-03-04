<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'guest_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'access_token',
        'status',
        'payment_method',
        'payment_status',
        'shipping_method',
        'shipping_cost',
        'subtotal',
        'discount_amount',
        'total',
        'currency',
        'currency_rate',
        'discount_code_id',
        'shipping_address_id',
        'billing_address_id',
        'payment_url',
        'payment_transaction_id',
        'myfatoorah_invoice_id',
        'notes',
    ];

    protected $hidden = [
        'access_token',
    ];

    protected $casts = [
        'shipping_cost' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'currency_rate' => 'decimal:6',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->access_token = bin2hex(random_bytes(32));
        });
    }

    /**
     * Get user relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get order items relationship.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get discount code relationship.
     */
    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    /**
     * Get shipping address relationship.
     */
    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    /**
     * Get billing address relationship.
     */
    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) ($this->total ?? 0);
    }

    public function getTotalInBaseCurrencyAttribute(): ?float
    {
        if ($this->currency === 'QAR' || $this->currency_rate === null || (float) $this->currency_rate <= 0) {
            return null;
        }
        return round((float) $this->total / (float) $this->currency_rate, 2);
    }
}
