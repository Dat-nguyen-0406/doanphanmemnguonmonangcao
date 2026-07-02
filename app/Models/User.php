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

    /**
     * QUAN TRỌNG: 'role' và 'branch_id' KHÔNG được đặt trong $fillable.
     * Nếu để trong $fillable, người dùng có thể tự gửi role=1 (Admin)
     * trong form đăng ký để leo thang đặc quyền (mass assignment / privilege escalation).
     * Hai trường này chỉ được set thủ công qua $user->role = ... ở AuthController::changeRole()
     * — không bao giờ qua ::create($request->all()) hoặc tương tự.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'image',     // từ kethop (avatar profile)
    ];

    protected $guarded = ['role', 'branch_id'];

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
