<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name', 'slogan', 'email', 'phone', 'address', 'city', 'state',
        'zip_code', 'country', 'gst_number', 'pan_number', 'discount_amount',
        'logo', 'jg_logo', 'terms_conditions', 'signature',
    ];
}
