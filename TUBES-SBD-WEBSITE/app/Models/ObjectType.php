<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ObjectType extends Model
{
    use HasFactory;

    protected $primaryKey = 'type_id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    public function artWorks(): HasMany
    {
        return $this->hasMany(ArtWork::class, 'type_id');
    }
}
