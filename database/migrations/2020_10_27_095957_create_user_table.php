<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id',5)->primary();
            $table->string('name',32);
            $table->string('username',32)->unique();
            $table->string('email',32)->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->tinyInteger('role');
            $table->foreignId('unit_id')->constrained();
            $table->string('password');
            $table->string('api_token')->nullable();
            $table->string('fcm_token')->nullable();
            $table->timestamps();

            // $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade')->onUpdate('cascade');
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
