<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    // Các cột được phép thêm dữ liệu hàng loạt (Mass Assignment)
    protected $fillable = ['name', 'slug'];

    /**
     * Một Thành phố có thể có nhiều Chi nhánh AEON (Quan hệ 1 - Nhiều)
     */
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}