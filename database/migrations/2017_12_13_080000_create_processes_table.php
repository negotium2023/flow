<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('processes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('process_type_id')->default(1);
            $table->integer('global')->nullable();
            $table->string('not_started_colour',20);
            $table->string('started_colour',20);
            $table->string('completed_colour',20);
            $table->integer('office_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('office_id')->references('id')->on('offices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('processes');
    }
}
