<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CateringCalendarSeeder extends Seeder {
    public function run(): void
    {
        $orders = DB::table('orders')->get();

        foreach ($orders as $order) {
            $start = Carbon::parse($order->start_date);
            $end = Carbon::parse($order->end_date);

            for ($date = $start; $date->lte($end); $date->addDay()) {
                DB::table('catering_calendar')->insert([
                    'order_id' => $order->id,
                    'active_day' => $date->format('Y-m-d'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

