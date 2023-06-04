<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class CustomReportExport implements FromView
{
    public function __construct($clients,$columns,$step_names)
    {
        $this->clients = $clients;
        $this->columns = $columns;
        $this->step_names = $step_names;
    }

    public function view(): View
    {
        return view('customreports.export', [
            'fields' => $this->columns,
            'clients' => $this->clients,
            'step_names' => $this->step_names
        ]);
    }
}
