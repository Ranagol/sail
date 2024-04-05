<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * These are the pricing rules that have to be applied.
     * Every rule has 3 elements.
     * 1. The name of the rule
     * 2. The quantity of the products that have to be bought to get the discount
     * 3. The new price after the quantity discount
     */
    public array $pricingRules = [
        //If the customer buys one FR1, will get another FR1 for free
        'FR1' => ['getOneFree', null, null],
        //If customer buys 3 pieces of SR1, the price will be 4.50 instead of 5.00
        'SR1' => ['bulkDiscount', 3, 4.50],
        // 'CF1' => 'priceDrop'
    ];

    public function setUp(): void
    {
        parent::setUp();
        //Here we do full seeding of the database
        Artisan::call('db:seed');
    }

    public function testCheckout1(): void
    {
        /**
         * These are the items that have to be scanned judgging by the task list:
         * FR1, SR1, FR1, FR1, CF1
         * 
         * Notice, that we passed here the $pricingRules array as an argument to the CheckoutService.
         */
        $checkout = new CheckoutService($this->pricingRules);
        $checkout->scan('FR1');
        $checkout->scan('SR1');
        $checkout->scan('FR1');
        $checkout->scan('FR1');
        $checkout->scan('CF1');

        $expectedTotal = 22.45;
        $this->assertEquals($expectedTotal, $checkout->getTotal());
    }

    /**
     * This is the second test case that has to be checked.
     * FR1, FR1
     *
     * @return void
     */
    public function testCheckout2(): void
    {
        $checkout = new CheckoutService($this->pricingRules);
        $checkout->scan('FR1');
        $checkout->scan('FR1');

        $expectedTotal = 3.11;
        $this->assertEquals($expectedTotal, $checkout->getTotal());
    }

    /**
     * This is the third test case that has to be checked.
     * SR1, SR1, FR1, SR1
     *
     * @return void
     */
    public function testCheckout3(): void
    {
        $checkout = new CheckoutService($this->pricingRules);
        $checkout->scan('SR1');
        $checkout->scan('SR1');
        $checkout->scan('FR1');
        $checkout->scan('SR1');

        $expectedTotal = 16.61;
        $this->assertEquals($expectedTotal, $checkout->getTotal());
    }
}
