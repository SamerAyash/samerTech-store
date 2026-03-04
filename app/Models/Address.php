<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'country',
        'first_name',
        'last_name',
        'company',
        'address',
        'apartment',
        'city',
        'postal_code',
        'phone',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get user relationship.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get shipping addresses.
     */
    public function scopeShipping($query)
    {
        return $query->where('type', 'shipping');
    }

    /**
     * Scope to get billing addresses.
     */
    public function scopeBilling($query)
    {
        return $query->where('type', 'billing');
    }

    /**
     * Scope to get default addresses.
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
