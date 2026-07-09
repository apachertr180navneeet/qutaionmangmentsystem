<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id', 'item_id', 'item_name', 'description', 'hsn_code',
        'unit', 'quantity', 'rate', 'discount_percentage', 'discount_amount',
        'taxable_value', 'tax_percentage', 'cgst_percentage', 'sgst_percentage',
        'igst_percentage', 'cgst_amount', 'sgst_amount', 'igst_amount',
        'total', 'sort_order',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
