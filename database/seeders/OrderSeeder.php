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
            ->whereIn('user_type_id', function ($query) {
                $query->select('id')->from('user_types')->where('name', 'client');
            })->pluck('id');

        $products = DB::table('products')->get();
        $discounts = DB::table('discounts')->pluck('code');

        $statuses = [
            'completed' => 25,
            'in_progress' => 20,
            'unordered' => 30,
            'cancelled' => 10,
        ];

        foreach ($statuses as $status => $count) {
            for ($i = 0; $i < $count; $i++) {
                $userId = $clients->random();
                $days = $faker->numberBetween(5, 14);
                $startDate = $faker->dateTimeBetween('-3 months', 'now');
                $endDate = (clone $startDate)->modify("+{$days} days");

                $orderId = DB::table('orders')->insertGetId([
                    'user_id'           => $userId,
                    'total_price'       => 0,
                    'status'            => $status,
                    'start_date'        => $startDate,
                    'end_date'          => $endDate,
                    'city'              => $faker->city,
                    'postal_code'       => $faker->postcode,
                    'street'            => $faker->streetName,
                    'apartment_number'  => $faker->buildingNumber,
                    'discount_code'     => $faker->optional(0.3)->randomElement($discounts),
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                $total = 0;
                $itemCount = min(rand(1, 5), $products->count());

                foreach ($products->random($itemCount) as $product) {
                    $qty = $faker->numberBetween(1, 4);
                    $price = $product->price;

                    DB::table('order_items')->insert([
                        'order_id'   => $orderId,
                        'product_id' => $product->id,
                        'quantity'   => $qty,
                        'unit_price' => $price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $total += $qty * $price * $days;
                }

                DB::table('orders')->where('id', $orderId)->update([
                    'total_price' => round($total, 2),
                ]);
            }
        }
    }
}
