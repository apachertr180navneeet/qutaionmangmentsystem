<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'name',
            'sku',
            'description',
            'unit',
            'rate',
            'tax_percentage',
            'hsn_code',
            'is_active'
        ];
    }

    public function array(): array
    {
        return [
            [
                'Sample Item',
                'SKU001',
                'Sample description',
                'pcs',
                '100',
                '18',
                'HSN1234',
                'Active'
            ]
        ];
    }
}
