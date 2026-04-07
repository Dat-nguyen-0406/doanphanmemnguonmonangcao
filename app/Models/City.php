<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; 

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug'];

    /**
     * Tự động tạo slug từ tên thành phố trước khi lưu vào Database
     */
    protected static function boot()
    {
        parent::boot();

        // Lắng nghe sự kiện 'creating' (trước khi bản ghi được tạo mới)
        static::creating(function ($city) {
            if (empty($city->slug)) {
                $city->slug = Str::slug($city->name);
            }
        });

        // Nếu Đạt muốn cập nhật slug khi đổi tên thành phố thì thêm sự kiện 'updating'
        static::updating(function ($city) {
            if ($city->isDirty('name')) {
                $city->slug = Str::slug($city->name);
            }
        });
    }

    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
}