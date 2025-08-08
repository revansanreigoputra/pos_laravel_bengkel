<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class PurchaseOrdersExport implements 
    FromCollection, 
    WithHeadings, 
    WithEvents
{
    protected $purchaseOrders;
    protected $exportTitle;
    protected $startDate;
    protected $endDate;
    protected $status;
    protected $paymentMethod;

    public function __construct($purchaseOrders, $exportTitle = '', $startDate = null, $endDate = null, $status = null, $paymentMethod = null)
    {
        $this->purchaseOrders = $purchaseOrders;
        $this->exportTitle = $exportTitle;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
        $this->paymentMethod = $paymentMethod;
    }

    public function collection()
    {
        return $this->purchaseOrders ?? collect();
    }

    public function headings(): array
    {
        return [
            'Nomor Invoice',
            'Tanggal Order',
            'Supplier',
            'Status',
            'Total Harga',
            'Item',
            'Jumlah',
            'Total Harga Beli',
        ];
    }

    public function map($purchaseOrder): array
    {
        $itemsData = $purchaseOrder->items->map(function ($item) {
            return [
                'item_name' => $item->sparepart->name ?? 'N/A',
                'quantity' => $item->quantity,
                'price' => $item->purchase_price,
                'total_item_price' => $item->total_price,
            ];
        });

        $itemDetails = $itemsData->map(function ($item) {
            return "{$item['item_name']} ({$item['quantity']}x @Rp" . number_format($item['price'], 0, ',', '.') . ") - Total: Rp" . number_format($item['total_item_price'], 0, ',', '.');
        })->implode("\n");

        return [
            $purchaseOrder->invoice_number,
            Carbon::parse($purchaseOrder->order_date)->format('Y-m-d H:i:s'),
            $purchaseOrder->supplier->name ?? 'N/A',
            $purchaseOrder->status,
            'Rp' . number_format($purchaseOrder->total_price, 0, ',', '.'),
            $itemDetails,
            $purchaseOrder->items->sum('quantity'),
            'Rp' . number_format($purchaseOrder->items->sum('total_price'), 0, ',', '.'),
        ];
    }

    public function title(): string
    {
        return 'Laporan Pembelian';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle(1)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FF4F70F5'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getStyle('A2:' . $highestColumn . $highestRow)
              ->getBorders()
              ->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN)
              ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));

        $sheet->getStyle('G:H')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        $sheet->getStyle('F')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);

        $sheet->setAutoFilter('A2:' . $highestColumn . '2');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();

                // Insert row for title
                $sheet->insertNewRowBefore(1, 1);

                $reportTitle = $this->exportTitle;

                if ($this->startDate && $this->endDate) {
                    $reportTitle .= ' Periode ' . Carbon::parse($this->startDate)->format('d M Y') . ' s/d ' . Carbon::parse($this->endDate)->format('d M Y');
                } elseif ($this->startDate) {
                    $reportTitle .= ' Mulai ' . Carbon::parse($this->startDate)->format('d M Y');
                } elseif ($this->endDate) {
                    $reportTitle .= ' Sampai ' . Carbon::parse($this->endDate)->format('d M Y');
                }

                if ($this->status) {
                    $reportTitle .= ' (Status: ' . ucfirst($this->status) . ')';
                }

                $sheet->setCellValue('A1', $reportTitle);
                $sheet->mergeCells('A1:' . $highestColumn . '1');

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
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFEEEEEE'],
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(30);
            },
        ];
    }
}
