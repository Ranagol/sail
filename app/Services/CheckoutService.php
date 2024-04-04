<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;

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

    public function scan(string $productCode): void
    {
        //Find the product in the db
        $product = Product::where('product_code', $productCode)->first();

        if($product){
            Cart::create([
                'product_id' => $product->id,
                'product_code' => $product->product_code,
                'price' => $product->price
            ]);

            dump(Cart::all()->toArray());

        }
    }

    public function getTotal(): float
    {
        $cartProducts = Cart::query()
            ->join('products', 'products.id', '=', 'carts.product_id')
            ->selectRaw('products.product_code, products.price, sum(carts.qty) as quantity')
            ->groupByRaw('products.product_code, products.price')
            ->get();
        
        // dd($cartProducts);

        $total = 0;

        foreach($cartProducts as $cartProduct){
            $total += $cartProduct->price * $cartProduct->quantity;
        }

        return $total; 
    }
}