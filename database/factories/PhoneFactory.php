<?php

namespace Database\Factories;

use App\Models\BrandModel;
use App\Models\Phone;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Phone::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'purchase_id' => Purchase::factory(),
            'brand_model_id' => BrandModel::factory(),
            'item_cost' => $this->faker->randomFloat(2, 0, 999999.99),
            'imei_1' => $this->faker->word,
            'imei_2' => $this->faker->word,
            'rom_size' => $this->faker->numberBetween(-10000, 10000),
            'color' => $this->faker->word,
            'description' => $this->faker->text,
            'item_sellout_price' => $this->faker->randomFloat(0, 0, 99999999.),
            'is_new' => $this->faker->boolean,
        ];
    }
}
