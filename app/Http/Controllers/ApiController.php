<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Car;
use App\Models\Driver;
use App\Models\Driving;

class ApiController extends Controller
{

    /**
     * Добавление записи о начале поездки.
     *
     * @param Request $r POST-параметры: "car_id", "driver_id"
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function create(Request $r)
    {

        /**
         * Validation code in processed ...
         */

        // попытка получения модели автомобиля: иначе 404
        $car = Car::findOrFail($r->car_id);
        // попытка получения моедли водителя: иначе 404
        $driver = Driver::findOrFail($r->driver_id);

        // проверка статуса автомобиля: 0 - свободен / 1 - занят
        if ($car->status === 1) {
            return response()->json(['message' => 'Автомобиль уже используется'], 200);
        }
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
        return response()->json(['driving_id' => $driving->id], 201);

    }

    /**
     * Обновление статуса поездки.
     *
     * @param int $driving_id идентификатор поездки
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function complete($driving_id)
    {

        /**
         * Validation code in processed ...
         */

        // попытка получения модели поездки: иначе 404
        $driving = Driving::findOrFail($driving_id);

        // проверка статуса поездки: 0 - продолжается / 1 - завершена
        if ($driving->status === 1) {
            return response()->json(['message' => 'Поездка больше недоступна для изменения'], 200);
        }

        // получение модели автомобиля, который участвует в поездке
        $car = Car::find($driving->car_id);
        // получение модели водителя, который участвует в поездке
        $driver = Driver::find($driving->driver_id);

        // обновление статуса автомобиля: свободен
        $car->update(['status' => 0]);
        // обновление статуса водителя: не управляет
        $driver->update(['status' => 0]);
        // обновление статуса поездки: завершена
        $driving->update(['status' => 1]);

        // возвращает сообщение о завершении поездки
        return response()->json(['message' => 'Поездка завершена'], 202);

    }

}
