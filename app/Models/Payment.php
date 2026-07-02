<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id', 'vnp_txn_ref', 'amount', 'bank_code', 'card_type',
        'order_info', 'vnp_response_code', 'vnp_transaction_no', 'pay_date',
        'status', 'vnp_data'
    ];

    protected $casts = [
        'pay_date' => 'datetime',
        'vnp_data' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
