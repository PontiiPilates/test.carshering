<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrivingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_drivings', function (Blueprint $table) {
            $table->id();
            $table->integer('car_id')->default(0)->comment('Идентификатор автомобиля');
            $table->integer('driver_id')->default(0)->comment('Идентификатор водителя');
            $table->integer('status')->default(0)->comment('Статус: 0 - продолжается / 1 - завершена');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('list_drivings');
    }
}
