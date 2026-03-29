<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id', 
        'name', 
        'address', 
        'image_url', 
        'map_link', 
        'description'
    ];

    /**
     * Một Chi nhánh chỉ thuộc về một Thành phố nhất định (Quan hệ Nghịch đảo)
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}