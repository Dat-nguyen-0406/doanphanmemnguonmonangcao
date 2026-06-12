<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Order;

class ProfileController extends Controller
{
    public function index() 
    {
        $user = Auth::user();
        
        $orders = Order::with(['orderDetails.product'])
                    ->where('user_id', $user->id)
                    ->latest()
                    ->get();
        
        return view('user.profile.index', compact('user', 'orders'));
    }

    public function update(Request $request) 
{
    $user = Auth::user();
    
    $request->validate([
        'name'    => 'required|string|max:255',
        'phone'   => 'required|string|max:15',
        'email'   => 'required|email|unique:users,email,' . $user->id,
        'address' => 'nullable|string',
        'avatar'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Đây là tên input từ Form
        'password'=> 'nullable|min:6|confirmed'
    ]);

    $data = $request->except(['avatar', 'password']);

    if ($request->hasFile('avatar')) {
        // Kiểm tra và xóa file cũ trong database (cột image)
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        // Lưu vào thư mục avatars và gán giá trị cho key 'image' để update vào DB
        $data['image'] = $request->file('avatar')->store('avatars', 'public');
    }
    
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }
   

    $user->update($data);

    return back()->with('success', 'Thông tin cá nhân tại AEON Mall đã được cập nhật!');
}
}