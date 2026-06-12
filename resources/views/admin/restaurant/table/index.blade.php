@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.restaurant.index') }}" class="text-sm text-pink-600 hover:underline">← Danh sách nhà hàng</a>
        <h1 class="text-2xl font-black text-gray-800 mt-1">🪑 Quản lý bàn — {{ $restaurant->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ $tables->count() }} bàn · {{ $tables->where('is_active', true)->count() }} đang hoạt động</p>
    </div>
    <a href="{{ route('admin.restaurant.tables.create', $restaurant->id) }}"
       class="bg-pink-600 hover:bg-pink-700 text-white font-bold py-2.5 px-5 rounded-xl text-sm transition-all shadow-lg">
        + Thêm bàn
    </a>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 flex items-center gap-2">
    ✅ {{ session('success') }}
</div>
@endif

{{-- SƠ ĐỒ THEO TẦNG --}}
@php $floors = $tables->pluck('floor')->unique()->sort()->values(); @endphp

@if($floors->count() > 1)
<div style="display:flex;gap:8px;margin-bottom:16px;">
    @foreach($floors as $f)
    <button onclick="showFloor({{ $f }})" id="tab-{{ $f }}"
            class="floor-tab px-4 py-2 rounded-xl text-sm font-bold border-2 border-gray-200 text-gray-600 hover:border-pink-500 hover:text-pink-600 transition">
        Tầng {{ $f }}
    </button>
    @endforeach
</div>
@endif

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Bàn</th>
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Tầng</th>
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Sức chứa</th>
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Hình dạng</th>
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($tables as $table)
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-5 py-3">
                    <p class="font-bold text-gray-800">{{ $table->label ?? ('Bàn ' . $table->table_number) }}</p>
                    <p class="text-xs text-gray-400">#{{ $table->table_number }}</p>
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="bg-blue-50 text-blue-600 text-xs font-bold px-2.5 py-1 rounded-full">Tầng {{ $table->floor }}</span>
                </td>
                <td class="px-5 py-3 text-center font-bold text-gray-700">{{ $table->capacity }} người</td>
                <td class="px-5 py-3 text-center">
                    @php $shapes = ['square' => '⬜ Vuông', 'round' => '🔵 Tròn', 'long' => '⬛ Dài']; @endphp
                    <span class="text-xs text-gray-600">{{ $shapes[$table->shape] ?? $table->shape }}</span>
                </td>
                <td class="px-5 py-3 text-center">
                    @if($table->is_active)
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">● Hoạt động</span>
                    @else
                        <span class="bg-gray-100 text-gray-500 text-xs font-bold px-2.5 py-1 rounded-full">● Tạm đóng</span>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.restaurant.tables.edit', [$restaurant->id, $table->id]) }}"
                           class="text-xs bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold px-3 py-1.5 rounded-lg transition">
                            ✏️ Sửa
                        </a>
                        <form action="{{ route('admin.restaurant.tables.destroy', [$restaurant->id, $table->id]) }}" method="POST"
                              onsubmit="return confirm('Xoá bàn này?')">
                            @csrf @method('DELETE')
                            <button class="text-xs bg-red-50 text-red-500 hover:bg-red-500 hover:text-white font-bold px-3 py-1.5 rounded-lg transition">🗑</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                    Chưa có bàn nào.
                    <a href="{{ route('admin.restaurant.tables.create', $restaurant->id) }}" class="text-pink-600 font-bold">+ Thêm ngay</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4 flex gap-3">
    <a href="{{ route('admin.restaurant.menu', $restaurant->id) }}"
       class="bg-orange-100 text-orange-700 hover:bg-orange-200 font-bold px-5 py-2.5 rounded-xl text-sm transition">
        🍜 Quản lý Menu
    </a>
    <a href="{{ route('admin.restaurant.bookings') }}?restaurant_id={{ $restaurant->id }}"
       class="bg-blue-100 text-blue-700 hover:bg-blue-200 font-bold px-5 py-2.5 rounded-xl text-sm transition">
        📋 Xem đặt bàn
    </a>
</div>
@endsection
