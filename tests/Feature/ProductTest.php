<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_products()
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_create_product()
    {
        $category = Category::factory()->create();
        $user = User::factory()->create(['email' => 'user@example.com']);

        $productData = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 1000,
            'category_id' => $category->id,
            'expired_at' => '2024-12-31',
            'modified_by' => $user->email,
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('products', $productData);
    }

    public function test_show_product()
    {
        $product = Product::factory()->create();

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $product->id,
            'name' => $product->name,
            // Include other fields you want to check
        ]);
    }

    public function test_update_product()
    {
        $product = Product::factory()->create();
        $updateData = [
            'name' => 'Updated Product Name',
            'price' => 1200,
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('products', $updateData);
    }

    public function test_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
