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
<<<<<<< HEAD
        'ticket_type_name',
=======
        'name',
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
        'base_price',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
    ];

    public $timestamps = false;

    public function ticketAvailabilities(): HasMany
    {
        return $this->hasMany(TicketAvailability::class, 'ticket_type_id', 'ticket_type_id');
    }
<<<<<<< HEAD

    public function getNameAttribute()
    {
        return $this->ticket_type_name;
    }
=======
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
}
