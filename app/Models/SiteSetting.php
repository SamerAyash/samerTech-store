<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Facades\Purify;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public function getValueAttribute($value)
    {
        return Purify::clean($value);
    }
    /**
     * Create or update setting.
     */
    public static function setSetting(string $key,$value = null)
    {
        $setting = static::firstOrCreate(
            ['key' => $key],
            [
                'value' => $value,
            ]
        );
        return $setting;
    }
}