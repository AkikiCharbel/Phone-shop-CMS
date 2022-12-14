<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Purchase extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'shipping_source',
        'shipping_date',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'date' => 'date',
        'shipping_date' => 'date',
    ];

    public function phones(): HasMany
    {
        return $this->hasMany(Phone::class);
    }

    public function getPhoneListAttribute()
    {
        return $this->phones->toArray();
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
