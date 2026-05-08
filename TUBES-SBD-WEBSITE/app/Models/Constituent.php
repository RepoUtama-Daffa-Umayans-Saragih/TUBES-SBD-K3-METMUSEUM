<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Constituent extends Model
{
    use HasFactory;

    protected $table = 'constituents';
    protected $primaryKey = 'constituent_id';
    public $timestamps = false;

    protected $fillable = [
        'met_constituent_id',
        'display_name',
        'display_bio',
        'alpha_sort',
        'birth_year',
        'death_year',
        'birth_date_display',
        'death_date_display',
        'birth_place',
        'death_place',
        'gender',
        'ulan_url',
        'wikidata_url'
    ];

    public function artWorks(): BelongsToMany
    {
        return $this->belongsToMany(ArtWork::class, 'art_work_constituents', 'constituent_id', 'art_work_id');
    }
}
