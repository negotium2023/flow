<?php
/**
 * Created by PhpStorm.
 * User: Klaas
 * Date: 2018/07/18
 * Time: 7:07 PM
 */

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DynamicReportExport implements FromView
{
    public function __construct($clients,$activity)
    {
        $this->clients = $clients;
        $this->activity = $activity;
    }

    public function view(): View
    {
        return view('reports.export', [
            'clients' => $this->clients,
            'activity' => $this->activity,
        ]);
    }
}