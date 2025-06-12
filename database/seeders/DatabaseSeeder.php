<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// DatabaseSeeder
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
        UserTypeSeeder::class,
        UserSeeder::class,
        ProductSeeder::class,
        OrderSeeder::class,
        DiscountSeeder::class,
        CancellationSeeder::class,
        ProductReviewSeeder::class,

        // For cascading delete tests
        //TestCascadeDeleteSeeder::class,

    ]);

    }
}
