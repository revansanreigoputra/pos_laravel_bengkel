<?php

namespace App\Exports\Sheets;

use App\Models\Sparepart;
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
    Alignment
};
use Carbon\Carbon;

class EmptySparepartSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, WithEvents
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
        $query = Sparepart::with(['category', 'supplier', 'purchaseOrderItems']);
        
        if ($this->startDate && $this->endDate) {
            $query->whereHas('purchaseOrderItems', function ($itemQuery) {
                $itemQuery->whereDate('created_at', '>=', $this->startDate)
                    ->whereDate('created_at', '<=', $this->endDate);
            });
        }

        $spareparts = $query->whereDoesntHave('purchaseOrderItems')
            ->orWhereHas('purchaseOrderItems', function ($itemQuery) {
                $itemQuery->whereRaw('quantity - sold_quantity <= 0');
            })
            ->get();
        
        return $spareparts->map(function ($sparepart) {
            $availableStock = $sparepart->purchaseOrderItems->where('quantity', '>', 0)->sum(function($item) {
                return $item->quantity - $item->sold_quantity;
            });
            
            return [
                'Kode Part' => $sparepart->code_part ?? '-',
                'Nama Sparepart' => $sparepart->name,
                'Kategori' => $sparepart->category->name ?? 'N/A',
                'Stok Tersedia' => $availableStock,
                'Harga Jual' => $sparepart->selling_price,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Kode Part',
            'Nama Sparepart',
            'Kategori',
            'Stok Tersedia',
            'Harga Jual',
        ];
    }

    public function title(): string
    {
        return 'Stok Kosong';
    }

    public function styles(Worksheet $sheet)
    {
        // Only set default font for the sheet
        $sheet->getParent()->getDefaultStyle()->applyFromArray([
            'font' => [
                'name' => 'Calibri',
                'size' => 11,
            ]
        ]);
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
                $sheet->setCellValue('A1', 'LAPORAN STOK SPAREPART - KOSONG');
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
