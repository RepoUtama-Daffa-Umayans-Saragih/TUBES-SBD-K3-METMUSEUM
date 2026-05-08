<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $primaryKey = 'department_id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
<<<<<<< HEAD
        'department_name',
=======
        'name',
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
    ];

    public $timestamps = false;

    public function artWorks(): HasMany
    {
        return $this->hasMany(ArtWork::class, 'department_id');
    }
<<<<<<< HEAD

    public function getNameAttribute()
    {
        return $this->department_name;
    }
=======
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
}
