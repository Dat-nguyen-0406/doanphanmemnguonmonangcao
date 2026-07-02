@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Quản lý Lịch Chiếu</h2>
            <p class="text-sm text-gray-500 mt-1">
                @if(Auth::user()->role == 1)
                    Chế độ giám sát: Xem toàn bộ lịch chiếu hệ thống
                @else
                    Danh sách tất cả các lịch chiếu tại chi nhánh của bạn
                @endif
            </p>
        </div>
        
        {{-- 1. ẨN NÚT THÊM VỚI ADMIN TỔNG --}}
        @if(Auth::user()->role != 1)
        <a href="{{ route('admin.showtimes.create') }}" class="bg-slate-800 text-white px-4 py-2 rounded-md text-sm hover:bg-black transition flex items-center">
            <i class="fa-solid fa-plus mr-1 text-xs"></i> Thêm Lịch Chiếu
        </a>
        @endif
    </div>
</div>

@if ($message = Session::get('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center" role="alert">
    <i class="fa-solid fa-check-circle mr-2"></i>
    <span>{{ $message }}</span>
</div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Phim</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Chi Nhánh</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Thời Gian</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Giá Thường</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Giá VIP</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Hành Động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($showtimes as $showtime)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <span class="font-semibold text-gray-800">{{ $showtime->movie->title }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-700">{{ $showtime->branch->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-800">{{ $showtime->start_time->format('d/m/Y') }}</div>
                        <div class="text-xs text-gray-500">{{ $showtime->start_time->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-semibold">{{ number_format($showtime->price_normal, 0, ',', '.') }} VND</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-purple-50 text-purple-700 px-2 py-1 rounded text-xs font-semibold">{{ number_format($showtime->price_vip, 0, ',', '.') }} VND</span>
                    </td>
                    <td class="px-6 py-4 text-center space-x-3">
                        {{-- 2. ĐIỀU KIỆN HIỂN THỊ HÀNH ĐỘNG --}}
                        @if(Auth::user()->role != 1)
                            <a href="{{ route('admin.showtimes.edit', $showtime->id) }}" class="text-slate-400 hover:text-amber-500 transition" title="Sửa lịch chiếu">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            
                            <form action="{{ route('admin.showtimes.destroy', $showtime->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition" 
                                        onclick="return confirm('Xác nhận xóa lịch chiếu?')" title="Xóa lịch chiếu">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        @else
                            <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded uppercase">Chỉ xem</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                        <i class="fa-solid fa-calendar text-4xl mb-2 block"></i>
                        <p>Chưa có lịch chiếu nào trong hệ thống.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $showtimes->links() }}
</div>
@endsection