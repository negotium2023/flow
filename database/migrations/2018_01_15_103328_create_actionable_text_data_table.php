<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionableTextDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actionable_text_data', function (Blueprint $table) {
            $table->increments('id');
            $table->text('data')->nullable();
            $table->integer('actionable_text_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('duration')->unsigned();
            $table->timestamps();

            $table->foreign('actionable_text_id')->references('id')->on('actionable_texts');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actionable_text_data');
    }
}
