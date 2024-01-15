<?php

namespace Database\Seeders;

use App\ResourceCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResourceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ResourceCategory::factory()
        ->count(3)
        ->create();
    }
}
