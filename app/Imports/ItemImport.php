<?php

namespace App\Imports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ItemImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    public function collection(Collection $rows)
    {
        $userId = auth()->id();
        $now = now();
        $insertData = [];

        foreach ($rows as $row) {
            if (empty($row['name'])) {
                continue;
            }

            $name = trim($row['name']);
            $sku = !empty($row['sku']) ? trim($row['sku']) : null;

            $insertData[] = [
                'uuid'           => (string) Str::uuid(),
                'name'           => $name,
                'sku'            => $sku,
                'description'    => $row['description'] ?? null,
                'unit'           => $row['unit'] ?? 'pcs',
                'rate'           => $row['rate'] ?? 0,
                'tax_percentage' => $row['tax_percentage'] ?? 0,
                'is_active'      => isset($row['is_active']) ? (strtolower($row['is_active']) == 'active' ? 1 : 0) : 1,
                'image'          => null,
                'created_by'     => $userId,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        if (!empty($insertData)) {
            Item::insert($insertData);
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
