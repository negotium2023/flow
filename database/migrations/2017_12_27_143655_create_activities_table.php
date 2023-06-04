<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('order')->unsigned();
            $table->integer('actionable_id')->unsigned();
            $table->string('actionable_type');
            $table->boolean('kpi')->default(true);
            $table->boolean('client_activity')->default(false);
            $table->integer('step_id')->unsigned();
            $table->integer('threshold')->default(604800);
            $table->integer('weight')->default(100);
            $table->string('marker')->nullable();
            $table->integer('dependant_activity_id')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('comment')->unsigned()->nullable();
            $table->integer('value')->unsigned()->nullable();
            $table->integer('procedure')->unsigned()->nullable();
            $table->integer('grouped')->unsigned()->default(0);
            $table->integer('grouping')->unsigned()->default(0);
            $table->text('default_value')->nullable();
            $table->text('tooltip')->nullable();
            $table->tinyInteger('show_tooltip')->nullable();
            $table->tinyInteger('report')->nullable();
            $table->tinyInteger('client_bucket')->nullable();
            $table->string('position')->nullable();
            $table->integer('level')->nullable();
            $table->string('color')->nullable();
            $table->integer('future_date')->nullable();
            $table->text('text_content')->nullable();
            $table->string('height')->nullable();
            $table->string('width')->nullable();
            $table->string('alignment')->nullable();
            $table->integer('multiple_selection')->nullable();

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('step_id')->references('id')->on('steps');
            $table->foreign('dependant_activity_id')->references('id')->on('activities');
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
        Schema::dropIfExists('activities');
    }
}
