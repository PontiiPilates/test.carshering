<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Driving;

class DrivingCompleteTest extends TestCase
{

    /**
     * Тест завершения поездки.
     * Для успешного прохождения нужно чтобы:
     * - в базе данных была запись о начале поездки
     * - статус выбранной поездки был - "0"
     *
     * @return void
     */

    public function test_driving_complete()
    {
        // идентификатор выбранной поездки
        $driving_id = 1;

        $driving = Driving::find($driving_id);

        // симуляция данных для успешного прохождения теста
        if ($driving) {
            $driving->update(['status' => 0]);
        } else {
            $driving->create(['car_id' => 1, 'driver_id' => 1, 'status' => 0]);
        }

        $response = $this->put("/api/driving/complete/$driving_id");

        $response->assertStatus(202);
        $response->assertJsonPath('message', $response->original['message']);
    }

}
