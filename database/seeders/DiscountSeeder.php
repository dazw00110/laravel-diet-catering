<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class DiscountSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        foreach (range(1, 10) as $i) {
            DB::table('discounts')->insert([
                'code' => strtoupper(Str::random(6)),
                'value' => $faker->randomFloat(2, 5, 50),
                'type' => $faker->randomElement(['percentage', 'fixed']),
                'expires_at' => $faker->dateTimeBetween('now', '+1 month'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
