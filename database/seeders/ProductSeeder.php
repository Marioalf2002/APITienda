<?php

namespace Database\Seeders;

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
        $products = [
            [
                'name' => 'Producto 1',
                'description' => 'Descripción del producto 1',
                'price' => 1200,
                'stock' => 100,
                'active' => true,
            ],
            [
                'name' => 'Producto 2',
                'description' => 'Descripción del producto 2',
                'price' => 800,
                'stock' => 50,
                'active' => true,
            ],
            [
                'name' => 'Producto 3',
                'description' => 'Descripción del producto 3',
                'price' => 1500,
                'stock' => 75,
                'active' => true,
            ],
            [
                'name' => 'Producto 4',
                'description' => 'Descripción del producto 4',
                'price' => 2000,
                'stock' => 30,
                'active' => true,
            ],
            [
                'name' => 'Producto 5',
                'description' => 'Descripción del producto 5',
                'price' => 950,
                'stock' => 60,
                'active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
