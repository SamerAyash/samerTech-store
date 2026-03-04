<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Facades\Purify;

class ProductTranslation extends Model
{
    protected $fillable = [
        'name',
        'brand',
        'short_description',
        'description',
        'details',
        'locale',
    ];

    public $timestamps = false;

    protected $casts = [
        'description' => 'string',
        'details' => 'string',
    ];

    public function setDescriptionAttribute($value)
    {
        $this->attributes['description'] = Purify::clean($value);
    }

    public function setDetailsAttribute($value)
    {
        $this->attributes['details'] = Purify::clean($value);
    }
}
