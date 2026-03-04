<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'gender',
        'birth_date',
        'country',
        'city',
        'avatar',
        'main_address',
        'status',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',
        'total_spent',
        'orders_count',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'birth_date' => 'date',
            'password' => 'hashed',
            'total_spent' => 'decimal:2',
        ];
    }

    /**
     * Get orders relationship.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get addresses relationship.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    public function default_address()
    {
        return $this->addresses()->where('is_default',1)->latest()->first();
    }
}
