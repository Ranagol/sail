<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\CheckoutService;

class CheckoutTest extends TestCase
{
    public function testCheckout1(): void
    {

        $pricingRules = [
            //If the customer buys one FR1, will get another FR1 for free
            'FR1' => ['getOneFree'],
            //If customer buys 3 pieces of SR1, the price will be 4.50 instead of 5.00
            'SR1' => ['bulkDiscount', 3, 4.50],
            'CF1' => 'priceDrop'
        ];

        /**
         * These are the items that have to be scanned judgging by the task list.
         * FR1, SR1, FR1, FR1, CF1
         */
        $checkout = new CheckoutService($pricingRules);
        $checkout->scan('FR1');
        $checkout->scan('SR1');
        $checkout->scan('FR1');
        $checkout->scan('FR1');
        $checkout->scan('CF1');

        $expectedTotal = 22.45;
        $this->assertEquals($expectedTotal, $checkout->getTotal());
    }
}