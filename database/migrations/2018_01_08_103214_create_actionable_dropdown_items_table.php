<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionableDropdownItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actionable_dropdown_items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('actionable_dropdown_id')->unsigned();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('actionable_dropdown_id')->references('id')->on('actionable_dropdowns');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actionable_dropdown_items');
    }
}
