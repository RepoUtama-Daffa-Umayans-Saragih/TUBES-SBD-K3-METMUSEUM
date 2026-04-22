<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisitSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'visit_date',
        'capacity_limit',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    public $timestamps = false;

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function ticketAvailabilities(): HasMany
    {
        return $this->hasMany(TicketAvailability::class, 'visit_schedule_id');
    }
}
