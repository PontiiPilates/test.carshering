<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Car;
use App\Models\Driver;
use App\Models\Driving;

class ApiControllerTest extends TestCase
{

    /**
     * Проверка работы валидации передаваемых данных.
     *
     * @return void
     */
    public function test_create_check_validation()
    {

        // симуляция несуществующего идентификатора автомобиля
        $cars = Car::count();
        $car_id = $cars + 1;

        // симуляция несуществующего идентификатора водителя
        $drivers = Driver::count();
        $driver_id = $drivers + 1;

        // отправка запроса с заведомо некорректными данными
        $response = $this->post('/api/create', [

            'car_id' => null,               // отсутствующее значение
            // 'car_id' => 'string',           // тип данных - строка
            // 'car_id' => $car_id,            // несуществующий идентификатор

            'driver_id' => null,            // отсутствующее значение
            // 'driver_id' => 'string',        // тип данных - строка
            // 'driver_id' => $driver_id,      // несуществующий идентификатор

        ]);

        $response->assertJsonStructure(['errors']);
        $response->assertStatus(400);

    }

    /**
     * Проверка работы проверки статуса автомобиля.
     *
     * @return void
     */
    public function test_create_check_status_car()
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
    public function test_create_check_status_driver()
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
     * Проверка работы создания записи о начале поездки.
     *
     * @return void
     */
    public function test_create_success()
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

        // получение идентификатора созданной поездки
        $driving_id = $response->original['driving_id'];

        // удаление симулированных данных
        Car::find($car->id)->delete();
        Driver::find($driver->id)->delete();
        Driving::find($driving_id)->delete();

        $response->assertStatus(201);
        $response->assertJsonPath('driving_id', $driving_id);

    }

    /**
     * Проверка работы валидации передаваемых данных.
     *
     * @return void
     */
    public function test_complete_check_validation()
    {

        // симуляция несуществующего идентификатора поездки
        $drivings = Driving::count();
        $driving_id = $drivings + 1;

        // отправка запроса с заведомо некорректными данными
        $response = $this->put('/api/complete', [

            'driving_id' => null,               // отсутствующее значение
            // 'driver_id' => 'string',         // тип данных - строка
            // 'driver_id' => $driving_id,      // несуществующий идентификатор

        ]);

        $response->assertStatus(400);
        $response->assertJsonStructure(['errors']);

    }

    /**
     * Проверка работы проверки статуса поездки.
     *
     * @return void
     */
    public function test_complete_check_status_driving()
    {

        // симуляция автомобиля
        $car = Car::create(['name' => 'Land Rover Discovery', 'status' => 0,]);
        // симуляция водителя
        $driver = Driver::create(['name' => 'Чарльз Дарвин', 'status' => 0,]);
        // симуляция поездки, недоступной для изменения
        $driving = Driving::create(['car_id' => $car->id, 'driver_id' => $driver->id, 'status' => 1]);

        // отправка запроса
        $response = $this->put('/api/complete', [
            'driving_id' => $driving->id,
        ]);

        // удаление симулированных данных
        Car::find($car->id)->delete();
        Driver::find($driver->id)->delete();
        Driving::find($driving->id)->delete();

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);

    }

    /**
     * Проверка работы завершения поездки.
     *
     * @return void
     */
    public function test_complete_success()
    {

        // симуляция недоступного автомобиля
        $car = Car::create(['name' => 'Land Rover Discovery', 'status' => 1,]);
        // симуляция недоступного водителя
        $driver = Driver::create(['name' => 'Чарльз Дарвин', 'status' => 1,]);
        // симуляция поездки, доступной для завершения
        $driving = Driving::create(['car_id' => $car->id, 'driver_id' => $driver->id, 'status' => 0]);

        // отправка запроса
        $response = $this->put('/api/complete', [
            'driving_id' => $driving->id,
        ]);

        // удаление симулированных данных
        Car::find($car->id)->delete();
        Driver::find($driver->id)->delete();
        Driving::find($driving->id)->delete();

        $response->assertStatus(202);
        $response->assertJsonStructure(['message']);

    }

}
