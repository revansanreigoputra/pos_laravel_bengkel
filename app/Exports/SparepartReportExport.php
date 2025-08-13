<?php 

namespace App\Exports;

 
use App\Exports\Sheets\AvailableSparepartSheet;
use App\Exports\Sheets\EmptySparepartSheet;
use App\Exports\Sheets\ExpiredSparepartSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SparepartReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Add the 'available' sheet
        $sheets[] = new AvailableSparepartSheet($this->startDate, $this->endDate);

        // Add the 'expired' sheet
        $sheets[] = new ExpiredSparepartSheet($this->startDate, $this->endDate);

        // Add the 'empty' sheet
        $sheets[] = new EmptySparepartSheet($this->startDate, $this->endDate);

        return $sheets;
    }
}