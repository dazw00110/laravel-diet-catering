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

        // 1 admin
        DB::table('users')->insert([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
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

        // 3 pracowników
        for ($i = 1; $i <= 3; $i++) {
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

        // 15 klientów
        for ($i = 1; $i <= 15; $i++) {
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
          DB::table('users')->insert([
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@demo.pl',
                'password' => Hash::make('admin123'),
                'birth_date' => '1990-01-01',
                'user_type_id' => 1, // admin
                'is_verified' => true,
                'is_vegan' => false,
                'is_vegetarian' => false,
                'avatar_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Staff',
                'last_name' => 'User',
                'email' => 'staff@demo.pl',
                'password' => Hash::make('staff123'),
                'birth_date' => '1992-01-01',
                'user_type_id' => 3, // staff
                'is_verified' => true,
                'is_vegan' => false,
                'is_vegetarian' => false,
                'avatar_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Client',
                'last_name' => 'User',
                'email' => 'client@demo.pl',
                'password' => Hash::make('client123'),
                'birth_date' => '1995-01-01',
                'user_type_id' => 2, // client
                'is_verified' => true,
                'is_vegan' => false,
                'is_vegetarian' => false,
                'avatar_url' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        /*
        |--------------------------------------------------------------------------
        | Loginy testowe użytkowników
        |--------------------------------------------------------------------------
        | Dodane w UserSeeder.php:
        | - Admin:    email: admin@demo.pl,  hasło: admin123
        | - Pracownik: email: staff@demo.pl, hasło: staff123
        | - Klient:   email: client@demo.pl, hasło: client123
        */

    }
}
