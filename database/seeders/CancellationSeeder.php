<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CancellationSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $orders = DB::table('orders')
            ->where('status', 'cancelled')
            ->inRandomOrder()
            ->limit(10)
            ->get(['id', 'start_date', 'end_date']);

        $discounts = DB::table('discounts')->pluck('id');

        foreach ($orders as $order) {
            $cancelDate = $faker->dateTimeBetween(
                $order->start_date,
                (new \DateTime($order->end_date))->modify('-1 day')
            )->format('Y-m-d');

            DB::table('cancellations')->insert([
                'order_id' => $order->id,
                'reason' => $faker->realText(50),
                'discount_id' => $faker->optional()->randomElement($discounts),
                'cancellation_date' => $cancelDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
