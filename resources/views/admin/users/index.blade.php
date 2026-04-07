@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Hệ thống Tài khoản & Đối tác AEON</h2>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Thành viên</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Liên hệ</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Vai trò hiện tại</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Phân quyền đối tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800">{{ $user->name }}</div>
                        <div class="text-[10px] text-gray-400">ID: #{{ $user->id }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <div>{{ $user->email }}</div>
                        <div class="text-xs italic">{{ $user->phone ?? 'Chưa cập nhật' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $roleData = [
                                0 => ['label' => 'Khách hàng', 'color' => 'bg-gray-100 text-gray-600'],
                                2 => ['label' => 'QL Phim (Cinema)', 'color' => 'bg-red-100 text-red-700'],
                                3 => ['label' => 'QL Quán ăn (Food)', 'color' => 'bg-orange-100 text-orange-700'],
                                4 => ['label' => 'QL Bán hàng (Shop)', 'color' => 'bg-blue-100 text-blue-700'],
                            ][$user->role] ?? ['label' => 'N/A', 'color' => 'bg-gray-100'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold {{ $roleData['color'] }}">
                            {{ $roleData['label'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($user->id !== Auth::id())
                        <form action="{{ route('admin.users.changeRole', $user->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            <select name="role" class="text-xs border border-gray-200 rounded px-2 py-1 outline-none focus:ring-1 focus:ring-pink-500">
                                <option value="0" {{ $user->role == 0 ? 'selected' : '' }}>Khách hàng</option>
                                <option value="2" {{ $user->role == 2 ? 'selected' : '' }}>Đối tác Cinema</option>
                                <option value="3" {{ $user->role == 3 ? 'selected' : '' }}>Đối tác Food</option>
                                <option value="4" {{ $user->role == 4 ? 'selected' : '' }}>Đối tác Online Shop</option>
                            </select>
                            <button type="submit" class="bg-slate-800 text-white text-[10px] px-3 py-1 rounded hover:bg-black transition uppercase font-bold">
                                Lưu
                            </button>
                        </form>
                        @else
                        <span class="text-xs text-gray-400 italic">Đang đăng nhập</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection