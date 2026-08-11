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
            'mrp',
            'sdp',
            'rate',
            'description',
            'unit',
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
                '120',
                '95',
                '100',
                'Sample description',
                'pcs',
                '18',
                'Active'
            ]
        ];
    }
}
