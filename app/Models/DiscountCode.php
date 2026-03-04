<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'min_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'boolean',
    ];

    /**
     * Get orders that used this discount code.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Check if the discount code is valid.
     */
    public function isValid(): bool
    {
        // Check if status is active
        if (!$this->status) {
            return false;
        }

        $now = Carbon::now();

        // Check if current date is before start date
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        // Check if current date is after end date
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        // Check if usage limit has been reached
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount for a given total.
     */
    public function calculateDiscount(float $total): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->min_amount && $total < $this->min_amount) {
            return 0;
        }

        $discount = 0;

        if ($this->discount_type === 'percentage') {
            $discount = ($total * $this->discount_value) / 100;
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
        } else {
            $discount = $this->discount_value;
            if ($discount > $total) {
                $discount = $total;
            }
        }

        return round($discount, 2);
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    /**
     * Scope to get active discount codes.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope to get valid discount codes.
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('status', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_date')
                  ->orWhere('start_date', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', $now);
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                  ->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }
}
