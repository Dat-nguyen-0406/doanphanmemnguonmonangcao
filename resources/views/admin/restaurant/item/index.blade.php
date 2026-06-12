@extends('layouts.admin')

@section('content')

{{-- HEADER --}}
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
    <div>
        <h1 class="text-2xl font-black text-gray-800">🍽️ Quản lý Nhà hàng</h1>
        <p class="text-sm text-gray-500 mt-1">
            @if(Auth::user()->role == 1)
                Toàn bộ nhà hàng trong hệ thống AEON Mall
            @else
                Nhà hàng thuộc chi nhánh của bạn
            @endif
        </p>
    </div>
    <a href="{{ route('admin.restaurant.create') }}"
       class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-orange-500 transition-all shadow-lg flex items-center gap-2 w-fit">
        <i class="fa-solid fa-plus"></i> THÊM NHÀ HÀNG
    </a>
</div>

{{-- FLASH MESSAGE --}}
@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 flex items-center gap-2">
    <i class="fa-solid fa-circle-check text-green-500"></i> {{ session('success') }}
</div>
@endif

{{-- STATS ROW --}}
@php
    $total     = $restaurants->total();
    $active    = $restaurants->getCollection()->where('is_active', true)->count();
    $totalBk   = $restaurants->getCollection()->sum('bookings_count');
    $totalTb   = $restaurants->getCollection()->sum('tables_count');
@endphp
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tổng nhà hàng</p>
        <p class="text-3xl font-black text-gray-800">{{ $total }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Đang hoạt động</p>
        <p class="text-3xl font-black text-green-600">{{ $active }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tổng lượt đặt bàn</p>
        <p class="text-3xl font-black text-orange-500">{{ $totalBk }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-5">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Tổng số bàn</p>
        <p class="text-3xl font-black text-blue-500">{{ $totalTb }}</p>
    </div>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nhà hàng</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Chi nhánh</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Loại ẩm thực</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Số bàn</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Đặt bàn</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
    @forelse($restaurants as $r)
    <tr class="hover:bg-slate-50/80 transition-colors">
        <td class="px-5 py-4">
            <div class="flex items-center gap-3">
                @if($r->image_url)
                    <img src="{{ $r->image_url }}" class="w-11 h-11 rounded-xl object-cover shadow-sm bg-gray-100">
                @else
                    <div class="w-11 h-11 rounded-xl bg-pink-50 text-pink-500 flex items-center justify-center font-bold text-sm">
                        {{ Str::upper(Str::substr($r->name, 0, 2)) }}
                    </div>
                @endif
                <div>
                    <span class="font-bold text-gray-800 text-sm block">{{ $r->name }}</span>
                    <span class="text-[11px] text-gray-400 block mt-0.5"><i class="fa-solid fa-location-dot mr-0.5"></i> {{ $r->branch->name ?? 'Chưa xác định' }}</span>
                </div>
            </div>
        </td>
        <td class="px-5 py-4 text-sm text-gray-600 font-medium">{{ $r->cuisine_type ?? '—' }}</td>
        <td class="px-5 py-4 text-center">
            <span class="bg-blue-50 text-blue-600 font-bold px-2.5 py-1 rounded-lg text-xs">
                {{ $r->tables_count }} bàn
            </span>
        </td>
        <td class="px-5 py-4 text-center">
            <span class="bg-purple-50 text-purple-600 font-bold px-2.5 py-1 rounded-lg text-xs">
                {{ $r->bookings_count }} lượt
            </span>
        </td>
        <td class="px-5 py-4 text-center">
            @if($r->is_active)
                <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 px-2.5 py-1 rounded-full text-xs font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span> Mở cửa
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-500 px-2.5 py-1 rounded-full text-xs font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Đóng cửa
                </span>
            @endif
        </td>

        {{-- CỘT HÀNH ĐỘNG THAY ĐỔI DỰA THEO QUYỀN VÀ TRẠNG THÁI SIDEBAR --}}
        <td class="px-5 py-4">
            <div class="flex items-center justify-end gap-2">
                @if(Auth::user()->role == 1)
                    {{-- NẾU LÀ ADMIN TỔNG ĐANG CLICK TỪ SIDEBAR XEM BÀN / MENU --}}
                    @if(request()->query('section') == 'tables')
                        <a href="{{ route('admin.restaurant.tables', $r->id) }}" 
                           class="px-3 py-1.5 rounded-xl bg-pink-50 text-pink-600 hover:bg-pink-600 hover:text-white text-xs font-bold transition flex items-center gap-1">
                            <i class="fa-solid fa-chair text-[10px]"></i> Xem Sơ Đồ Bàn
                        </a>
                    @elseif(request()->query('section') == 'menu')
                        <a href="{{ route('admin.restaurant.menu', $r->id) }}" 
                           class="px-3 py-1.5 rounded-xl bg-pink-50 text-pink-600 hover:bg-pink-600 hover:text-white text-xs font-bold transition flex items-center gap-1">
                            <i class="fa-solid fa-book-open text-[10px]"></i> Xem Thực Đơn
                        </a>
                    @else
                        {{-- Chế độ tổng quan bình thường của Admin: hiển thị các nút điều hướng cơ bản --}}
                        <a href="{{ route('admin.restaurant.tables', $r->id) }}" title="Xem Bàn"
                           class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-pink-600 hover:text-white text-slate-600 flex items-center justify-center transition">
                            <i class="fa-solid fa-table-cells-large text-xs"></i>
                        </a>
                        <a href="{{ route('admin.restaurant.menu', $r->id) }}" title="Xem Menu"
                           class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-pink-600 hover:text-white text-slate-600 flex items-center justify-center transition">
                            <i class="fa-solid fa-book-open text-xs"></i>
                        </a>
                        <a href="{{ route('admin.restaurant.edit', $r->id) }}" title="Sửa nhà hàng"
                           class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </a>
                        <form action="{{ route('admin.restaurant.destroy', $r->id) }}" method="POST" onsubmit="return confirm('Xoá nhà hàng này?')">
                            @csrf @method('DELETE')
                            <button type="submit" title="Xoá" class="w-8 h-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    @endif
                @else
                    {{-- NẾU LÀ ĐỐI TÁC QUÁN ĂN (ROLE 3): Có đầy đủ tính năng thêm/sửa/xoá của riêng họ --}}
                    <a href="{{ route('admin.restaurant.tables', $r->id) }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition">
                        <i class="fa-solid fa-table-cells-large text-xs"></i>
                    </a>
                    <a href="{{ route('admin.restaurant.menu', $r->id) }}" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition">
                        <i class="fa-solid fa-book-open text-xs"></i>
                    </a>
                    <a href="{{ route('admin.restaurant.edit', $r->id) }}" class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition">
                        <i class="fa-solid fa-pen text-xs"></i>
                    </a>
                @endif
            </div>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="6" class="px-5 py-16 text-center">
            <div class="flex flex-col items-center gap-3 text-gray-400">
                <i class="fa-solid fa-store-slash text-4xl"></i>
                <p class="font-bold text-gray-500">Chưa có nhà hàng nào</p>
            </div>
        </td>
    </tr>
    @endforelse
</tbody>
        </table>
    </div>

    {{-- PAGINATION --}}
    @if($restaurants->hasPages())
    <div class="px-5 py-4 border-t border-slate-100">
        {{ $restaurants->links() }}
    </div>
    @endif
</div>

@endsection