<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('list_drivers', function (Blueprint $table) {
            $table->id();
            $table->char('name', 64)->comment('Данные о водителе');
            $table->integer('status')->default(0)->comment('Статус: 0 - не управляет / 1 - управляет');
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
        Schema::dropIfExists('list_drivers');
    }
}
