<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientExport implements FromCollection, WithHeadings
{
    public function __construct(Collection $clients)
    {
        $this->clients = $clients;
    }

    public function headings(): array
    {
        return [
            'P/R',
            'Name',
            'Committee',
            'Case Number',
            'Out of Scope',
            'CIF Code',
            'Project',
            'Instruction Date',
            'Trigger Type',
            'Investigation Completed Date',
            'Qa Completed Date',
            'Committee Date',
            'Committee Decision Date',
            'Case Completed Date',
            'Current Work Qeue',
            'Assigned User'
        ];
    }

    public function collection()
    {
        //$collecton = $this->clients->only(['client_id', 'company']);
        return $this->clients;
    }
}
