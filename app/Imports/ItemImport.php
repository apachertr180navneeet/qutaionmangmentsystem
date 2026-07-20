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

        foreach ($rows as $row) {
            if (!empty($row['name'])) {
                $names[] = trim($row['name']);
            }
        }

        if (empty($names)) {
            return;
        }

        // Fetch existing items in a batch query to optimize performance by name
        $existingItems = Item::query()->whereIn('name', $names)->get();

        // Map existing items by name for O(1) lookups
        $existingByName = [];
        foreach ($existingItems as $item) {
            $existingByName[strtolower($item->name)] = $item;
        }

        $userId = auth()->id();

        DB::transaction(function () use ($rows, &$existingByName, $userId) {
            foreach ($rows as $row) {
                if (empty($row['name'])) {
                    continue;
                }

                $name = trim($row['name']);
                $sku = !empty($row['sku']) ? trim($row['sku']) : null;

                // Match only by name
                $item = null;
                if (isset($existingByName[strtolower($name)])) {
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
                    $item = Item::create($data);

                    // Add to the local lookup array to prevent duplicate inserts in the same chunk
                    $existingByName[strtolower($name)] = $item;
                }
            }
        });
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
