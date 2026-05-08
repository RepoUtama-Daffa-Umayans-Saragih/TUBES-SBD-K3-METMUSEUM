<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Material extends Model
{
    use HasFactory;

    protected $primaryKey = 'material_id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
<<<<<<< HEAD
        'material_name',
=======
        'name',
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
    ];

    public $timestamps = false;

    public function artWorks(): BelongsToMany
    {
        return $this->belongsToMany(ArtWork::class, 'art_work_materials', 'material_id', 'art_work_id');
    }
}
