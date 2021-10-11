<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfessionalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('professional', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('type_document');
            $table->string('identification')->unique();
            $table->string('name');
            $table->string('lastname');
            $table->integer('age')->nullable();
            $table->string('address')->nullable();
            $table->string('phone');
            $table->string('phone_contact');
            $table->string('salary');
            $table->string('email')->unique();
            $table->date('admission_date');
            $table->date('retirement_date')->nullable();
            $table->unsignedInteger('position');
            $table->unsignedInteger('status');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE professional ADD photo LONGBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('professional');
    }
}
