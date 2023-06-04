<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('log_id')->unsigned();
            $table->integer('activity_id')->unsigned();
            $table->string('activity_name');
            $table->string('old_value')->nullable();
            $table->string('new_value');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('activity_id')->references('id')->on('activities');
            $table->foreign('log_id')->references('id')->on('logs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
}
