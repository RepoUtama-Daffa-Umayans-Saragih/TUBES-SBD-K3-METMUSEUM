<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'ticket_availability_id',
        'quantity',
    ];

    public $timestamps = false;

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    public function ticketAvailability(): BelongsTo
    {
        return $this->belongsTo(TicketAvailability::class, 'ticket_availability_id');
    }
}
