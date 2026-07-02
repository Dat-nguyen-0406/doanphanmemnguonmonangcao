<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price' // Lưu giá tại thời điểm mua để tránh thay đổi giá sau này
    ];

    // Quan hệ: Chi tiết đơn hàng thuộc về một đơn hàng chính
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Quan hệ: Chi tiết này trỏ tới một sản phẩm cụ thể
    public function product()
{
    return $this->belongsTo(Product::class, 'product_id');
}
}