<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DrivingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// получение имеющихся данных
Route::get('/drivings', [DrivingController::class, 'getDrivings']);

// создание новой поездки
Route::post('/driving/create', [DrivingController::class, 'drivingCreate']);

// завершение поездки
Route::put('/driving/complete/{driving_id}', [DrivingController::class, 'drivingComplete']);
