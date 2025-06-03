<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $userTypes = DB::table('user_types')->pluck('id', 'name');

        // Admin
        DB::table('users')->insert([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@demo.pl',
            'password' => Hash::make('admin123'),
            'birth_date' => '1990-01-01',
            'is_verified' => true,
            'user_type_id' => $userTypes['admin'],
            'is_vegan' => false,
            'is_vegetarian' => false,
            'avatar_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Staff
        DB::table('users')->insert([
            'first_name' => 'Staff',
            'last_name' => 'User',
            'email' => 'staff@demo.pl',
            'password' => Hash::make('staff123'),
            'birth_date' => '1992-01-01',
            'user_type_id' => $userTypes['staff'],
            'is_verified' => true,
            'is_vegan' => false,
            'is_vegetarian' => false,
            'avatar_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Demo Client
        DB::table('users')->insert([
            'first_name' => 'Client',
            'last_name' => 'User',
            'email' => 'client@demo.pl',
            'password' => Hash::make('client123'),
            'birth_date' => '1995-01-01',
            'user_type_id' => $userTypes['client'],
            'is_verified' => true,
            'is_vegan' => false,
            'is_vegetarian' => false,
            'avatar_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Staff x5
        for ($i = 1; $i <= 5; $i++) {
            DB::table('users')->insert([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => "staff{$i}@example.com",
                'password' => Hash::make('password'),
                'birth_date' => $faker->date(),
                'is_verified' => true,
                'user_type_id' => $userTypes['staff'],
                'is_vegan' => $faker->boolean,
                'is_vegetarian' => $faker->boolean,
                'avatar_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Clients x50
        for ($i = 1; $i <= 50; $i++) {
            DB::table('users')->insert([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => "client{$i}@example.com",
                'password' => Hash::make('password'),
                'birth_date' => $faker->date(),
                'is_verified' => true,
                'user_type_id' => $userTypes['client'],
                'is_vegan' => $faker->boolean,
                'is_vegetarian' => $faker->boolean,
                'avatar_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
