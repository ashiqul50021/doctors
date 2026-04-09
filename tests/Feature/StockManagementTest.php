<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use Tests\TestCase;

#[RequiresPhpExtension('pdo_sqlite')]
class StockManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_place_order_decrements_product_stock(): void
    {
        $product = $this->createProduct(stock: 8, price: 120);

        $response = $this->withSession([
            'cart' => [
                "product_{$product->id}" => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => 120,
                    'image' => null,
                    'quantity' => 3,
                ],
            ],
        ])->post(route('ecommerce.order.place'), [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '01700000000',
            'address' => 'Dhaka',
        ]);

        $order = Order::first();

        $this->assertNotNull($order);
        $response->assertRedirect(route('ecommerce.order.success', ['order' => $order->id]));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_place_order_fails_when_requested_quantity_exceeds_stock(): void
    {
        $product = $this->createProduct(stock: 2, price: 80);

        $response = $this->from(route('ecommerce.checkout'))->withSession([
            'cart' => [
                "product_{$product->id}" => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => 80,
                    'image' => null,
                    'quantity' => 4,
                ],
            ],
        ])->post(route('ecommerce.order.place'), [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '01700000000',
            'address' => 'Dhaka',
        ]);

        $response->assertRedirect(route('ecommerce.checkout'));
        $response->assertSessionHasErrors('stock');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 2,
        ]);
    }

    public function test_cancelling_order_restores_stock(): void
    {
        $product = $this->createProduct(stock: 4, price: 200);
        $order = Order::create([
            'order_number' => 'ORD-' . Str::upper(Str::random(10)),
            'customer_name' => 'Test Customer',
            'customer_email' => 'customer@example.com',
            'customer_phone' => '01700000000',
            'subtotal' => 400,
            'shipping' => 0,
            'total' => 400,
            'status' => 'pending',
            'shipping_address' => 'Dhaka',
            'shipping_city' => 'Dhaka',
            'shipping_phone' => '01700000000',
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 200,
            'total' => 400,
        ]);

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.status', $order->id), [
            'status' => 'cancelled',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 6,
        ]);
    }

    public function test_place_order_decrements_variant_stock_and_updates_parent_stock(): void
    {
        $product = $this->createProduct(stock: 8, price: 120);
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'option_name' => 'Strength',
            'option_value' => '500mg',
            'price' => 130,
            'sale_price' => 110,
            'stock' => 5,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'option_name' => 'Strength',
            'option_value' => '650mg',
            'price' => 150,
            'stock' => 3,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $response = $this->withSession([
            'cart' => [
                "product_{$product->id}_variant_{$variant->id}" => [
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'variant_label' => $variant->display_label,
                    'name' => $product->name,
                    'price' => 110,
                    'image' => null,
                    'quantity' => 2,
                ],
            ],
        ])->post(route('ecommerce.order.place'), [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '01700000000',
            'address' => 'Dhaka',
        ]);

        $order = Order::first();

        $this->assertNotNull($order);
        $response->assertRedirect(route('ecommerce.order.success', ['order' => $order->id]));
        $this->assertDatabaseHas('product_variants', [
            'id' => $variant->id,
            'stock' => 3,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 6,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'variant_label' => $variant->display_label,
            'quantity' => 2,
        ]);
    }

    protected function createProduct(int $stock, int $price): Product
    {
        $category = ProductCategory::create([
            'name' => 'Medicine',
            'slug' => 'medicine',
            'is_active' => true,
        ]);

        return Product::create([
            'product_category_id' => $category->id,
            'name' => 'Napa Extra',
            'slug' => 'napa-extra-' . Str::lower(Str::random(6)),
            'price' => $price,
            'stock' => $stock,
            'is_active' => true,
        ]);
    }
}
