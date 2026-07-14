<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class CustomerImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Check if company_name is provided, else skip
        if (empty($row['company_name'])) {
            return null;
        }

        // Find existing customer by email, phone, or company name
        $query = Customer::query();
        $query->where(function($q) use ($row) {
            if (!empty($row['email'])) {
                $q->orWhere('email', $row['email']);
            }
            if (!empty($row['phone'])) {
                $q->orWhere('phone', $row['phone']);
            }
            if (!empty($row['company_name'])) {
                $q->orWhere('company_name', $row['company_name']);
            }
        });

        $customer = $query->first();

        $data = [
            'company_name'     => $row['company_name'],
            'contact_person'   => $row['contact_person'] ?? null,
            'email'            => $row['email'] ?? null,
            'phone'            => $row['phone'] ?? null,
            'alt_phone'        => $row['alt_phone'] ?? null,
            'gst_number'       => $row['gst_number'] ?? null,
            'billing_address'  => $row['billing_address'] ?? null,
            'shipping_address' => $row['shipping_address'] ?? null,
            'city'             => $row['city'] ?? null,
            'state'            => $row['state'] ?? null,
            'zip_code'         => $row['zip_code'] ?? null,
            'country'          => $row['country'] ?? null,
            'notes'            => $row['notes'] ?? null,
            'status'           => isset($row['status']) ? (strtolower($row['status']) == 'active' ? 1 : 0) : 1,
        ];

        if ($customer) {
            // Update the existing customer's attributes
            $customer->fill($data);
            return $customer;
        }

        // Create new customer
        $data['uuid'] = (string) Str::uuid();
        $data['created_by'] = auth()->id();
        
        return new Customer($data);
    }
}
