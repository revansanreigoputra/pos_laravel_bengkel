<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping,
    WithTitle,
    WithStyles,
    ShouldAutoSize,
    WithEvents
};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{
    NumberFormat,
    Border,
    Fill,
    Alignment,
    Font
};
use Carbon\Carbon;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $status;

    public function __construct($startDate = null, $endDate = null, $status = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    public function collection()
    {
        $query = Transaction::with('items.service', 'items.sparepart');

        if ($this->startDate) {
            $query->whereDate('transaction_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('transaction_date', '<=', $this->endDate);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return $query->orderBy('transaction_date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Invoice',
            'Pelanggan',
            'No. telepon',
            'No. Kendaraan',
            'Model Kendaraan',
            'Tanggal Transaksi',
            'Metode Pembayaran',
            'Diskon (Rp)',
            'Total Harga (Rp)',
            'Status',
            'Items',
        ];
    }

    public function map($transaction): array
    {
        $items = $transaction->items->map(function ($item) {
            $itemName = '';
            if ($item->item_type == 'service' && $item->service) {
                $itemName = $item->service->name . ' (Servis)';
            } elseif ($item->item_type == 'sparepart' && $item->sparepart) {
                $itemName = $item->sparepart->name . ' (Sparepart)';
            } else {
                $itemName = ucfirst($item->item_type);
            }
            return $itemName . ' - ' . $item->quantity . ' x Rp ' . number_format($item->price, 0, ',', '.');
        })->implode(PHP_EOL);

        return [
            $transaction->invoice_number,
            $transaction->customer->name,
            $transaction->customer->phone ?? '-',
            $transaction->vehicle_number,
            $transaction->vehicle_model ?? '-',
            Carbon::parse($transaction->transaction_date)->format('d-m-Y'),
            ucfirst($transaction->payment_method),
            $transaction->discount_amount,
            $transaction->total_price,
            ucfirst($transaction->status),
            $items,
        ];
    }

    public function title(): string
    {
        return 'Transaksi ' . ucfirst($this->status ?? 'Semua');
    }

    public function styles(Worksheet $sheet)
    {
        // Set default font for the entire sheet
        $sheet->getParent()->getDefaultStyle()->applyFromArray([
            'font' => [
                'name' => 'Calibri',
                'size' => 11,
            ]
        ]);

        // Header row styling
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FF4F70F5'], // Blue header
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

        // Set row height for header
        $sheet->getRowDimension(2)->setRowHeight(25);

        // Data rows styling
        $highestRow = $sheet->getHighestRow();
        $dataRange = 'A3:J' . $highestRow;

        $sheet->getStyle($dataRange)->applyFromArray([
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
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
        for ($i = 3; $i <= $highestRow; $i++) {
            if ($i % 2 == 0) {
                $sheet->getStyle('A' . $i . ':J' . $i)->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFF5F5F5'], // Very light gray
                    ],
                ]);
            }
        }

        // Number formatting for currency columns
        $sheet->getStyle('G3:H' . $highestRow)->getNumberFormat()
              ->setFormatCode('#,##0.00');

        // Items column styling
        $sheet->getStyle('J3:J' . $highestRow)->getAlignment()
              ->setWrapText(true)
              ->setVertical(Alignment::VERTICAL_TOP);

        // Auto-filter for header
        $sheet->setAutoFilter("A1:J2");

        // Set column widths (auto-size plus some manual adjustments)
        $sheet->getColumnDimension('A')->setWidth(15); // Invoice
        $sheet->getColumnDimension('B')->setWidth(20); // Pelanggan
        $sheet->getColumnDimension('C')->setWidth(15); // No. Kendaraan
        $sheet->getColumnDimension('D')->setWidth(20); // Model Kendaraan
        $sheet->getColumnDimension('E')->setWidth(15); // Tanggal
        $sheet->getColumnDimension('F')->setWidth(15); // Pembayaran
        $sheet->getColumnDimension('G')->setWidth(12); // Diskon
        $sheet->getColumnDimension('H')->setWidth(15); // Total
        $sheet->getColumnDimension('I')->setWidth(12); // Status
        $sheet->getColumnDimension('J')->setWidth(40); // Items
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Insert title row
                $sheet->insertNewRowBefore(1, 2);
                
                // Set report title
                $title = 'LAPORAN TRANSAKSI';
                if ($this->status) {
                    $title .= ' ' . strtoupper($this->status);
                }
                
                $subtitle = '';
                if ($this->startDate && $this->endDate) {
                    $subtitle = 'Periode: ' . Carbon::parse($this->startDate)->format('d M Y') . ' - ' . Carbon::parse($this->endDate)->format('d M Y');
                } elseif ($this->startDate) {
                    $subtitle = 'Mulai: ' . Carbon::parse($this->startDate)->format('d M Y');
                } elseif ($this->endDate) {
                    $subtitle = 'Sampai: ' . Carbon::parse($this->endDate)->format('d M Y');
                }

                $sheet->setCellValue('A1', $title);
                $sheet->mergeCells('A1:J1');
                
                if (!empty($subtitle)) {
                    $sheet->setCellValue('A2', $subtitle);
                    $sheet->mergeCells('A2:J2');
                }

                // Style title
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

                // Style subtitle
                if (!empty($subtitle)) {
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

                // Set row heights for title/subtitle
                $sheet->getRowDimension(1)->setRowHeight(30);
                if (!empty($subtitle)) {
                    $sheet->getRowDimension(2)->setRowHeight(20);
                }

                // Freeze header row
                $sheet->freezePane('A3');
            },
        ];
    }
}