<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('insurer')->nullable();
            $table->string('policy')->nullable();
            $table->double('upfront_revenue')->nullable();
            $table->double('ongoing_revenue')->nullable();
            $table->date('due_date')->nullable();
            $table->string('team_ids')->nullable();
            $table->integer('assignee_id')->nullable();
            $table->integer('status_id')->nullable();
            $table->integer('priority_id')->nullable();
            $table->integer('section_id')->nullable();
            $table->integer('dependency_id')->nullable();
            $table->text('description')->nullable();
            $table->string('assignee_name')->nullable();
            $table->text('team_names')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('client_name')->nullable();
            $table->integer('archived')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
}
