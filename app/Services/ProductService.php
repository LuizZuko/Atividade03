<?php

namespace App\Services;

use App\Models\Product;
use InvalidArgumentException;

class ProductService
{
    private const MIN_PRICE = 10.00;

   
    public function create(array $data): Product
    {
        $this->validatePrice($data['price'] ?? null);

        return Product::create($data);
    }

   
    public function update(Product $product, array $data): Product
    {
        if (array_key_exists('price', $data)) {
            $this->validatePrice($data['price']);
        }

        $product->update($data);

        return $product;
    }

    private function validatePrice(?float $price): void
    {
        if ($price === null || $price < self::MIN_PRICE) {
            throw new InvalidArgumentException("O valor do produto não pode ser menor que R$ " . number_format(self::MIN_PRICE, 2, ',', '.'));
        }
    }
}