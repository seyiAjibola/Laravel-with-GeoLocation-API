<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PromoCode;

class PromoCodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //PromoCode::truncate();

        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            PromoCode::create([
                'code_value' => $faker->randomNumber($nbDigits = NULL, $strict = false),
                'max_rides' => $faker->randomDigit,
                'radius' => 300,
                'expiry_date' => $faker->date($format = 'Y-m-d', $max = '2022-07-01'),
                'event_id' => rand(1,10)
            ]);
        }
    }
}
