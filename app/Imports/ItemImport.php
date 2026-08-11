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
            $id = !empty($row['id']) ? trim($row['id']) : null;
            $sku = !empty($row['sku']) ? trim($row['sku']) : null;
            $name = !empty($row['name']) ? trim($row['name']) : null;

            if (empty($id) && empty($name) && empty($sku)) {
                continue;
            }

            $rate = isset($row['rate']) && $row['rate'] !== '' ? (float) $row['rate'] : null;
            $mrp = isset($row['mrp']) && $row['mrp'] !== '' ? (float) $row['mrp'] : ($rate ?? 0);
            $sdp = isset($row['sdp']) && $row['sdp'] !== '' ? (float) $row['sdp'] : null;

            // Find existing item by ID, SKU or Name
            $existingItem = null;
            if ($id) {
                $existingItem = Item::find($id);
            }
            if (!$existingItem && $sku) {
                $existingItem = Item::where('sku', $sku)->first();
            }
            if (!$existingItem && $name) {
                $existingItem = Item::where('name', $name)->first();
            }

            if ($existingItem) {
                // Update MRP, SDP and other provided fields for existing item
                $updateData = ['updated_at' => $now];
                if ($name !== null) {
                    $updateData['name'] = $name;
                }
                if ($sku !== null) {
                    $updateData['sku'] = $sku;
                }
                if ($mrp !== null) {
                    $updateData['mrp'] = $mrp;
                }
                if ($sdp !== null) {
                    $updateData['sdp'] = $sdp;
                }
                if ($rate !== null) {
                    $updateData['rate'] = $rate;
                }
                if (!empty($row['description'])) {
                    $updateData['description'] = $row['description'];
                }
                if (!empty($row['unit'])) {
                    $updateData['unit'] = $row['unit'];
                }
                if (isset($row['tax_percentage']) && $row['tax_percentage'] !== '') {
                    $updateData['tax_percentage'] = $row['tax_percentage'];
                }
                if (isset($row['is_active']) && $row['is_active'] !== '') {
                    $updateData['is_active'] = strtolower($row['is_active']) == 'active' ? 1 : 0;
                }

                $existingItem->update($updateData);
            } else {
                // Prepare new item insert data
                if (empty($name)) {
                    continue;
                }
                $insertData[] = [
                    'uuid'           => (string) Str::uuid(),
                    'name'           => $name,
                    'sku'            => $sku,
                    'description'    => $row['description'] ?? null,
                    'unit'           => $row['unit'] ?? 'pcs',
                    'mrp'            => $mrp ?? 0,
                    'sdp'            => $sdp ?? 0,
                    'rate'           => $rate ?? 0,
                    'tax_percentage' => $row['tax_percentage'] ?? 0,
                    'is_active'      => isset($row['is_active']) ? (strtolower($row['is_active']) == 'active' ? 1 : 0) : 1,
                    'image'          => null,
                    'created_by'     => $userId,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
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
