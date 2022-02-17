<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReserveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserve', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->unsignedInteger('user');
            $table->unsignedInteger('customer_address');
            $table->unsignedInteger('service');
            $table->integer('type');
            $table->integer('status');
            $table->unsignedInteger('professional')->nullable();
            $table->unsignedInteger('supervisor')->nullable();
            $table->date('initial_service_date')->nullable();
            $table->timestamp('scheduling_date')->nullable();
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
        Schema::dropIfExists('reserve');
    }
}
