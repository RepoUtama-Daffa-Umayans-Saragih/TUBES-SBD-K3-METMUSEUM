<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class County extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'counties';
    protected $primaryKey = 'county_id';

    protected $fillable = ['state_id', 'county_name'];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }
}
