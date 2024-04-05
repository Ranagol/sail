<?php

namespace App\Services;

use App\Models\Product;
use Mockery\MockInterface;

/**
 * This is the class that we want to test. It works with the Product model. Since we do not want to 
 * test the actual database, we will mock the Product model, during the testing (not here).
 * Do not forget, that here we are testing the ProductService class, not the Product model.
 */
class ProductService
{
    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getProductById($id)
    {
        return $this->product->find($id);
    }
}


