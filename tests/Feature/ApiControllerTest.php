<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Car;
use App\Models\Driver;

class ApiControllerTest extends TestCase
{

    /**
     * Проверка выдачи 404 статуса при отсутсвии запрашиваемого автомобиля в таблице.
     *
     * @return void
     */

    public function test_car_is_missing()
    {

        // симуляция несуществующего идентификатора автомобиля
        $cars = Car::count();
        $car_id = $cars++;

        // идентификатор водителя будет настоящим
        $driver_id = Driver::first()->id;

        // отправка запроса
        $response = $this->post('/api/create', [
            'car_id' => $car_id,
            'driver_id' => $driver_id,
        ]);

        $response->assertStatus(404);

    }

    /**
     * Проверка выдачи 404 статуса при отсутствии запрашиваемого водителя в таблице.
     *
     * @return void
     */

    public function test_driver_is_missing()
    {

        // симуляция несуществующего идентификатора водителя
        $drivers = Driver::count();
        $driver_id = $drivers++;

        // идентификатор автомобиля будет настоящим
        $car_id = Car::first()->id;

        // отправка запроса
        $response = $this->post('/api/create', [
            'car_id' => $car_id,
            'driver_id' => $driver_id,
        ]);

        $response->assertStatus(404);

    }

    /**
     * Проверка работы проверки статуса автомобиля.
     *
     * @return void
     */

    public function test_car_status_check()
    {

        // симуляция недоступного автомобиля
        $car = Car::create(['name' => 'Land Rover Discovery', 'status' => 1,]);
        // симуляция доступного водителя
        $driver = Driver::create(['name' => 'Чарльз Дарвин', 'status' => 0,]);

        // отправка запроса
        $response = $this->post('/api/create', [
            'car_id' => $car->id,
            'driver_id' => $driver->id,
        ]);

        // удаление симулированных данных
        Car::find($car->id)->delete();
        Driver::find($driver->id)->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);

    }

    /**
     * Проверка работы проверки статуса водителя.
     *
     * @return void
     */

    public function test_driver_status_check()
    {

        // симуляция доступного автомобиля
        $car = Car::create(['name' => 'Land Rover Discovery', 'status' => 0,]);
        // симуляция недоступного водителя
        $driver = Driver::create(['name' => 'Чарльз Дарвин', 'status' => 1,]);

        // отправка запроса
        $response = $this->post('/api/create', [
            'car_id' => $car->id,
            'driver_id' => $driver->id,
        ]);

        // удаление симулированных данных
        Car::find($car->id)->delete();
        Driver::find($driver->id)->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);

    }

    /**
     * Проверка работы создания записи о начале поездки
     *
     * @return void
     */

    public function test_driving_create_success()
    {

        // симуляция доступного автомобиля
        $car = Car::create(['name' => 'Land Rover Discovery', 'status' => 0,]);
        // симуляция доступного водителя
        $driver = Driver::create(['name' => 'Чарльз Дарвин', 'status' => 0,]);

        // отправка запроса
        $response = $this->post('/api/create', [
            'car_id' => $car->id,
            'driver_id' => $driver->id,
        ]);

        // удаление симулированных данных
        Car::find($car->id)->delete();
        Driver::find($driver->id)->delete();

        // получение идентификатора созданной поездки
        $driving_id = $response->original['driving_id'];

        $response->assertStatus(201);
        $response->assertJsonPath('driving_id', $driving_id);

    }

    /**
     * Tests method complete in processed
     */

}
