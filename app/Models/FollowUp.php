<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    protected $fillable = [
        'quotation_id', 'user_id', 'follow_up_date', 'follow_up_time',
        'notes', 'status', 'reminder_sent',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'reminder_sent' => 'boolean',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
