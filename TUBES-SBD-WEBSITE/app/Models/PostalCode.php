<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PostalCode extends Model
{
    use HasFactory;

    protected $table      = 'postal_codes';
    protected $primaryKey = 'postal_code_id';
    public $incrementing  = true;
    protected $keyType    = 'int';
    public $timestamps    = false;

    protected $fillable = [
        'postal_code',
<<<<<<< HEAD
        'postal_city',
        'postal_state',
        'postal_country',
=======
        'city',
        'state',
        'country',
>>>>>>> d4924d7e134627f65fb14d5d19ae9cabdff3b454
    ];

    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class, 'postal_code_id', 'postal_code_id');
    }
}
