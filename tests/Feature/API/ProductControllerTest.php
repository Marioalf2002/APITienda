<?php

namespace Tests\Feature\API;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecutar los seeders para tener datos de prueba
        $this->seed();
    }

    /**
     * Prueba para obtener todos los productos.
     */
    public function test_can_get_all_products(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'per_page',
                'total',
            ]);
    }

    /**
     * Prueba para obtener un producto por su ID.
     */
    public function test_can_get_product_by_id(): void
    {
        $product = Product::first();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
                'name' => $product->name,
            ]);
    }

    /**
     * Prueba para crear un nuevo producto.
     */
    public function test_can_create_product(): void
    {
        $productData = [
            'name' => 'Producto de prueba',
            'description' => 'Descripción del producto de prueba',
            'price' => 1000,
            'stock' => 50,
            'active' => true,
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201)
            ->assertJson([
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'active' => $productData['active'],
            ]);

        $this->assertDatabaseHas('products', [
            'name' => $productData['name'],
        ]);
    }

    /**
     * Prueba para actualizar un producto existente.
     */
    public function test_can_update_product(): void
    {
        $product = Product::first();

        $updatedData = [
            'name' => 'Producto actualizado',
            'price' => 1500,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
                'name' => $updatedData['name'],
                'price' => $updatedData['price'],
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $updatedData['name'],
            'price' => $updatedData['price'],
        ]);
    }

    /**
     * Prueba para eliminar un producto.
     */
    public function test_can_delete_product(): void
    {
        $product = Product::first();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    /**
     * Prueba para validar que no se puede crear un producto sin los campos requeridos.
     */
    public function test_cannot_create_product_without_required_fields(): void
    {
        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price']);
    }

    /**
     * Prueba para validar que no se puede actualizar un producto con datos inválidos.
     */
    public function test_cannot_update_product_with_invalid_data(): void
    {
        $product = Product::first();

        $invalidData = [
            'price' => 'no-es-un-numero',
        ];

        $response = $this->putJson("/api/products/{$product->id}", $invalidData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }
}