<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name', 'email', 'phone', 'address', 'city', 'state',
        'zip_code', 'country', 'gst_number', 'pan_number', 'logo',
        'terms_conditions', 'signature',
    ];
}
