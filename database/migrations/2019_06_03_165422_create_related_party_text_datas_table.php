<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatedPartyTextDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_party_text_datas', function (Blueprint $table) {
            $table->increments('id');
            $table->text('data')->nullable();
            $table->integer('related_party_text_id');
            $table->integer('related_party_id');
            $table->integer('client_id');
            $table->integer('user_id');
            $table->integer('duration');
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
        Schema::dropIfExists('related_party_text_datas');
    }
}
