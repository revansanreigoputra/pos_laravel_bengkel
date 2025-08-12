<?php

namespace App\Imports;

use App\Models\Sparepart;
use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SparepartImport implements ToCollection, WithHeadingRow, WithStartRow
{
    /**
     * Define the heading row number.
     * Your headings are now on row 3 due to the title and a blank row.
     *
     * @return int
     */
    public function headingRow(): int
    {
        return 3;
    }

    /**
     * Define the starting row for data import.
     * The data begins on the row after the headings.
     *
     * @return int
     */
    public function startRow(): int
    {
        return 4;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip rows with no name or category_name, as these are required.
            if (empty($row['name']) || empty($row['category_name'])) {
                continue;
            }

            // Find the category by name from the dropdown list
            $category = Category::where('name', $row['category_name'])->first();

            // If the category doesn't exist, skip the row to prevent errors
            if (!$category) {
                continue;
            }

            // Generate code_part if it's not provided in the Excel file
            $generatedCodePart = $this->generateUniqueCodePart($row['name'], $category->name);

            Sparepart::create([
                'name' => $row['name'],
                'category_id' => $category->id,
                'code_part' => $row['code_part'] ?? $generatedCodePart, // Use existing or generated
               
                'purchase_price' => $row['purchase_price'] ?? null,
                'selling_price' => $row['selling_price'] ?? null,
                'discount_percentage' => $row['discount_percentage'] ?? 0,
                'discount_start_date' => $row['discount_start_date'] ? Carbon::parse($row['discount_start_date']) : null,
                'discount_end_date' => $row['discount_end_date'] ? Carbon::parse($row['discount_end_date']) : null,
            ]);
        }
    }

    /**
     * Generate a unique code part for the sparepart.
     */
    protected function generateUniqueCodePart($name, $categoryName)
    {
        $namePart = strtoupper(substr(Str::slug($name), 0, 3));
        $catPart = strtoupper(substr(Str::slug($categoryName), 0, 3));
        $random = mt_rand(100, 9999);

        $generatedCodePart = "{$catPart}-{$namePart}-{$random}";

        while (Sparepart::where('code_part', $generatedCodePart)->exists()) {
            $random = mt_rand(100, 9999);
            $generatedCodePart = "{$catPart}-{$namePart}-{$random}";
        }
        return $generatedCodePart;
    }
}