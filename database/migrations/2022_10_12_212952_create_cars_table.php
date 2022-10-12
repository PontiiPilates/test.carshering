<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_cars', function (Blueprint $table) {
            $table->id();
            $table->char('name', 32)->comment('Марка автомобиля');
            $table->integer('status')->default(0)->comment('Статус: 0 - автомобиль свободен / 1 - автомобиль занят');
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
        Schema::dropIfExists('list_cars');
    }
}
