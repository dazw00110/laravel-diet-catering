<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $clients = DB::table('users')
            ->whereIn('user_type_id', function($query) {
                $query->select('id')->from('user_types')->where('name', 'client');
            })->pluck('id');

        $products = DB::table('products')->pluck('id');

        foreach ($clients as $clientId) {
            foreach ($products->random(rand(1, 4)) as $productId) {
                DB::table('cart')->insert([
                    'user_id' => $clientId,
                    'product_id' => $productId,
                    'quantity' => rand(1, 3),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
