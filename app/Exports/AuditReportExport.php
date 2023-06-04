<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class AuditReportExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
}
