<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomerTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'company_name',
            'contact_person',
            'email',
            'phone',
            'alt_phone',
            'gst_number',
            'billing_address',
            'shipping_address',
            'city',
            'state',
            'zip_code',
            'country',
            'notes',
            'status'
        ];
    }

    public function array(): array
    {
        return [
            [
                'ABC Corp',
                'John Doe',
                'john@example.com',
                '1234567890',
                '',
                'GSTIN123',
                '123 Main St',
                '123 Main St',
                'New York',
                'NY',
                '10001',
                'USA',
                'Sample Note',
                'Active'
            ]
        ];
    }
}
