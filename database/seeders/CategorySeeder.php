<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   
    public function run()
    {
        // Create 50 categories using the factory
        Category::factory()->count(50)->create();
    }
}
