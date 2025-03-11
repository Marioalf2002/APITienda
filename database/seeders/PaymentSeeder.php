<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $payments = [
            [
                'name' => 'Tarjeta de crédito',
                'description' => 'Pago con tarjeta de crédito',
                'active' => true,
            ],
            [
                'name' => 'Contra entrega',
                'description' => 'Pago contra entrega',
                'active' => true,
            ],
            [
                'name' => 'Transferencia bancaria',
                'description' => 'Pago mediante transferencia bancaria',
                'active' => true,
            ],
        ];

        foreach ($payments as $payment) {
            Payment::create($payment);
        }
    }
}