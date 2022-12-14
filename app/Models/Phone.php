<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Phone extends Model
{
    use CrudTrait;
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'purchase_id',
        'brand_model_id',
        'item_cost',
        'imei_1',
        'imei_2',
        'rom_size',
        'color',
        'description',
        'item_sellout_price',
        'is_new',
    ];

    protected $casts = [
        'id' => 'integer',
        'purchase_id' => 'integer',
        'brand_model_id' => 'integer',
        'item_cost' => 'float',
        'item_sellout_price' => 'float',
        'is_new' => 'boolean',
    ];

    protected $appends = [
        'phone_info',
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function brandModel(): BelongsTo
    {
        return $this->belongsTo(BrandModel::class);
    }

    public function sellout(): MorphToMany
    {
        return $this->morphToMany(Sellout::class, 'sellable')
            ->withTimestamps();
    }

    public function phoneInfo(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->brandModel?->full_name.' / '
                    .$this?->imei_1.' / '
                    .$this?->imei_2.' / '
                    .$this?->rom_size.' / '
                    .$this?->color;
            }
        );
    }
}
