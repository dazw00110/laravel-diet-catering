<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $categories = DB::table('categories')->pluck('id');

        foreach (range(1, 20) as $i) {
            DB::table('products')->insert([
                'name' => 'Dieta ' . $faker->numberBetween(1200, 3500) . ' kcal',
                'description' => $faker->sentence(12),
                'price' => $faker->randomFloat(2, 20, 100),
                'calories' => $faker->numberBetween(1200, 3500),
                'category_id' => $categories->random(),
                'is_active' => $faker->boolean(90),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
