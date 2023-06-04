<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelatedPartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('related_parties', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('related_party_parent_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('id_number')->nullable();
            $table->string('company')->nullable();
            $table->string('company_registration_number')->nullable();
            $table->string('email');
            $table->string('contact')->nullable();
            $table->string('cif_code')->nullable();//Remember to limit from the front-end

            $table->integer('referrer_id')->unsigned()->nullable();
            $table->integer('introducer_id')->unsigned();
            $table->integer('office_id')->unsigned();
            $table->integer('process_id')->unsigned();
            $table->integer('step_id')->unsigned()->default(1);

            $table->dateTime('completed_at')->nullable();
            $table->boolean('is_progressing')->default(true);
            $table->dateTime('not_progressing_date')->nullable();
            $table->boolean('needs_approval')->default(false);

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
        Schema::dropIfExists('related_parties');
    }
}
