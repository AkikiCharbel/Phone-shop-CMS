<?php

namespace Database\Seeders;

use App\Models\Sellable;
use Illuminate\Database\Seeder;

class SellableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sellable::factory()->count(5)->create();
    }
}
