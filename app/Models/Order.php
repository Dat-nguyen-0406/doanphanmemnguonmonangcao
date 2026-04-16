<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status', // pending, paid, failed, shipped
        'vnp_txn_ref', // Mã giao dịch VNPay
        'note'
    ];

    // Quan hệ: Một đơn hàng thuộc về một người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ: Một đơn hàng có nhiều chi tiết sản phẩm
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}