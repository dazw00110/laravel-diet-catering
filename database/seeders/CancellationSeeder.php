<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CancellationSeeder extends Seeder {
    public function run(): void
    {
        $faker = Faker::create();
        $orders = DB::table('orders')->inRandomOrder()->limit(10)->pluck('id');
        $discounts = DB::table('discounts')->pluck('id');

        foreach ($orders as $orderId) {
            DB::table('cancellations')->insert([
                'order_id' => $orderId,
                'reason' => $faker->realText(50),
                'discount_id' => $faker->optional()->randomElement($discounts),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}