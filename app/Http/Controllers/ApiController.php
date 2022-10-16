<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

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

        // валидация
        $validator = Validator::make($r->all(), [
            'car_id' => 'required|integer|exists:App\Models\Car,id',
            'driver_id' => 'required|integer|exists:App\Models\Driver,id',
        ]);
        // проверка переданных параметров на валидность
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // получение модели автомобиля
        $car = Car::find($r->car_id);
        // проверка статуса автомобиля: 0 - свободен / 1 - занят
        if ($car->status === 1) {
            return response()->json(['message' => 'Автомобиль уже используется'], 200);
        }

        // получение моедли водителя
        $driver = Driver::find($r->driver_id);
        // проверка статуса водителя: 0 - не управляет / 1 - управляет
        if ($driver->status === 1) {
            return response()->json(['message' => 'Водитель уже управляет автомобилем'], 200);
        }

        // создание записи поездки
        $driving = Driving::create($r->all());

        // обновление статуса автомобиля: 1 - занят
        $car->update(['status' => 1]);
        // обновление статуса водителя: 1 - управляет
        $driver->update(['status' => 1]);

        // возвращает идентификатор поездки
        return response()->json(['driving_id' => $driving->id], 201);

    }

    /**
     * Завершение поездки (изменение статуса поездки).
     *
     * @param Request $r PUT-параметры: "driving_id"
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Request $r)
    {

        // валидация
        $validator = Validator::make($r->all(), [
            'driving_id' => 'required|integer|exists:App\Models\Driving,id',
        ]);
        // проверка переданных параметров на валидность
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // получение модели поездки
        $driving = Driving::find($r->driving_id);
        // проверка статуса поездки: 0 - продолжается / 1 - завершена
        if ($driving->status === 1) {
            return response()->json(['message' => 'Поездка больше недоступна для изменения'], 200);
        }

        // получение модели автомобиля, который участвует в поездке
        $car = Car::find($driving->car_id);
        // получение модели водителя, который участвует в поездке
        $driver = Driver::find($driving->driver_id);

        // обновление статуса автомобиля: 0 - свободен
        $car->update(['status' => 0]);
        // обновление статуса водителя: 0 - не управляет
        $driver->update(['status' => 0]);
        // обновление статуса поездки: 1 - завершена
        $driving->update(['status' => 1]);

        // возвращает сообщение о завершении поездки
        return response()->json(['message' => 'Поездка завершена'], 202);

    }

}
