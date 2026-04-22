<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArtWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'object_number',
        'title',
        'slug',
        'description',
        'gallery_number',
        'year_start',
        'year_end',
        'department_id',
        'type_id',
        'geo_id',
        'location_id',
    ];

    public $timestamps = false;

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function objectType(): BelongsTo
    {
        return $this->belongsTo(ObjectType::class, 'type_id');
    }

    public function geoLocation(): BelongsTo
    {
        return $this->belongsTo(GeoLocation::class, 'geo_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'art_work_materials', 'art_work_id', 'material_id');
    }

    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'art_work_artists', 'art_work_id', 'artist_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ArtWorkImage::class, 'art_work_id');
    }

    public function artWorkImages(): HasMany
    {
        return $this->images();
    }
}
