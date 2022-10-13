<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Car;
use App\Models\Driver;
use App\Models\Driving;

class DrivingController extends Controller
{
    /**
     * Просто для просмотра и отладки
     */

    public function getLists()
    {

        // получение свободных автомобилей
        $cars = Car::where('status', 0)->get();

        // получение свободных водителей
        $drivers = Driver::where('status', 0)->get();

        // получение списка поездок
        $drivings = Driving::all();

        echo "Drivings \r\n";
        echo $drivings->toJson();

        echo "\r\nDrivers \r\n";
        echo $drivers->toJson();

        echo "\r\nCars \r\n";
        echo $cars->toJson();

    }

    /**
     * Добавление записи о начале поездки
     *
     * @param Request $r POST-параметры: "car_id", "driver_id"
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function drivingBegin(Request $r)
    {

        // попытка получения модели автомобиля: иначе 404
        $car = Car::findOrFail($r->car_id);

        // проверка статуса автомобиля: 0 - свободен / 1 - занят
        if ($car->status === 1) {
            return response()->json(['message' => 'Автомобиль уже занят'], 200);
        }

        // попытка получения моедли водителя: иначе 404
        $driver = Driver::findOrFail($r->driver_id);

        // проверка статуса водителя: 0 - не управляет / 1 - управляет
        if ($driver->status === 1) {
            return response()->json(['message' => 'Водитель уже управляет автомобилем'], 200);
        }

        // создание записи поездки
        $driving = Driving::create($r->all());

        // обновление статуса автомобиля: занят
        $car->update(['status' => 1]);

        // обновление статуса водителя: управляет
        $driver->update(['status' => 1]);

        // возвращает идентификатор поездки
        return response()->json(['usage_number' => $driving->id], 201);

    }

    /**
     * Обновление статуса поездки
     *
     * @param int $driving_id идентификатор поездки
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function drivingComplete($driving_id)
    {

        // попытка получения модели поездки: иначе 404
        $driving = Driving::findOrFail($driving_id);

        // получение модели автомобиля, который участвует в поездке
        $car = Car::find($driving->car_id);

        // получение модели водителя, который участвует в поездке
        $driver = Driver::find($driving->driver_id);

        // проверка статуса поездки: 0 - продолжается / 1 - завершена
        if ($driving->status === 1) {
            return response()->json(['message' => 'Поездка больше недоступна для изменения'], 200);
        }

        // обновление статуса автомобиля: свободен
        $car->update(['status' => 0]);

        // обновление статуса водителя: не управляет
        $driver->update(['status' => 0]);

        // обновление статуса поездки: завершена
        $driving->update(['status' => 1]);

        // возвращает сообщение о завершении поездки
        return response()->json(['message' => 'Поездка завершена'], 201);

    }
}
