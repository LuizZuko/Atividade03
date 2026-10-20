<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use App\Services\ProductService;
use InvalidArgumentException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = new ProductService();
    }

    public function test_cannot_create_product_if_price_violates_business_rule(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $dataWithInvalidPrice = [
            'name' => 'Caneta Azul',
            'description' => 'Caneta esferográfica',
            'price' => 5.00 
        ];

        $this->productService->create($dataWithInvalidPrice);

        $this->assertDatabaseMissing('products', [
            'name' => 'Caneta Azul'
        ]);
    }

    
    public function test_can_update_product_successfully_via_service(): void
    {
        $product = Product::factory()->create([
            'name' => 'Mouse USB',
            'price' => 20.00
        ]);

        $updateData = [
            'name' => 'Mouse Sem Fio',
            'price' => 80.00 
        ];

        $updatedProduct = $this->productService->update($product, $updateData);

        $this->assertEquals('Mouse Sem Fio', $updatedProduct->name);
        $this->assertEquals(80.00, $updatedProduct->price);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Mouse Sem Fio',
            'price' => 80.00
        ]);
    }

    public function test_cannot_update_product_if_new_price_violates_business_rule(): void
    {
        $product = Product::factory()->create([
            'name' => 'Monitor 24"',
            'price' => 500.00
        ]);

        $this->expectException(InvalidArgumentException::class);

        $invalidUpdateData = [
            'price' => 3.50
        ];

        try {
            $this->productService->update($product, $invalidUpdateData);
        } finally {
            $this->assertDatabaseHas('products', [
                'id' => $product->id,
                'price' => 500.00
            ]);
        }
    }
}