<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = ['branch_id', 'name', 'cuisine_type', 'description', 'image_url', 'is_active'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
