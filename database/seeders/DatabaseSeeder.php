<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Car;
use App\Models\Driver;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Car::factory(10)->create();
        Driver::factory(10)->create();
    }
}
