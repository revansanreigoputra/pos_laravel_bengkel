<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithStyles,
    WithTitle,
    WithEvents
};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\{
    Fill,
    Font,
    Alignment,
    Border
};
use App\Models\Category;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class SparepartTemplate  implements FromCollection, WithHeadings, WithStyles, WithTitle, WithEvents
{
    private $categories;
    private $headings;

    public function __construct()
    {
        $this->categories = Category::pluck('name')->toArray();
        $this->headings = [
            'name',
            'category_name', 
            'purchase_price',
            'selling_price',
            'discount_percentage',
            'discount_start_date',
            'discount_end_date'
        ];
    }

    public function collection()
    {
        // Return a single empty row for the user to fill out.
        // The headings and styles are handled by their respective methods.
        return collect([
                                     // A blank row for spacing
            array_fill(0, count($this->headings), null)
        ]);
    }

    public function headings(): array
    {
        // Define the column headings for the sparepart data.
        return $this->headings;
    }

    public function title(): string
    {
        return ' ';
    }

    public function styles(Worksheet $sheet)
    {
        $highestCol = $sheet->getHighestColumn();
        $nextCol = chr(ord($highestCol) + 2); // Get the column after the last heading

        // Add the legend heading
        $sheet->setCellValue($nextCol . '1', 'Arti Warna');
        $sheet->getStyle($nextCol . '1')->getFont()->setBold(true);

        // Add the legend items
        $sheet->setCellValue($nextCol . '2', 'Harus Diisi (yellow)');
        $sheet->setCellValue($nextCol . '4', 'Opsional (gray)');

        // Style the legend
        $sheet->getStyle($nextCol . '2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $sheet->getStyle($nextCol . '4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD3D3D3');

        // --- Existing styling logic, now adjusted to target row 1 ---

        // Set default font for the entire sheet
        $sheet->getParent()->getDefaultStyle()->applyFromArray([
            'font' => [
                'name' => 'Calibri',
                'size' => 11,
            ]
        ]);

        // Header row styling
        $sheet->getStyle('A1:' . $highestCol . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => '000000'],
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

        // Specific color styling for the headings
        // Name (A1) and Category (B1) are yellow
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        $sheet->getStyle('B1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');

        // Other fields (C1 to the highest column) are gray
        $sheet->getStyle('C1:' . $highestCol . '1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFD3D3D3');

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // --- Data validation and dropdown logic ---
        $sheet->getCell('B2')->setDataValidation(clone $sheet->getCell('B2')->getDataValidation());
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $categoriesCount = count($this->categories);
                $highestCol = $sheet->getHighestColumn();

                // Insert title row
                $sheet->insertNewRowBefore(1, 2);

                // Set report title
                $title = 'Template Tambah Sparepart';
                $sheet->setCellValue('A1', $title);

                // Style title
                $sheet->mergeCells('A1:' . $highestCol . '1');
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

                // Add a hidden sheet for the category dropdown data
                $event->sheet->getParent()->createSheet(1);
                $categorySheet = $event->sheet->getParent()->getSheet(1);
                $categorySheet->setTitle('Categories');

                // Write categories to the new sheet
                foreach ($this->categories as $index => $categoryName) {
                    $categorySheet->setCellValue('A' . ($index + 1), $categoryName);
                }

                // Hide the sheet
                $categorySheet->setSheetState(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::SHEETSTATE_HIDDEN);

                // Set data validation for the category_name column (now B)
                $validation = $sheet->getCell('B3')->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setAllowBlank(false);
                $validation->setShowDropDown(true);
                $validation->setFormula1('Categories!$A$1:$A$' . $categoriesCount);

                // Apply data validation to the entire column
                for ($i = 3; $i <= 1000; $i++) {
                    $sheet->getCell('B' . $i)->setDataValidation(clone $validation);
                }
            },
        ];
    }
}
