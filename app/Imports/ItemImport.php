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
        $names = [];
        $skus = [];

        foreach ($rows as $row) {
            if (!empty($row['name'])) {
                $names[] = trim($row['name']);
            }
            if (!empty($row['sku'])) {
                $skus[] = trim($row['sku']);
            }
        }

        if (empty($names)) {
            return;
        }

        // Fetch existing items in a batch query to optimize performance
        $query = Item::query()->whereIn('name', $names);
        if (!empty($skus)) {
            $query->orWhereIn('sku', $skus);
        }
        $existingItems = $query->get();

        // Map existing items by name and SKU for O(1) lookups
        $existingByName = [];
        $existingBySku = [];
        foreach ($existingItems as $item) {
            $existingByName[strtolower($item->name)] = $item;
            if ($item->sku) {
                $existingBySku[strtolower($item->sku)] = $item;
            }
        }

        $userId = auth()->id();

        DB::transaction(function () use ($rows, $existingByName, $existingBySku, $userId) {
            foreach ($rows as $row) {
                if (empty($row['name'])) {
                    continue;
                }

                $name = trim($row['name']);
                $sku = !empty($row['sku']) ? trim($row['sku']) : null;

                $item = null;
                if ($sku && isset($existingBySku[strtolower($sku)])) {
                    $item = $existingBySku[strtolower($sku)];
                } elseif (isset($existingByName[strtolower($name)])) {
                    $item = $existingByName[strtolower($name)];
                }

                $data = [
                    'name'           => $name,
                    'sku'            => $sku,
                    'description'    => $row['description'] ?? null,
                    'unit'           => $row['unit'] ?? 'pcs',
                    'rate'           => $row['rate'] ?? 0,
                    'tax_percentage' => $row['tax_percentage'] ?? 0,
                    'hsn_code'       => $row['hsn_code'] ?? null,
                    'is_active'      => isset($row['is_active']) ? (strtolower($row['is_active']) == 'active' ? 1 : 0) : 1,
                    'image'          => null,
                ];

                if ($item) {
                    $item->update($data);
                } else {
                    $data['uuid'] = (string) Str::uuid();
                    $data['created_by'] = $userId;
                    Item::create($data);
                }
            }
        });
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
