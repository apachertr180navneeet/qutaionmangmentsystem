<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id', 'item_id', 'item_name', 'sku', 'mrp', 'sdp',
        'quantity', 'rate', 'discount_percentage', 'discount_amount',
        'total', 'sort_order',
    ];

    protected $casts = [
        'quantity' => 'float',
        'mrp' => 'float',
        'sdp' => 'float',
        'rate' => 'float',
        'discount_percentage' => 'float',
        'discount_amount' => 'float',
        'total' => 'float',
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
