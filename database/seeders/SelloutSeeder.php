<?php

namespace Database\Seeders;

use App\Models\Sellout;
use Illuminate\Database\Seeder;

class SelloutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Sellout::factory()->count(5)->create();
    }
}
