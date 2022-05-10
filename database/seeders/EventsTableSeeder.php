<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Event::truncate();

        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            Event::create([
                'title' => $faker->word,
                'location' => $faker->state,
                'event_long' => $faker->longitude($min = -180, $max = 180),
                'event_lat' => $faker->latitude($min = -90, $max = 90) 
            ]);
        }
    }
}
