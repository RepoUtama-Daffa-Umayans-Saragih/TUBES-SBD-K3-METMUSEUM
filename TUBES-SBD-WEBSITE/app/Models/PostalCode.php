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
        'city',
        'state',
        'country',
    ];

    public function userProfiles(): HasMany
    {
        return $this->hasMany(UserProfile::class, 'postal_code_id', 'postal_code_id');
    }
}
