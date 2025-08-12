<?php

namespace App\Exports;

use App\Models\Sparepart;
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

class SparepartExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, ShouldAutoSize, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Eager load relationships to avoid N+1 query issues
        return Sparepart::with(['category'])->get();
    }

    /**
     * Set the title of the sheet.
     */
    public function title(): string
    {
        return 'Sparepart';
    }

    /**
     * Apply styles to the worksheet.
     */
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


    /**
     * Register events to customize the sheet.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Example: Set auto filter for all columns
                $cellRange = 'A1:L1'; // Adjust range according to headings count
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
                $event->sheet->getDelegate()->setAutoFilter($cellRange);
            },
        ];
    }

    /**
     * Define the headings for the exported file.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Category',
            'Code Part', 
            'Purchase Price',
            'Selling Price', 
            'Discount Percentage',
            'Discount Start Date',
            'Discount End Date',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * Map the data from the collection to the columns.
     */
    public function map($sparepart): array
    {
         
        return [
            $sparepart->id,
            $sparepart->name,
            $sparepart->category->name ?? 'N/A', // Handle case where category might be null
            $sparepart->code_part, 
            $sparepart->purchase_price,
            $sparepart->selling_price,
           
            $sparepart->discount_percentage,
            $sparepart->discount_start_date,
            $sparepart->discount_end_date,
            $sparepart->created_at,
            $sparepart->updated_at,
        ];
    }
}