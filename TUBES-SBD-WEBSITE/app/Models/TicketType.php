<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'base_price',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
    ];

    public $timestamps = false;

    public function ticketAvailabilities(): HasMany
    {
        return $this->hasMany(TicketAvailability::class, 'ticket_type_id');
    }
}
