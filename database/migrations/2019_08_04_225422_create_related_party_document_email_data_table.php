<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatedPartyDocumentEmailDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_party_document_email_data', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('document_id')->unsigned();
            $table->string('email')->nullable();
            $table->integer('related_party_document_email_id')->unsigned();
            $table->integer('client_id')->unsigned();
            $table->integer('related_party_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('duration')->unsigned();
            $table->timestamps();

            /*$table->foreign('document_id')->references('id')->on('documents');
            $table->foreign('related_party_document_email_id','ade_id')->references('id')->on('related_party_document_emails');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('user_id')->references('id')->on('users');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('related_party_document_email_data');
    }
}
