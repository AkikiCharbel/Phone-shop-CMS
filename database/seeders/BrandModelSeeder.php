<?php

namespace Database\Seeders;

use App\Models\BrandModel;
use Illuminate\Database\Seeder;

class BrandModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BrandModel::factory()->count(5)->create();
    }
}
