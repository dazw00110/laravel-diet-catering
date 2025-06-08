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

        // Wyczyść tabele
        DB::table('discount_user')->truncate();
        DB::table('discounts')->truncate();

        // Stwórz 10 zniżek
        $discountIds = [];
        foreach (range(1, 10) as $i) {
            $discountIds[] = DB::table('discounts')->insertGetId([
                'code' => strtoupper(Str::random(6)),
                'value' => $faker->randomFloat(2, 5, 30),
                'type' => $faker->randomElement(['percentage', 'fixed']),
                'expires_at' => $faker->dateTimeBetween('now', '+2 months'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Przypisz po 3 losowe zniżki do każdego użytkownika
        $users = DB::table('users')->pluck('id');
        foreach ($users as $userId) {
            foreach (collect($discountIds)->random(3) as $discountId) {
                DB::table('discount_user')->insert([
                    'discount_id' => $discountId,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
