<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    use HasFactory;

    protected $primaryKey = 'ticket_type_id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'ticket_type_name',
        'base_price',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
    ];

    // timestamps diaktifkan agar created_at & updated_at otomatis

    public function ticketAvailabilities(): HasMany
    {
        return $this->hasMany(TicketAvailability::class, 'ticket_type_id', 'ticket_type_id');
    }

    public function getNameAttribute()
    {
        return $this->ticket_type_name;
    }
}
