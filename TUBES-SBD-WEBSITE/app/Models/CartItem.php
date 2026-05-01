<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'cart_item_id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'cart_group_id',
        'ticket_availability_id',
        'quantity',
    ];

    public $timestamps = false;

    public function cart(): BelongsTo
    {
        return $this->belongsTo(CartGroup::class, 'cart_group_id', 'cart_group_id');
    }

    public function ticketAvailability(): BelongsTo
    {
        return $this->belongsTo(TicketAvailability::class, 'ticket_availability_id', 'ticket_availability_id');
    }
}
