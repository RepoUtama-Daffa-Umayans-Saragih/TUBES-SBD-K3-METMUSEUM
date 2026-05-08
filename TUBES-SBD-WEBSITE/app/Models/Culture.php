<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Culture extends Model
{
    use HasFactory;

    protected $table = 'cultures';
    protected $primaryKey = 'culture_id';
    public $timestamps = false;

    protected $fillable = [
        'culture_name'
    ];

    public function artWorks(): BelongsToMany
    {
        return $this->belongsToMany(ArtWork::class, 'art_work_cultures', 'culture_id', 'art_work_id');
    }
}
