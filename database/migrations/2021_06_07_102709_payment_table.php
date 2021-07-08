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
            $table->string('reference');
            $table->unsignedInteger('promocode')->nullable();
            $table->string('identification')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('reference_sale')->nullable();
            $table->string('state_pol')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_method_type')->nullable();
            $table->string('installments_number')->nullable();
            $table->string('transaction_date')->nullable();
            $table->string('cus')->nullable();
            $table->string('pse_bank')->nullable();
            $table->string('authorization_code')->nullable();
            $table->string('ip')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('payment_method_name')->nullable();
            $table->string('value')->nullable();
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
