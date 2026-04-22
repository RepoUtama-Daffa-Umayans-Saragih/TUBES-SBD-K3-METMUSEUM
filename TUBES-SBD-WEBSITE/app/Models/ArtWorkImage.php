<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArtWorkImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'art_work_id',
        'url',
        'is_primary',
    ];

    public $timestamps = false;

    public function artWork(): BelongsTo
    {
        return $this->belongsTo(ArtWork::class, 'art_work_id');
    }
}
