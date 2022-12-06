<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Sellout extends Model
{
    use CrudTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'amount',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'customer_id' => 'integer',
        'amount' => 'float',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function phones(): MorphToMany
    {
        return $this->morphedByMany(Phone::class, 'sellable')
            ->withTimestamps();
    }

    protected function soledPhones(): Attribute
    {
        return Attribute::make(
            get: function () {
                $phones = [];
                foreach ($this->phones as $phone) {
                    $phones[] = [
                        'phone_id' => $phone->id,
                        'price_sold' => $phone->item_sellout_price,
                    ];
                }

                return $phones;
            }
        );
    }
}
