<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatedPartyDropdownDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_party_dropdown_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('related_party_dropdown_id');
            $table->integer('related_party_dropdown_item_id');
            $table->integer('client_id');
            $table->integer('user_id');
            $table->integer('duration');
            $table->integer('related_party_id');
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
        Schema::dropIfExists('related_party_dropdown_datas');
    }
}
