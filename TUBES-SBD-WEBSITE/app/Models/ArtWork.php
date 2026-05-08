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
        'met_object_id',
        'title',
        'slug',
        'description',
        'gallery_number',
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

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class, 'repository_id');
    }

    public function classification(): BelongsTo
    {
        return $this->belongsTo(Classification::class, 'classification_id');
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'art_work_materials', 'art_work_id', 'material_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ArtWorkImage::class, 'art_work_id');
    }

    public function artWorkImages(): HasMany
    {
        return $this->images();
    }

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
}
