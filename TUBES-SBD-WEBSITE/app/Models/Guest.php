<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'session_token',
    ];

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class, 'guest_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'guest_id');
    }
}
