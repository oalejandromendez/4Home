<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('type_document')->nullable();
            $table->string('identification')->unique()->nullable();
            $table->string('name');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->integer('age')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('address')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('password');
            $table->unsignedInteger('customer_type')->nullable();
            $table->unsignedInteger('reset_password')->nullable();
            $table->integer('status');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
