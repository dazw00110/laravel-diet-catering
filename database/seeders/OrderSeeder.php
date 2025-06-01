<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $clients = DB::table('users')
            ->whereIn('user_type_id', function($query) {
                $query->select('id')->from('user_types')->where('name', 'client');
            })->pluck('id');

        $products = DB::table('products')->get();

        foreach (range(1, 15) as $i) {
            $userId = $clients->random();
            $startDate = $faker->dateTimeBetween('-1 month', '+1 week');
            $endDate = (clone $startDate)->modify('+7 days');

            $orderId = DB::table('orders')->insertGetId([
                'user_id' => $userId,
                'total_price' => 0,
                'status' => $faker->randomElement(['pending', 'confirmed']),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $total = 0;

            foreach ($products->random(rand(1, 3)) as $product) {
                $qty = rand(1, 2);
                $price = $product->price;

                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $total += $qty * $price;
            }

            DB::table('orders')->where('id', $orderId)->update(['total_price' => $total]);
        }
    }
}
