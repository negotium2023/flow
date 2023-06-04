<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('onboard_days')->nullable();
            $table->integer('onboards_per_day')->nullable();
            $table->integer('client_target_data')->nullable();
            $table->integer('client_converted')->nullable();
            $table->integer('client_conversion')->nullable();
            $table->boolean('message_subject')->nullable();
            $table->boolean('enable_support')->nullable();
            $table->string('support_email')->nullable();
            $table->string('allowed_email_domains')->nullable();
            $table->string('absolute_path')->nullable();
            $table->integer('dashboard_process')->nullable();
            $table->string('dashboard_regions')->nullable();
            $table->string('dashboard_avg_step')->nullable();
            $table->string('dashboard_outstanding_activities')->nullable();
            $table->integer('dashboard_outstanding_step')->nullable();
            $table->integer('dashboard_activities_step_for_age')->nullable();
            $table->integer('default_onboarding_process')->nullable();
            $table->integer('related_party_process')->nullable();
            $table->integer('action_threshold')->default(7)->nullable();
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
        Schema::dropIfExists('configs');
    }
}
