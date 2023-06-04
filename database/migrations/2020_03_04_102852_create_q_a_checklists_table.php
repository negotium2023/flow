<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQAChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('q_a_checklists', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('client_id');
            $table->boolean('strapline')->nullable();
            $table->boolean('footer_updated')->nullable();
            $table->boolean('correct_me_listed')->nullable();
            $table->boolean('page_numbers_updated')->nullable();
            $table->boolean('family_tree')->nullable();
            $table->boolean('all_rp_included')->nullable();
            $table->boolean('client_exposure_not_identified_correctly')->nullable();
            $table->boolean('client_information')->nullable();
            $table->boolean('relationship')->nullable();
            $table->boolean('kyc_date')->nullable();
            $table->boolean('casa')->nullable();
            $table->boolean('pep')->nullable();
            $table->boolean('sanctions')->nullable();
            $table->boolean('str')->nullable();
            $table->boolean('litigation')->nullable();
            $table->boolean('adverse_media')->nullable();
            $table->boolean('v5_standard')->nullable();
            $table->boolean('all_products_included')->nullable();
            $table->boolean('linked_accounts_included')->nullable();
            $table->boolean('wimi_wfs_account_listed')->nullable();
            $table->boolean('email_sent_cib_wimi_wfs_clients')->nullable();
            $table->boolean('all_info_included')->nullable();
            $table->boolean('review_date_correct')->nullable();
            $table->boolean('expected_account_activity')->nullable();
            $table->boolean('ta_has_conclusion')->nullable();
            $table->boolean('listed_in_chronological_order')->nullable();
            $table->boolean('eb_exact_extract_from_article')->nullable();
            $table->boolean('rb_summary_article')->nullable();
            $table->boolean('does_it_align_with_background_ta')->nullable();
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
        Schema::dropIfExists('q_a_checklists');
    }
}
