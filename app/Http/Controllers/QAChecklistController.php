<?php

namespace App\Http\Controllers;

use App\QAChecklist;

class QAChecklistController extends Controller
{
    public function create($id)
    {
        $parameters = [
            'client_id' => $id,
            'qa_checklist' => QAChecklist::whereClientId($id)->first()
        ];
        return view('clients.qa-checklist.create')->with($parameters);
    }

    public function store($id)
    {
        $QAChecklist = new QAChecklist();
        $QAChecklist->client_id = $this->valueHelper($id);
        $QAChecklist->strapline = $this->valueHelper(request()->strapline);
        $QAChecklist->footer_updated = $this->valueHelper(request()->footer_updated);
        $QAChecklist->correct_me_listed = $this->valueHelper(request()->correct_me_listed);
        $QAChecklist->page_numbers_updated = $this->valueHelper(request()->page_numbers_updated);
        $QAChecklist->family_tree = $this->valueHelper(request()->family_tree);
        $QAChecklist->all_rp_included = $this->valueHelper(request()->all_rp_included);
        $QAChecklist->client_exposure_not_identified_correctly = $this->valueHelper(request()->client_exposure_not_identified_correctly);
        $QAChecklist->client_information = $this->valueHelper(request()->client_information);
        $QAChecklist->relationship = $this->valueHelper(request()->relationship);
        $QAChecklist->kyc_date = $this->valueHelper(request()->kyc_date);
        $QAChecklist->casa = $this->valueHelper(request()->casa);
        $QAChecklist->pep = $this->valueHelper(request()->pep);
        $QAChecklist->sanctions = $this->valueHelper(request()->sanctions);
        $QAChecklist->str = $this->valueHelper(request()->str);
        $QAChecklist->litigation = $this->valueHelper(request()->litigation);
        $QAChecklist->adverse_media = $this->valueHelper(request()->adverse_media);
        $QAChecklist->v5_standard = $this->valueHelper(request()->v5_standard);
        $QAChecklist->all_products_included = $this->valueHelper(request()->all_products_included);
        $QAChecklist->linked_accounts_included = $this->valueHelper(request()->linked_accounts_included);
        $QAChecklist->wimi_wfs_account_listed = $this->valueHelper(request()->wimi_wfs_account_listed);
        $QAChecklist->email_sent_cib_wimi_wfs_clients = $this->valueHelper(request()->email_sent_cib_wimi_wfs_clients);
        $QAChecklist->all_info_included = $this->valueHelper(request()->all_info_included);
        $QAChecklist->review_date_correct = $this->valueHelper(request()->review_date_correct);
        $QAChecklist->expected_account_activity = $this->valueHelper(request()->expected_account_activity);
        $QAChecklist->ta_has_conclusion = $this->valueHelper(request()->ta_has_conclusion);
        $QAChecklist->listed_in_chronological_order = $this->valueHelper(request()->listed_in_chronological_order);
        $QAChecklist->eb_exact_extract_from_article = $this->valueHelper(request()->eb_exact_extract_from_article);
        $QAChecklist->rb_summary_article = $this->valueHelper(request()->rb_summary_article);
        $QAChecklist->does_it_align_with_background_ta = $this->valueHelper(request()->does_it_align_with_background_ta);
        $QAChecklist->save();

        return redirect()->route('clients.show', $id)->with('flash_success', 'QA Checklist updated successfully');
    }

    public function update($id)
    {
        $QAChecklist = QAChecklist::find($id);
        $QAChecklist->strapline = $this->valueHelper(request()->strapline);
        $QAChecklist->footer_updated = $this->valueHelper(request()->footer_updated);
        $QAChecklist->correct_me_listed = $this->valueHelper(request()->correct_me_listed);
        $QAChecklist->page_numbers_updated = $this->valueHelper(request()->page_numbers_updated);
        $QAChecklist->family_tree = $this->valueHelper(request()->family_tree);
        $QAChecklist->all_rp_included = $this->valueHelper(request()->all_rp_included);
        $QAChecklist->client_exposure_not_identified_correctly = $this->valueHelper(request()->client_exposure_not_identified_correctly);
        $QAChecklist->client_information = $this->valueHelper(request()->client_information);
        $QAChecklist->relationship = $this->valueHelper(request()->relationship);
        $QAChecklist->kyc_date = $this->valueHelper(request()->kyc_date);
        $QAChecklist->casa = $this->valueHelper(request()->casa);
        $QAChecklist->pep = $this->valueHelper(request()->pep);
        $QAChecklist->sanctions = $this->valueHelper(request()->sanctions);
        $QAChecklist->str = $this->valueHelper(request()->str);
        $QAChecklist->litigation = $this->valueHelper(request()->litigation);
        $QAChecklist->adverse_media = $this->valueHelper(request()->adverse_media);
        $QAChecklist->v5_standard = $this->valueHelper(request()->v5_standard);
        $QAChecklist->all_products_included = $this->valueHelper(request()->all_products_included);
        $QAChecklist->linked_accounts_included = $this->valueHelper(request()->linked_accounts_included);
        $QAChecklist->wimi_wfs_account_listed = $this->valueHelper(request()->wimi_wfs_account_listed);
        $QAChecklist->email_sent_cib_wimi_wfs_clients = $this->valueHelper(request()->email_sent_cib_wimi_wfs_clients);
        $QAChecklist->all_info_included = $this->valueHelper(request()->all_info_included);
        $QAChecklist->review_date_correct = $this->valueHelper(request()->review_date_correct);
        $QAChecklist->expected_account_activity = $this->valueHelper(request()->expected_account_activity);
        $QAChecklist->ta_has_conclusion = $this->valueHelper(request()->ta_has_conclusion);
        $QAChecklist->listed_in_chronological_order = $this->valueHelper(request()->listed_in_chronological_order);
        $QAChecklist->eb_exact_extract_from_article = $this->valueHelper(request()->eb_exact_extract_from_article);
        $QAChecklist->rb_summary_article = $this->valueHelper(request()->rb_summary_article);
        $QAChecklist->does_it_align_with_background_ta = $this->valueHelper(request()->does_it_align_with_background_ta);
        $QAChecklist->save();

        return redirect()->route('clients.show', $QAChecklist->client_id)->with('flash_success', 'QA Checklist updated successfully');
    }

    public function report()
    {
        $QAChecklists = QAChecklist::all();

        $parameters = [
            'Strapline' => $this->filterChecklist($QAChecklists, 'strapline'),
            'Footer updated' => $this->filterChecklist($QAChecklists, 'footer_updated'),
            'Correct ME listed' => $this->filterChecklist($QAChecklists, 'correct_me_listed'),
            'Page Numbers updated' => $this->filterChecklist($QAChecklists, 'page_numbers_updated'),
            'Family Tree' => $this->filterChecklist($QAChecklists, 'family_tree'),
            'All RP included' => $this->filterChecklist($QAChecklists, 'all_rp_included'),
            'Clients with exposure and no exposure identified correctly' => $this->filterChecklist($QAChecklists, 'client_exposure_not_identified_correctly'),
            'Client information' => $this->filterChecklist($QAChecklists, 'client_information'),
            'Relationship' => $this->filterChecklist($QAChecklists, 'relationship'),
            'KYC date' => $this->filterChecklist($QAChecklists, 'kyc_date'),
            'Casa' => $this->filterChecklist($QAChecklists, 'casa'),
            'PEP' => $this->filterChecklist($QAChecklists, 'pep'),
            'Sanctions' => $this->filterChecklist($QAChecklists, 'sanctions'),
            'STR' => $this->filterChecklist($QAChecklists, 'str'),
            'Litigation' => $this->filterChecklist($QAChecklists, 'litigation'),
            'Adverse media' => $this->filterChecklist($QAChecklists, 'adverse_media'),
            'In line with V5 of Standard' => $this->filterChecklist($QAChecklists, 'v5_standard'),
            'All products inclued' => $this->filterChecklist($QAChecklists, 'all_products_included'),
            'Linked accounts included' => $this->filterChecklist($QAChecklists, 'linked_accounts_included'),
            'Wimi & WFS account listed' => $this->filterChecklist($QAChecklists, 'wimi_wfs_account_listed'),
            'Email sent to Carissa for CIB, WIMI & WFS Clients' => $this->filterChecklist($QAChecklists, 'email_sent_cib_wimi_wfs_clients'),
            'All info included' => $this->filterChecklist($QAChecklists, 'all_info_included'),
            'Review date correct' => $this->filterChecklist($QAChecklists, 'review_date_correct'),
            'Expected account activity' => $this->filterChecklist($QAChecklists, 'expected_account_activity'),
            'TA has a conclusion' => $this->filterChecklist($QAChecklists, 'ta_has_conclusion'),
            'Listed in chronological order' => $this->filterChecklist($QAChecklists, 'listed_in_chronological_order'),
            'EB-exact extract from article' => $this->filterChecklist($QAChecklists, 'eb_exact_extract_from_article'),
            'RB-Summary of article' => $this->filterChecklist($QAChecklists, 'rb_summary_article'),
            'Does it align with the background and TA' => $this->filterChecklist($QAChecklists, 'does_it_align_with_background_ta'),
        ];
        return view('reports.qa_checklist')->withData($parameters);
    }

    private function filterChecklist($checklist, $column){
        return [
            'pass' => $checklist->filter(function ($list) use($column) {
                return $list[$column] == '1';
            })->count(),
            'fail' => $checklist->filter(function ($list) use($column) {
                return $list[$column] === '0';
            })->count(),
            'not_reviewed' => $checklist->filter(function ($list) use($column) {
                return $list[$column] == null;
            })->count()
        ];
    }

    private function valueHelper($input){
        return ($input == 'on')?null:$input;
    }
}
