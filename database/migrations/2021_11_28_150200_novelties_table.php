<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NoveltiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('novelties', function (Blueprint $table) {
            $table->id();
            $table->string('professional');
            $table->integer('type');
            $table->integer('status');
            $table->date('initial_date')->nullable();
            $table->date('final_date')->nullable();
            $table->string('ids_reserves')->nullable();
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
        Schema::dropIfExists('novelties');
    }
}
