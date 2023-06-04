<?php

namespace App\Http\Controllers;

use App\QAChecklist;
use App\User;
use PDF;
use Illuminate\Http\Request;

class QAChecklistAnalystController extends Controller
{
    public function index()
    {
        if (!empty(\request()->all()) && \request()->analyst != ''){
            $report = QAChecklist::whereHas('client', function ($query){
                $query->whereConsultantId(\request()->analyst);
            });

            if (\request()->date_from != ''){
                $report = $report->whereHas('client', function ($query){
                    $query->where('instruction_date', '>=', \request()->date_from);
                });
            }

            if (\request()->date_to != ''){
                $report = $report->whereHas('client', function ($query){
                    $query->where('instruction_date', '>=', \request()->date_to);
                });
            }

            $report = $report->get();
            $report_values = $report->map(function ($checlist){
                return array_values($checlist->toArray());
            });

            $parameters = [
                'total_passed' => $report_values->flatten()->filter(function ($item){ return $item == '1'; })->count(),
                'total_failed' => $report_values->flatten()->filter(function ($item){ return $item == '0'; })->count(),
                'total_not_reviewed' => $report_values->flatten()->filter(function ($item){ return $item == null; })->count(),
                'user' => User::find(\request()->analyst, ['first_name', 'last_name']),
                'strapline' => $this->filterPerItem($report, 'strapline'),
                'footer_updated' => $this->filterPerItem($report, 'footer_updated'),
                'correct_me_listed' => $this->filterPerItem($report, 'correct_me_listed'),
                'page_numbers_updated' => $this->filterPerItem($report, 'page_numbers_updated'),
                'family_tree' => $this->filterPerItem($report, 'family_tree'),
                'all_rp_included' => $this->filterPerItem($report, 'all_rp_included'),
                'client_exposure_not_identified_correctly' => $this->filterPerItem($report, 'client_exposure_not_identified_correctly'),
                'client_information' => $this->filterPerItem($report, 'client_information'),
                'relationship' => $this->filterPerItem($report, 'relationship'),
                'kyc_date' => $this->filterPerItem($report, 'kyc_date'),
                'casa' => $this->filterPerItem($report, 'casa'),
                'pep' => $this->filterPerItem($report, 'pep'),
                'sanctions' => $this->filterPerItem($report, 'sanctions'),
                'str' => $this->filterPerItem($report, 'str'),
                'litigation' => $this->filterPerItem($report, 'litigation'),
                'adverse_media' => $this->filterPerItem($report, 'adverse_media'),
                'v5_standard' => $this->filterPerItem($report, 'v5_standard'),
                'all_products_included' => $this->filterPerItem($report, 'all_products_included'),
                'linked_accounts_included' => $this->filterPerItem($report, 'linked_accounts_included'),
                'wimi_wfs_account_listed' => $this->filterPerItem($report, 'wimi_wfs_account_listed'),
                'email_sent_cib_wimi_wfs_clients' => $this->filterPerItem($report, 'email_sent_cib_wimi_wfs_clients'),
                'all_info_included' => $this->filterPerItem($report, 'all_info_included'),
                'review_date_correct' => $this->filterPerItem($report, 'review_date_correct'),
                'expected_account_activity' => $this->filterPerItem($report, 'expected_account_activity'),
                'ta_has_conclusion' => $this->filterPerItem($report, 'ta_has_conclusion'),
                'listed_in_chronological_order' => $this->filterPerItem($report, 'listed_in_chronological_order'),
                'eb_exact_extract_from_article' => $this->filterPerItem($report, 'eb_exact_extract_from_article'),
                'rb_summary_article' => $this->filterPerItem($report, 'rb_summary_article'),
                'does_it_align_with_background_ta' => $this->filterPerItem($report, 'does_it_align_with_background_ta'),
            ];

            if(\request()->export != ''){
                $pdf = PDF::loadView('qa-analyst.pdf', $parameters);
                return $pdf->download('Analyst_QA_Report '.$parameters['user']['first_name'].'_'.$parameters['user']['first_name'].now()->timestamp);
            }
        }else{
            $users_drop_down = User::all()->keyBy('id')->map(function ($user){
                return $user->first_name.' '.$user->last_name;
            });
            $parameters = [
                'analysts' => $users_drop_down
            ];
        }

        return view('qa-analyst.index')->with($parameters);
    }

    private function filterPerItem($checklist, $column){
        return [
            'passed' => $checklist->filter(function ($item) use($column){ return $item[$column] == '1'; })->count(),
            'failed' => $checklist->filter(function ($item) use($column){ return $item[$column] == '0'; })->count(),
            'not_reviewed' => $checklist->filter(function ($item) use($column){ return $item[$column] == null; })->count(),
        ];
    }
}
