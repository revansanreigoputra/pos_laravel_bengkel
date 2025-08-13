<?php

namespace App\Exports\Sheets;

use App\Models\TransactionItem;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithTitle,
    WithStyles,
    ShouldAutoSize,
    WithEvents
}; 
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{
    Fill,
    Border,
    Alignment,
    Font
};
use Carbon\Carbon;

class StokKeluarSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, WithEvents
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = TransactionItem::with(['sparepart.category', 'transaction'])->where('item_type', 'sparepart');
        if ($this->startDate && $this->endDate) {
            $query->whereHas('transaction', function ($q) {
                $q->whereBetween('transaction_date', [
                    Carbon::parse($this->startDate)->startOfDay(),
                    Carbon::parse($this->endDate)->endOfDay()
                ]);
            });
        }
        $barangKeluar = $query->get();
        
        $counter = 1;
        return $barangKeluar->map(function ($item) use (&$counter) {
            return [
                'No' => $counter++,
                'Tanggal' => $item->transaction->transaction_date ? Carbon::parse($item->transaction->transaction_date)->format('d M Y') : '-',
                'Nama Sparepart' => $item->sparepart->name ?? '-',
                'Kategori' => $item->sparepart->category->name ?? '-',
                'Jumlah Keluar' => $item->quantity,
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Nama Sparepart', 'Kategori', 'Jumlah Keluar'];
    }

    public function title(): string
    {
        return 'Stok Keluar';
    }

    public function styles(Worksheet $sheet)
    {
        // Set default font for the sheet
        $sheet->getParent()->getDefaultStyle()->applyFromArray([
            'font' => [
                'name' => 'Calibri',
                'size' => 11,
            ]
        ]);
        
        // Data rows styling (starting from row 4)
        $highestColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();
        $dataRange = 'A4:' . $highestColumn . $highestRow;
        
        $sheet->getStyle($dataRange)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FFD3D3D3'], // Light gray borders
                ],
            ],
        ]);
        
        // Alternate row coloring
        for ($i = 4; $i <= $highestRow; $i++) {
            if ($i % 2 !== 0) {
                $sheet->getStyle('A' . $i . ':' . $highestColumn . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFF5F5F5'], // Very light gray
                    ],
                ]);
            }
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $lastColumnLetter = $highestColumn;

                // Insert title and subtitle rows
                $sheet->insertNewRowBefore(1, 2);

                // Title
                $sheet->setCellValue('A1', 'LAPORAN STOK SPAREPART - STOK KELUAR');
                $sheet->mergeCells('A1:' . $lastColumnLetter . '1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['argb' => 'FF000000'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Subtitle (if any)
                $subtitle = '';
                if ($this->startDate && $this->endDate) {
                    $subtitle = 'Periode: ' . Carbon::parse($this->startDate)->format('d M Y') . ' - ' . Carbon::parse($this->endDate)->format('d M Y');
                } elseif ($this->startDate) {
                    $subtitle = 'Mulai: ' . Carbon::parse($this->startDate)->format('d M Y');
                } elseif ($this->endDate) {
                    $subtitle = 'Sampai: ' . Carbon::parse($this->endDate)->format('d M Y');
                }

                if (!empty($subtitle)) {
                    $sheet->setCellValue('A2', $subtitle);
                    $sheet->mergeCells('A2:' . $lastColumnLetter . '2');
                    $sheet->getStyle('A2')->applyFromArray([
                        'font' => [
                            'size' => 12,
                            'color' => ['argb' => 'FF555555'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                // Style the headings row (A3)
                $sheet->getStyle('A3:' . $lastColumnLetter . '3')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['argb' => 'FFFFFFFF'],
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'FF4F70F5'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                ]);

                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(30);
                if (!empty($subtitle)) {
                    $sheet->getRowDimension(2)->setRowHeight(20);
                }
                $sheet->getRowDimension(3)->setRowHeight(25);

                // Freeze headings row
                $sheet->freezePane('A4');
            },
        ];
    }
} 