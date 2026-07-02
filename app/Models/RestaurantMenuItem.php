<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantMenuItem extends Model
{
    protected $table = 'restaurant_menu_items';

    protected $fillable = [
        'restaurant_id',
        'name',
        'category',
        'price',
        'description',
        'image_url',
        'is_available',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'price' => 'float',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
