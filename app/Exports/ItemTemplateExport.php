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
                'Active'
            ]
        ];
    }
}
