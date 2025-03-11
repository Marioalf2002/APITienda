<?php

namespace Tests\Feature\API;

use App\Models\Order;
use App\Models\OrderState;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecutar los seeders para tener datos de prueba
        $this->seed();
    }

    /**
     * Crear una orden para pruebas
     */
    private function createTestOrder()
    {
        $user = User::first();
        $products = Product::take(2)->get();
        $payment = Payment::first();

        $orderData = [
            'user_id' => $user->id,
            'products' => [
                [
                    'id' => $products[0]->id,
                    'amount' => 3,
                ],
                [
                    'id' => $products[1]->id,
                    'amount' => 1,
                ],
            ],
            'payment' => $payment->id,
        ];

        $response = $this->postJson('/api/buy', $orderData);
        return $response;
    }

    /**
     * Prueba para crear una nueva orden.
     */
    public function test_can_create_order(): void
    {
        $user = User::first();
        $products = Product::take(2)->get();
        $payment = Payment::first();

        $orderData = [
            'user_id' => $user->id,
            'products' => [
                [
                    'id' => $products[0]->id,
                    'amount' => 3,
                ],
                [
                    'id' => $products[1]->id,
                    'amount' => 1,
                ],
            ],
            'payment' => $payment->id,
        ];

        $response = $this->postJson('/api/buy', $orderData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_id' => $payment->id,
        ]);

        // Verificar que se crearon los items de la orden
        $order = Order::latest()->first();
        $this->assertEquals(2, $order->items->count());
    }

    /**
     * Prueba para obtener el detalle de una orden.
     */
    public function test_can_get_order_detail(): void
    {
        // Crear una orden para la prueba
        $response = $this->createTestOrder();
        $response->assertStatus(201);

        $order = Order::latest()->first();

        $response = $this->getJson("/api/buy/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'user'],
                'products' => [
                    '*' => ['id', 'name', 'value', 'amount', 'total']
                ],
                'state' => ['id', 'name'],
                'total',
                'amount',
                'created_at',
                'updated_at',
                'payment' => ['id', 'name'],
            ]);
    }

    /**
     * Prueba para obtener todas las órdenes.
     */
    public function test_can_get_all_orders(): void
    {
        // Crear una orden para la prueba
        $response = $this->createTestOrder();
        $response->assertStatus(201);

        $response = $this->getJson('/api/buy');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'per_page',
                'total',
            ]);
    }

    /**
     * Prueba para actualizar el estado de una orden.
     */
    public function test_can_update_order_state(): void
    {
        // Crear una orden para la prueba
        $response = $this->createTestOrder();
        $response->assertStatus(201);

        $order = Order::latest()->first();
        $confirmedState = OrderState::where('name', 'Confirmed')->first();

        $stateData = [
            'state_id' => $confirmedState->id,
        ];

        $response = $this->putJson("/api/buy/{$order->id}/state", $stateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_state_id' => $confirmedState->id,
        ]);
    }

    /**
     * Prueba para actualizar el método de pago de una orden.
     */
    public function test_can_update_order_payment(): void
    {
        // Crear una orden para la prueba
        $response = $this->createTestOrder();
        $response->assertStatus(201);

        $order = Order::latest()->first();
        $contraEntrega = Payment::where('name', 'Contra entrega')->first();

        $paymentData = [
            'payment_id' => $contraEntrega->id,
        ];

        $response = $this->putJson("/api/buy/{$order->id}/payment", $paymentData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_id' => $contraEntrega->id,
        ]);
    }

    /**
     * Prueba para obtener las órdenes de un usuario.
     */
    public function test_can_get_user_orders(): void
    {
        // Crear una orden para la prueba
        $response = $this->createTestOrder();
        $response->assertStatus(201);

        $user = User::first();

        $response = $this->getJson("/api/user/{$user->id}/orders");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'per_page',
                'total',
            ]);
    }

    /**
     * Prueba para validar que no se puede crear una orden sin los campos requeridos.
     */
    public function test_cannot_create_order_without_required_fields(): void
    {
        $response = $this->postJson('/api/buy', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_id', 'products']);
    }

    /**
     * Prueba para validar que no se puede crear una orden con productos que no existen.
     */
    public function test_cannot_create_order_with_nonexistent_products(): void
    {
        $user = User::first();

        $orderData = [
            'user_id' => $user->id,
            'products' => [
                [
                    'id' => 9999, // ID que no existe
                    'amount' => 3,
                ],
            ],
        ];

        $response = $this->postJson('/api/buy', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['products.0.id']);
    }
}
