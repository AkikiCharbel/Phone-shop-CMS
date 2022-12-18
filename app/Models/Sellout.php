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
        'is_new' => 'boolean',
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

    protected function soledPhonesShow(): Attribute
    {
        return Attribute::make(
            get: function () {
                $phones = [];
                foreach ($this->phones as $phone) {
                    $phones[] = [
                        'phone_id' => $phone->id,
                        'brand_model_name' => $phone->brandModel->name,
                        'brand_name' => $phone->brandModel->brand->name,
                        'item_cost' => $phone->item_cost,
                        'imei_1' => $phone->imei_1,
                        'imei_2' => $phone->imei_2,
                        'rom_size' => $phone->rom_size,
                        'color' => $phone->color,
                        'description' => $phone->description,
                        'item_sellout_price' => $phone->item_sellout_price,
                        'is_new' => ($phone->is_new == 1) ? 'New' : 'Used',
                    ];
                }
                return $phones;
            }
        );
    }
}
