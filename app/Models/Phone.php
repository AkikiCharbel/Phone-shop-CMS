<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Phone extends Model
{
    use CrudTrait;
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'purchase_id' => 'integer',
        'brand_model_id' => 'integer',
        'item_cost' => 'float',
        'item_sellout_price' => 'float',
        'is_new' => 'boolean',
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
        return $this->morphToMany(Sellout::class, 'sellable');
    }

    public function getPhoneInfoAttribute(): string
    {
        return $this->imei_1.' / '
            .$this->imei_2.' / '
            .$this->brandModel->full_name.' / '
            .$this->rom_size.' / '
            .$this->color;
    }
}
