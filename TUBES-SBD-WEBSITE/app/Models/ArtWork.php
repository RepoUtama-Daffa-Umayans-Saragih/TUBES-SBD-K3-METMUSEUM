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

    protected $primaryKey = 'art_work_id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
<<<<<<< HEAD
        'met_object_id',
=======
        'object_number',
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
        'title',
        'slug',
        'description',
        'gallery_number',
<<<<<<< HEAD
        'accession_number',
        'accession_year',
        'object_date_display',
        'object_begin_date',
        'object_end_date',
        'medium_display',
        'dimensions_display',
        'credit_line',
        'rights_and_reproduction',
        'metadata_date',
        'is_on_view',
        'is_highlight',
        'is_public_domain',
        'is_timeline_work',
        'department_id',
        'type_id',
        'location_id',
        'repository_id',
        'classification_id',
        'link_resource',
        'object_url',
        'object_wikidata_url',
=======
        'year_start',
        'year_end',
        'department_id',
        'type_id',
        'geo_id',
        'location_id',
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
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

<<<<<<< HEAD
=======
    public function geoLocation(): BelongsTo
    {
        return $this->belongsTo(GeoLocation::class, 'geo_id');
    }

>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

<<<<<<< HEAD
    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class, 'repository_id');
    }

    public function classification(): BelongsTo
    {
        return $this->belongsTo(Classification::class, 'classification_id');
    }

=======
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'art_work_materials', 'art_work_id', 'material_id');
    }

<<<<<<< HEAD
=======
    public function artists(): BelongsToMany
    {
        return $this->belongsToMany(Artist::class, 'art_work_artists', 'art_work_id', 'artist_id');
    }

>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
    public function images(): HasMany
    {
        return $this->hasMany(ArtWorkImage::class, 'art_work_id');
    }

    public function artWorkImages(): HasMany
    {
        return $this->images();
    }
<<<<<<< HEAD

    public function getNameAttribute()
    {
        return $this->title;
    }

    public function getYearStartAttribute()
    {
        return $this->object_begin_date;
    }

    public function getImageUrlAttribute()
    {
        $image = $this->images()->where('is_primary', true)->first() ?? $this->images()->first();
        return $image ? $image->image_url : null;
    }

    public function getCreatedAtAttribute()
    {
        return \Carbon\Carbon::now();
    }

    public function getUpdatedAtAttribute()
    {
        return \Carbon\Carbon::now();
    }
=======
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
}
