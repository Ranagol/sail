<?php

namespace App\Services;

/**
 * Source here: https://www.youtube.com/watch?v=5XywKLjCD3g
 * Watched and followed the tut till 16:26 of 36:03.
 * Very good tutorial.
 */
class CheckoutService
{
    private array $pricingRules;

    public float $total = 0;

    /**
     * Items are string, the unique identifier of the product.
     * FR1, SR1, CF1
     *
     * @var array
     */
    private array $items = [];

    public function __construct(array $pricingRules)
    {
        $this->pricingRules = $pricingRules;
    }

    public function scan(string $item): void
    {
        //Find the product in the db
        $product = Product::where('product_code', $item)->first();

        if($product){
            Cart::create([
                'product_id' => $product->id,
                'price' => $product->price
            ]);

        }
    }

    public function getTotal(): float
    {
        return $this->total;
    }
}