<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class ItemImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        if (empty($row['name'])) {
            return null;
        }

        $query = Item::query();
        $query->where(function($q) use ($row) {
            $q->where('name', $row['name']);
            if (!empty($row['sku'])) {
                $q->orWhere('sku', $row['sku']);
            }
        });

        $item = $query->first();

        $data = [
            'name'           => $row['name'],
            'sku'            => $row['sku'] ?? null,
            'description'    => $row['description'] ?? null,
            'unit'           => $row['unit'] ?? 'pcs',
            'rate'           => $row['rate'] ?? 0,
            'tax_percentage' => $row['tax_percentage'] ?? 0,
            'hsn_code'       => $row['hsn_code'] ?? null,
            'is_active'      => isset($row['is_active']) ? (strtolower($row['is_active']) == 'active' ? 1 : 0) : 1,
            'image'          => null, // Set image to null as requested
        ];

        if ($item) {
            $item->fill($data);
            return $item;
        }

        $data['uuid'] = (string) Str::uuid();
        $data['created_by'] = auth()->id();
        
        return new Item($data);
    }
}
