<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sellable extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sellout_id',

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'sellout_id' => 'integer',
    ];

    public function phones()
    {
        return $this->morphMany(Phone::class, 'phoneable');
    }

    public function sellout()
    {
        return $this->belongsTo(Sellout::class);
    }
}
