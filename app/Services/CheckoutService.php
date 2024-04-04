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

            // dump(Cart::all()->toArray());
        }
    }

    /**
     * Calculate the total price of the cart. For all products in the cart.
     * With the pricing rules applied.
     * 
     * @return float
     */
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
            
            /**
             * Find a rule for a given product. Example, find the rule for FR1.
             * Example how one rule looks like:  'SR1' => ['bulkDiscount', 3, 4.50],
             */
            $rule = $this->pricingRules[$cartProduct->product_code] ?? null;

            //If there is a rule for the given situation, calculate total with applying the rule
            if(!is_null($rule)){

                /**
                 * This is tricky. Very.
                 * 
                 * $rule[0] is a variable that contains the name of the method to be called. The 
                 * syntax $this->{$rule[0]} is used to call the method whose name is stored in 
                 * $rule[0]. All in all, $this->{$rule[0]} is = $this->getOneFree() for example.
                 * 
                 * $rule[0] - the name of the rule
                 * $rule[1] - the quantity of the products that have to be bought to get the discount
                 * $rule[2] - the new price after the quantity discount
                 * 
                 */
                $total += $this->{$rule[0]}(
                    $cartProduct,
                    $rule[1],//The quantity of the products that have to be bought to get the discount
                    $rule[2]//The new price after the quantity discount
                );
            } else {
                //If there is no rule, calculate the total without applying any rule
                $total += $cartProduct->price * $cartProduct->quantity;
            }
        }

        return $total; 
    }

    /**
     * If the customer buys one FR1, will get another FR1 for free.
     *
     * @param [type] $product
     * @param [type] $quantity
     * @param [type] $newPrice
     * @return void
     */
    private function getOneFree($product, $quantity, $newPrice)
    {
        /**
         * With the 'getOneFree' the qty is the key, this is what we must calculate. The qty changes.
         * Because if the quantity is 2, customer pays for 1, with this logic below.
         * If the quantity is 3, customer pays for 2, with this logic below... And so on.
         * So, we must calculate the quantity that the customer pays for.
         */
        $quantity = floor($product->quantity / 2) + $product->quantity % 2;
        return $total = $product->price * $quantity;
    }

    /**
     * If customer buys 3 pieces of SR1, the price will be 4.50 instead of 5.00
     *
     * @param [type] $product
     * @param [type] $quantity
     * @param [type] $newPrice
     * @return void
     */
    private function bulkDiscount($product, $quantity, $newPrice)
    {
        //This is the original price of the product
        $price = $product->price;

        //If we have 3 or more strawberries, then the price is 4.50 instead of 5.00
        if($product->quantity >= $quantity){

            //This is the new price, with the bulk discount
            $price = $newPrice;
        }
        return $total = $price * $product->quantity;
    }


}