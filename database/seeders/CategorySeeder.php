<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Kue Basah',
                'description' => 'Berbagai macam kue basah tradisional',
                'stock' => 38,
                'is_active' => true,
            ],
            [
                'name' => 'Kue Kering',
                'description' => 'Kue kering untuk camilan',
                'stock' => 38,
                'is_active' => true,
            ],
            [
                'name' => 'Kue Tart',
                'description' => 'Kue tart untuk acara spesial',
                'stock' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Minuman',
                'description' => 'Minuman segar dan hangat',
                'stock' => 50,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
