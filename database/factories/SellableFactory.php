<?php

namespace Database\Factories;

use App\Models\Sellable;
use App\Models\Sellout;
use Illuminate\Database\Eloquent\Factories\Factory;

class SellableFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sellable::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sellout_id' => Sellout::factory(),
            'sellable_id' => $this->faker->randomDigitNotNull,
            'sellable_type' => $this->faker->word,
        ];
    }
}
