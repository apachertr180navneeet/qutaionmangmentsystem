<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Customer extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid', 'company_name', 'contact_person', 'email', 'phone', 'alt_phone',
        'gst_number', 'billing_address', 'shipping_address', 'city', 'state',
        'zip_code', 'country', 'notes', 'status', 'created_by',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
}
