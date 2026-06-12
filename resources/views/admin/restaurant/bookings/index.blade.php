@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-black text-gray-800">📋 Quản lý Đặt bàn</h1>
        <p class="text-sm text-gray-500 mt-1">Tất cả đơn đặt bàn nhà hàng trong hệ thống</p>
    </div>
</div>

{{-- FILTER --}}
<div class="bg-white rounded-2xl shadow-sm p-5 mb-6">
    <form action="{{ route('admin.restaurant.bookings') }}" method="GET" class="flex gap-3 flex-wrap items-end">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nhà hàng</label>
            <select name="restaurant_id" class="px-3 py-2 border-2 border-gray-200 rounded-xl text-sm bg-white outline-none">
                <option value="">Tất cả</option>
                @foreach($restaurants as $r)
                    <option value="{{ $r->id }}" {{ request('restaurant_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Trạng thái</label>
            <select name="status" class="px-3 py-2 border-2 border-gray-200 rounded-xl text-sm bg-white outline-none">
                <option value="">Tất cả</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Chờ thanh toán</option>
                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>✅ Đã xác nhận</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>❌ Đã hủy</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>🎉 Hoàn thành</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ngày</label>
            <input type="date" name="date" value="{{ request('date') }}"
                   class="px-3 py-2 border-2 border-gray-200 rounded-xl text-sm outline-none">
        </div>
        <button type="submit" class="bg-pink-600 text-white font-bold px-5 py-2 rounded-xl text-sm">Lọc</button>
        @if(request()->hasAny(['restaurant_id','status','date']))
        <a href="{{ route('admin.restaurant.bookings') }}" class="bg-gray-100 text-gray-600 font-bold px-4 py-2 rounded-xl text-sm">✕ Xoá</a>
        @endif
    </form>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5">✅ {{ session('success') }}</div>
@endif

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Khách hàng</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Nhà hàng & Bàn</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Thời gian</th>
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Khách</th>
                <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Tiền cọc</th>
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Trạng thái</th>
                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($bookings as $b)
            <tr class="hover:bg-slate-50">
                <td class="px-5 py-3">
                    <p class="font-bold text-gray-800">{{ $b->user->name ?? 'N/A' }}</p>
                    <p class="text-xs text-gray-400">{{ $b->user->email ?? '' }}</p>
                </td>
                <td class="px-5 py-3">
                    <p class="font-bold text-gray-700">{{ $b->restaurant->name ?? '—' }}</p>
                    <p class="text-xs text-gray-400">
                        {{ $b->table ? ($b->table->label ?? 'Bàn ' . $b->table->table_number) : '—' }}
                    </p>
                </td>
                <td class="px-5 py-3">
                    <p class="font-bold text-gray-700">{{ \Carbon\Carbon::parse($b->booking_date)->format('d/m/Y') }}</p>
                    <p class="text-xs text-gray-400">{{ $b->booking_time }}</p>
                </td>
                <td class="px-5 py-3 text-center font-bold">{{ $b->guests_count }} người</td>
                <td class="px-5 py-3 text-right font-bold text-pink-600">{{ number_format($b->deposit_amount + ($b->pre_order_amount ?? 0)) }}đ</td>
                <td class="px-5 py-3 text-center">
                    @php
                        $statusMap = [
                            'pending'   => ['bg-yellow-100 text-yellow-700', '⏳ Chờ TT'],
                            'confirmed' => ['bg-green-100 text-green-700',  '✅ Đã xác nhận'],
                            'cancelled' => ['bg-red-100 text-red-700',      '❌ Đã hủy'],
                            'completed' => ['bg-blue-100 text-blue-700',    '🎉 Hoàn thành'],
                        ];
                        [$cls, $lbl] = $statusMap[$b->status] ?? ['bg-gray-100 text-gray-500', $b->status];
                    @endphp
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full {{ $cls }}">{{ $lbl }}</span>
                </td>
                <td class="px-5 py-3 text-center">
                    <form action="{{ route('admin.restaurant.bookings.status', $b->id) }}" method="POST" class="inline-flex items-center gap-1">
                        @csrf @method('PATCH')
                        <select name="status" class="text-xs border border-gray-200 rounded-lg px-2 py-1 bg-white outline-none">
                            <option value="pending" {{ $b->status == 'pending' ? 'selected' : '' }}>⏳ Chờ TT</option>
                            <option value="confirmed" {{ $b->status == 'confirmed' ? 'selected' : '' }}>✅ Xác nhận</option>
                            <option value="completed" {{ $b->status == 'completed' ? 'selected' : '' }}>🎉 Hoàn thành</option>
                            <option value="cancelled" {{ $b->status == 'cancelled' ? 'selected' : '' }}>❌ Hủy</option>
                        </select>
                        <button type="submit" class="text-xs bg-pink-100 text-pink-700 hover:bg-pink-600 hover:text-white font-bold px-2 py-1 rounded-lg transition">Lưu</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                    Không có đơn đặt bàn nào.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($bookings->hasPages())
    <div class="px-5 py-3 border-t border-slate-100">
        {{ $bookings->links() }}
    </div>
    @endif
</div>
@endsection
