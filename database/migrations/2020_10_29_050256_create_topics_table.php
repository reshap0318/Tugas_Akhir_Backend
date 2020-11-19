<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicsTable extends Migration
{
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->string('id',5)->primary();
            $table->string('name');
            $table->timestamps();

            // $table->foreignId('period_id')->constrained();
            // $table->foreign('period_id')->references('id')->on('periods')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('topics');
    }
}
