<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
class ProductReviewSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $clients = DB::table('users')
            ->whereIn('user_type_id', function($q) {
                $q->select('id')->from('user_types')->where('name', 'client');
            })->pluck('id');

        $products = DB::table('products')->pluck('id');

        foreach (range(1, 500) as $i) {
            DB::table('product_reviews')->insert([
                'user_id' => $clients->random(),
                'product_id' => $products->random(),
                'rating' => rand(1, 5),
                'comment' => $faker->optional()->realText(60),
                'created_at' => now()->subDays(rand(0, 100)),
                'updated_at' => now(),
            ]);
        }
    }
}
