<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Product;
use Mockery\MockInterface;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * We are making a feature testing for ProductService. We do not want to test the actual database, 
 * so we will mock the Product model, during our testing.
 * 
 * sail artisan test --filter testGetProductById
 * 
 * Important: we test the ProductService class, not the Product model. 
 */
class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testGetProductById()
    {
        //Create a product in db. 
        $product = Product::factory()->create();

        /**
         * Mock the Product model. 
         * 
         * We are telling the mock to expect a call to the find method with the
         * product id. The mock should return the product instance that we created above.
         */
        $productModelMock = $this->mock(
            Product::class, 
            function (MockInterface $mock) use ($product) {
                $mock->shouldReceive('find')
                    ->with($product->id)
                    ->once()
                    ->andReturn($product);
            }
        );

        //Create an instance of ProductService, that will use the mocked Product model
        $productService = new ProductService($productModelMock);

        //Call the method, that will use the mocked Product model
        $result = $productService->getProductById($product->id);

        /**
         * Assert.
         */
        $this->assertEquals($product->id, $result->id);
        $this->assertEquals($product->name, $result->name);

    }
}
