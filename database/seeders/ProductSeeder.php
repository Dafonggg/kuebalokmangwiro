<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kueBasah = Category::where('name', 'Kue Basah')->first();
        $kueKering = Category::where('name', 'Kue Kering')->first();
        $kueTart = Category::where('name', 'Kue Tart')->first();
        $minuman = Category::where('name', 'Minuman')->first();

        $products = [
            // Kue Basah
            [
                'category_id' => $kueBasah->id,
                'name' => 'Lapis Legit',
                'description' => 'Kue lapis legit dengan rasa manis dan legit',
                'price' => 25000,
                'is_active' => true,
            ],
            [
                'category_id' => $kueBasah->id,
                'name' => 'Klepon',
                'description' => 'Klepon dengan isian gula merah',
                'price' => 5000,
                'is_active' => true,
            ],
            [
                'category_id' => $kueBasah->id,
                'name' => 'Dadar Gulung',
                'description' => 'Dadar gulung dengan kelapa parut',
                'price' => 4000,
                'is_active' => true,
            ],
            // Kue Kering
            [
                'category_id' => $kueKering->id,
                'name' => 'Kastengel',
                'description' => 'Kue kastengel keju yang renyah',
                'price' => 35000,
                'is_active' => true,
            ],
            [
                'category_id' => $kueKering->id,
                'name' => 'Nastar',
                'description' => 'Kue nastar dengan selai nanas',
                'price' => 30000,
                'is_active' => true,
            ],
            [
                'category_id' => $kueKering->id,
                'name' => 'Putri Salju',
                'description' => 'Kue putri salju dengan taburan gula halus',
                'price' => 32000,
                'is_active' => true,
            ],
            // Kue Tart
            [
                'category_id' => $kueTart->id,
                'name' => 'Tart Buah',
                'description' => 'Tart dengan topping buah segar',
                'price' => 150000,
                'is_active' => true,
            ],
            [
                'category_id' => $kueTart->id,
                'name' => 'Black Forest',
                'description' => 'Kue black forest dengan cokelat',
                'price' => 180000,
                'is_active' => true,
            ],
            // Minuman
            [
                'category_id' => $minuman->id,
                'name' => 'Es Teh Manis',
                'description' => 'Es teh manis segar',
                'price' => 5000,
                'is_active' => true,
            ],
            [
                'category_id' => $minuman->id,
                'name' => 'Es Jeruk',
                'description' => 'Es jeruk peras segar',
                'price' => 8000,
                'is_active' => true,
            ],
            [
                'category_id' => $minuman->id,
                'name' => 'Kopi Hitam',
                'description' => 'Kopi hitam hangat',
                'price' => 6000,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
