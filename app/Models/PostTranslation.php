<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Facades\Purify;

class PostTranslation extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'locale',
    ];

    public $timestamps = false;

    protected $casts = [
        'content' => 'string',
    ];

    public function setContentAttribute($value)
    {
        $this->attributes['content'] = Purify::clean($value);
    }
}
