<?php

namespace Database\Factories;

use App\Models\Sellout;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SelloutFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Sellout::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'customer_id' => User::factory(),
            'amout' => $this->faker->randomFloat(0, 0, 99999999.),
        ];
    }
}
