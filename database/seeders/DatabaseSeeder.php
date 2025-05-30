<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserTypeSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            CartSeeder::class,
            PurchaseHistorySeeder::class,
            DiscountSeeder::class,
            CancellationSeeder::class,
            CateringCalendarSeeder::class,
            ProductReviewSeeder::class,
        ]);
    }
}
