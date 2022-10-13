<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    /**
     * Данные для наполнения таблицы list_cars
     *
     * @return array
     */

    public function definition()
    {

        return [
            'name' => $this->faker->randomElement([
                'Koenigseqq Gemera',
                'Maseratti Ghibli',
                'Ferrari Gran Turismo',
                'Bugatti Veiron',
                'Lamborghini Diablo',
                'Ford Mustang',
                'Porsche Carrera Rs',
                'Aston Martin DB9',
                'Bentley Continental GT',
                'Alfa Romeo Montreal'
            ]),
            'status' => $this->faker->numberBetween(0, 1)
        ];

    }

}
