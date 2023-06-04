<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_report', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('process_id');
            $table->integer('user_id')->unsigned();
            /*$table->integer('activity_id')->unsigned();*/
            $table->softDeletes();
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
        Schema::dropIfExists('custom_report');
    }
}
