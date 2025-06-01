<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_types')->insert([
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'client'],
            ['id' => 3, 'name' => 'staff'],
        ]);
    }
}
