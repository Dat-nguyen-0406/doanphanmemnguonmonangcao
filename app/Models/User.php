<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'role',
        'image',     // từ kethop (avatar profile)
        'branch_id', // từ doanphanmem (Cinema Partner liên kết chi nhánh)
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cinema Partner (role 2) liên kết với một chi nhánh AEON
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Kiểm tra xem user có phải admin không (role 1)
     */
    public function isAdmin(): bool
    {
        return $this->role == 1;
    }

    /**
     * Kiểm tra xem user có phải cinema partner không (role 2)
     */
    public function isCinemaPartner(): bool
    {
        return $this->role == 2;
    }
}
