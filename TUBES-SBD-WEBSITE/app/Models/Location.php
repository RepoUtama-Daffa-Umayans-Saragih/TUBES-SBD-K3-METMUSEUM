<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $primaryKey = 'location_id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
<<<<<<< HEAD
        'location_name',
=======
        'name',
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
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
<<<<<<< HEAD

    public function getNameAttribute()
    {
        return $this->location_name;
    }
=======
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
}
