<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool {
    return true; // Phải đổi thành true để cho phép sử dụng
}

public function rules(): array {
    return [
        'name'     => 'required|string|max:255',
        'email'    => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed', // password_confirmation
        'phone'    => 'nullable|numeric|digits_between:10,11',
        'address'  => 'nullable|string|max:500',
    ];
}
}
