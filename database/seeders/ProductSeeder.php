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

        $adjectives = [
            // Styl życia / aktywność
            'sportowa', 'piłkarska', 'kulturystyczna', 'biegowa', 'crossfitowa',
            'fitnessowa', 'rowerowa', 'taneczna', 'wspinaczkowa', 'jogowa',

            // Cel diety
            'redukująca', 'masowa', 'detoks', 'oczyszczająca', 'wysokobiałkowa',
            'niskowęglowodanowa', 'zbilansowana', 'wysokokaloryczna', 'wegańska', 'wegetariańska',

            // Tempo / pora / dostępność
            'szybka', 'ekspresowa', 'codzienna', 'weekendowa', 'poranna', 'nocna',
            'na wynos', 'na wynos plus', 'na mieście', 'biurowa', 'domowa',

            // Styl / moda / target
            'klasyczna', 'premium', 'ekskluzywna', 'ekonomiczna', 'młodzieżowa',
            'aktywnych mam', 'studentów', 'seniorów', 'biznesowa', 'rodzinna',

            // Smaki / kuchnie
            'śródziemnomorska', 'japońska', 'tajska', 'indyjska', 'amerykańska',
            'meksykańska', 'polska', 'francuska', 'arabska', 'fusion',

            // Kuchnie świata
            'włoska', 'francuska', 'hiszpańska', 'grecka', 'turecka',
            'chińska', 'japońska', 'wietnamska', 'tajska', 'indyjska',
            'meksykańska', 'amerykańska', 'brazylijska', 'arabska', 'ukraińska',
            'niemiecka', 'czeska', 'polska', 'skandynawska', 'afrykańska',
            'izraelska', 'perska', 'koreańska', 'mongolska', 'gruzińska',
            'portugalska', 'marokańska', 'filipińska', 'indonezyjska', 'australijska'
        ];


        foreach (range(1, 20) as $i) {
            $isVegan = $faker->boolean(30);
            $isVegetarian = $isVegan ? true : $faker->boolean(60);

            DB::table('products')->insert([
                'name' => 'Dieta ' . $faker->randomElement($adjectives),
                'description' => $faker->sentence(12),
                'price' => $faker->randomFloat(2, 20, 100),
                'calories' => $faker->numberBetween(1200, 3500),
                'is_active' => $faker->boolean(90),
                'is_vegan' => $isVegan,
                'is_vegetarian' => $isVegetarian,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
