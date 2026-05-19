<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'slug', 
        'description', 
        'price', 
        'stock', 
        'image', 
        'category_id', 
        'branch_id', 
        'user_id', 
        'is_active'
    ];

    /**
     * Sản phẩm thuộc về một danh mục
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Sản phẩm được bán tại một chi nhánh AEON cụ thể
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Sản phẩm thuộc quyền quản lý của một User (Đối tác Role 4)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}