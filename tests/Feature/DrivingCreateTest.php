<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Car;
use App\Models\Driver;

class DrivingCreateTest extends TestCase
{

    /**
     * Тест создания поездки.
     * Для успешного прохождения нужно чтобы:
     * - статус выбранного автомобиля был - "0"
     * - статус выбранного водителя был - "0"
     *
     * @return void
     */

    public function test_driving_create()
    {
        // данные для тела запроса
        $car_id = 1;
        $driver_id = 1;

        // симуляция данных для успешного прохождения теста
        Car::find($car_id)->update(['status' => 0]);            // 0 - свободен / 1 - занят
        Driver::find($driver_id)->update(['status' => 0]);      // 0 - не управляет / 1 - управляет

        $response = $this->post('/api/driving/create', [
            'car_id' => $car_id,
            'driver_id' => $driver_id,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('driving_number', $response->original['driving_number']);
    }

}
