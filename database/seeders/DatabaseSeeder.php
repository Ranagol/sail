<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Product::factory()->create(
            [
                'product_code' => 'FR1',
                'name' => 'Fruit Tea',
                'price' => 3.11,
            ]
        );

        Product::factory()->create(
            [
                'product_code' => 'SR1',
                'name' => 'Strawberries',
                'price' => 5.00,
            ]
        );

        Product::factory()->create(
            [
                'product_code' => 'CF1',
                'name' => 'Coffee',
                'price' => 11.23,
            ]
        );
    }
}
