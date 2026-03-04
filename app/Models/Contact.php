<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'name',
        'phone',
        'subject',
        'message',
        'readed'
    ];

    protected $casts = [
        'readed' => 'boolean',
    ];
}
