<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('reserve');
            $table->integer('type');
            $table->string('name');
            $table->string('type_document');
            $table->string('document');
            $table->string('phone');
            $table->string('total');
            $table->string('authorizationCode')->nullable();
            $table->string('orderId')->nullable();
            $table->string('state');
            $table->string('trazabilityCode')->nullable();
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
        Schema::dropIfExists('payment');

    }
}
