<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Item::orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'id',
            'name',
            'sku',
            'mrp',
            'sdp',
            'rate',
            'unit',
            'tax_percentage',
            'description',
            'is_active',
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->name,
            $item->sku,
            $item->mrp ?? $item->rate,
            $item->sdp ?? 0,
            $item->rate,
            $item->unit,
            $item->tax_percentage,
            $item->description,
            $item->is_active ? 'Active' : 'Inactive',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
