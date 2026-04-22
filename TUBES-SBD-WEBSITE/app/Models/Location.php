<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'capacity_limit',
    ];

    public $timestamps = false;

    public function artWorks(): HasMany
    {
        return $this->hasMany(ArtWork::class, 'location_id');
    }

    public function visitSchedules(): HasMany
    {
        return $this->hasMany(VisitSchedule::class, 'location_id');
    }
}
