<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PurchaseHistorySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $orders = DB::table('orders')->get();

        foreach ($orders as $order) {
            DB::table('purchase_history')->insert([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'amount_spent' => $order->total_price,
                'purchased_at' => $faker->dateTimeBetween($order->start_date, $order->end_date),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
