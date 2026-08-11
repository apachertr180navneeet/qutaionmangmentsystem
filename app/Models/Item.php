<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Item extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid', 'name', 'sku', 'description', 'unit', 'mrp', 'sdp', 'rate', 'tax_percentage',
        'is_active', 'image', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'mrp' => 'float',
        'sdp' => 'float',
        'rate' => 'float',
    ];

    public function getImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        if (\Illuminate\Support\Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }
        return asset($value);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quotationItems()
    {
        return $this->hasMany(QuotationItem::class);
    }
}
