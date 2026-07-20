<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasUuid;

class Quotation extends Model
{
    use SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid', 'quotation_number', 'customer_id', 'user_id', 'revision_number',
        'parent_id', 'subtotal', 'discount_type', 'discount_value', 'discount_amount',
        'tax_type', 'tax_rate', 'cgst_percentage', 'sgst_percentage', 'igst_percentage',
        'cgst_amount', 'sgst_amount', 'igst_amount', 'round_off', 'grand_total',
        'status', 'valid_until', 'notes', 'terms_conditions', 'created_by',
    ];

    protected $casts = [
        'valid_until' => 'date',
        'subtotal' => 'float',
        'discount_value' => 'float',
        'discount_amount' => 'float',
        'tax_rate' => 'float',
        'cgst_percentage' => 'float',
        'sgst_percentage' => 'float',
        'igst_percentage' => 'float',
        'cgst_amount' => 'float',
        'sgst_amount' => 'float',
        'igst_amount' => 'float',
        'round_off' => 'float',
        'grand_total' => 'float',
        'revision_number' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $year = now()->format('Y');
            $lastQuotation = static::where('quotation_number', 'like', "Q-{$year}-%")
                ->orderBy('quotation_number', 'desc')
                ->lockForUpdate()
                ->first();

            if ($lastQuotation) {
                $lastNumber = (int) substr($lastQuotation->quotation_number, -4);
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '0001';
            }

            $model->quotation_number = "Q-{$year}-{$newNumber}";
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function emailLogs()
    {
        return $this->hasMany(EmailLog::class);
    }

    public function parent()
    {
        return $this->belongsTo(Quotation::class, 'parent_id');
    }

    public function revisions()
    {
        return $this->hasMany(Quotation::class, 'parent_id');
    }
}
