<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionableNotificationDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actionable_notification_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('actionable_notification_id')->unsigned();
            $table->integer('notification_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('duration')->unsigned();
            $table->timestamps();

            $table->foreign('actionable_notification_id')->references('id')->on('actionable_notifications');
            $table->foreign('notification_id')->references('id')->on('notifications');
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
        Schema::dropIfExists('actionable_notification_data');
    }
}
