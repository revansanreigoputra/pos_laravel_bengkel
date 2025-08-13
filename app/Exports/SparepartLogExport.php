<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\StokSaatIniSheet;
use App\Exports\Sheets\StokMasukSheet;
use App\Exports\Sheets\StokKeluarSheet;

class SparepartLogExport implements WithMultipleSheets
{
    use Exportable;

    protected $tipe;
    protected $startDate;
    protected $endDate;

    public function __construct($tipe, $startDate, $endDate)
    {
        $this->tipe = $tipe;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Always add all three sheets
        $sheets[] = new StokSaatIniSheet($this->startDate, $this->endDate);
        $sheets[] = new StokMasukSheet($this->startDate, $this->endDate);
        $sheets[] = new StokKeluarSheet($this->startDate, $this->endDate);
        
        return $sheets;
    }
}