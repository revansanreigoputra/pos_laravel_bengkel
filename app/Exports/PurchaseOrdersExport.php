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

class PurchaseOrdersExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $exportTitle;

    public function __construct($startDate, $endDate, $exportTitle = 'Laporan Pembelian')
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->exportTitle = $exportTitle;
    }

    /**
     * @return \Illuminate\Support\Collection
     */ 

    public function collection()
    {
         $query = PurchaseOrder::query()->with(['supplier', 'items.sparepart']);

        if ($this->startDate) {
            $query->whereDate('order_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('order_date', '<=', $this->endDate);
        }

        $query->where('status', 'completed');

        return $query->orderBy('order_date', 'desc')->get();
    }
    public function headings(): array
    {
        return [
            'ID Pembelian',
            'Nomor PO',
            'Tanggal Order',
            'Supplier',
            'Status',
            'Total Harga',
            'Item',
            'Jumlah',
            'Harga Beli',
            'Total Item',
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

        // Combine items into a readable string
        $itemDetails = $itemsData->map(function ($item) {
            return "{$item['item_name']} ({$item['quantity']}x @Rp" . number_format($item['price'], 0, ',', '.') . ") - Total: Rp" . number_format($item['total_item_price'], 0, ',', '.');
        })->implode("\n");

        return [
            $purchaseOrder->id,
            $purchaseOrder->order_number,
            Carbon::parse($purchaseOrder->order_date)->format('Y-m-d H:i:s'),
            $purchaseOrder->supplier->name ?? 'N/A',
            $purchaseOrder->status,
            'Rp' . number_format($purchaseOrder->total_price, 0, ',', '.'),
            $itemDetails,
            $purchaseOrder->items->sum('quantity'),
            // 'price_column_for_item_here', // Placeholder, can't be a single value
            'Rp' . number_format($purchaseOrder->items->sum('purchase_price'), 0, ',', '.'), // this needs to be re-evaluated for accuracy
            'Rp' . number_format($purchaseOrder->items->sum('total_price'), 0, ',', '.'),
        ];
    }
     public function title(): string
    {
        return 'Pembelian Selesai'; // Nama sheet Excel
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
     public function styles(Worksheet $sheet)
    {
        // --- Styling Header (Baris 2, karena baris 1 adalah judul) ---
        $sheet->getStyle(1)->applyFromArray([ // Mengubah dari 1 menjadi 2
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFFFF'], // Teks putih
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['argb' => 'FF4F70F5'], // Latar belakang biru
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // --- Border pada semua sel yang berisi data (dimulai dari baris 1 untuk judul) ---
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . $highestRow) // Range dimulai dari A1
              ->getBorders()
              ->getAllBorders()
              ->setBorderStyle(Border::BORDER_THIN)
              ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK));

        // --- Format kolom harga (Diskon dan Total Harga) sebagai mata uang ---
        // Kolom G (Diskon) dan H (Total Harga)
        // Perhatikan bahwa format ini akan berlaku untuk semua baris di kolom tersebut.
        $sheet->getStyle('G:H')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);

        // --- Auto Filter pada header (Baris 2) ---
        $sheet->setAutoFilter($sheet->calculateWorksheetDimension()); // Ini akan otomatis mendeteksi header yang valid

        // --- Mengatur wrap text dan rata atas untuk kolom 'Items' (Kolom J) ---
        $sheet->getStyle('J')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
    }

     public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn(); // Dapatkan kolom terakhir yang digunakan

                // --- Sisipkan baris baru di paling atas (baris 1) ---
                $sheet->insertNewRowBefore(1, 1);

                // --- Tulis judul laporan ke baris 1 ---
                $reportTitle = 'Laporan Pembelian Selesai'; // Default title for the sheet
                if ($this->startDate && $this->endDate) {
                    $reportTitle .= ' Periode ' . Carbon::parse($this->startDate)->format('d M Y') . ' s/d ' . Carbon::parse($this->endDate)->format('d M Y');
                } elseif ($this->startDate) {
                    $reportTitle .= ' Mulai ' . Carbon::parse($this->startDate)->format('d M Y');
                } elseif ($this->endDate) {
                    $reportTitle .= ' Sampai ' . Carbon::parse($this->endDate)->format('d M Y');
                }

                $sheet->setCellValue('A1', $reportTitle);

                // --- Gabungkan sel untuk judul (dari A1 sampai kolom terakhir) ---
                $sheet->mergeCells('A1:' . $highestColumn . '1');

                // --- Styling judul ---
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 16,
                        'color' => ['argb' => 'FF000000'], // Teks hitam
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFEEEEEE'], // Latar belakang abu-abu terang
                    ],
                ]);

                // Mengatur tinggi baris untuk judul
                $sheet->getRowDimension(1)->setRowHeight(30);
            },
        ];
    }
}