<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'email',
        'password',
        'is_admin',
        'premium_started_at',
        'premium_ended_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password'           => 'hashed',
            'is_admin'           => 'boolean',
            'premium_started_at' => 'datetime',
            'premium_ended_at'   => 'datetime',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'user_id', 'user_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class, 'user_id', 'user_id');
    }

    public function getRememberTokenName()
    {
        return null;
    }
}
