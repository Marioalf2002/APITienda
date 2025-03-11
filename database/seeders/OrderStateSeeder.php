<?php

namespace Database\Seeders;

use App\Models\OrderState;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            [
                'name' => 'Processing',
                'description' => 'La orden está siendo procesada',
            ],
            [
                'name' => 'Confirmed',
                'description' => 'La orden ha sido confirmada',
            ],
            [
                'name' => 'Shipped',
                'description' => 'La orden ha sido enviada',
            ],
            [
                'name' => 'Delivered',
                'description' => 'La orden ha sido entregada',
            ],
            [
                'name' => 'Cancelled',
                'description' => 'La orden ha sido cancelada',
            ],
        ];

        foreach ($states as $state) {
            OrderState::create($state);
        }
    }
}