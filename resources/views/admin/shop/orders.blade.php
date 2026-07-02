@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Quản lý Đơn hàng</h1>
            <p class="text-sm text-gray-500 font-medium">Danh sách và cập nhật trạng thái đơn hàng bán hàng online</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-900 text-white border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-center">Mã ĐH</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Khách hàng</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Sản phẩm & Chi tiết</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-right">Tổng tiền</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm font-black text-slate-800">#{{ $order->id }}</span>
                            <div class="text-[10px] text-gray-400 mt-0.5">{{ $order->created_at->format('d/m/Y H:i') }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-800">{{ $order->user->name ?? 'Khách vãng lai' }}</div>
                            <div class="text-xs text-gray-500">{{ $order->user->email ?? 'N/A' }}</div>
                            <div class="text-[11px] text-gray-400 italic mt-0.5">{{ $order->user->phone ?? '' }}</div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="space-y-2 max-w-md">
                                @foreach($order->orderDetails as $detail)
                                    {{-- Nếu không phải Admin, chỉ hiển thị sản phẩm của chính shop đang xem --}}
                                    @if(Auth::user()->role == 1 || ($detail->product && $detail->product->user_id == Auth::id()))
                                    <div class="flex items-start justify-between text-xs bg-slate-50 p-2 rounded-lg border border-slate-100">
                                        <div class="pr-4">
                                            <p class="font-semibold text-slate-800">{{ $detail->product->name ?? 'Sản phẩm đã bị xóa' }}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5">Đơn giá: {{ number_format($detail->price) }}đ</p>
                                        </div>
                                        <span class="text-slate-500 font-bold bg-white px-2 py-0.5 rounded border">x{{ $detail->quantity }}</span>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </td>

                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-black text-pink-600">{{ number_format($order->total_amount) }}đ</span>
                            <div class="text-[10px] text-gray-400 mt-0.5">VNPay Ref: <span class="font-mono">{{ $order->vnp_txn_ref ?? 'Trực tiếp' }}</span></div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            @php
                                $statusLabel = [
                                    'pending'   => ['text' => 'Chờ xử lý', 'css' => 'bg-amber-50 text-amber-600 border border-amber-200'],
                                    'paid'      => ['text' => 'Đã thanh toán', 'css' => 'bg-emerald-50 text-emerald-600 border border-emerald-200'],
                                    'shipping'  => ['text' => 'Đang giao', 'css' => 'bg-blue-50 text-blue-600 border border-blue-200'],
                                    'completed' => ['text' => 'Hoàn thành', 'css' => 'bg-indigo-50 text-indigo-600 border border-indigo-200'],
                                    'cancelled' => ['text' => 'Đã hủy đơn', 'css' => 'bg-rose-50 text-rose-600 border border-rose-200'],
                                ][$order->status] ?? ['text' => $order->status, 'css' => 'bg-gray-50 text-gray-600'];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusLabel['css'] }}">
                                {{ $statusLabel['text'] }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.shop.updateStatus', $order->id) }}" method="POST" class="flex items-center justify-center space-x-1.5">
                                @csrf
                                <select name="status" class="text-xs border border-gray-200 rounded-xl px-2 py-1.5 bg-white font-medium text-slate-700 focus:border-pink-500 outline-none">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                    <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Hủy đơn</option>
                                </select>
                                <button type="submit" class="bg-slate-800 text-white text-[10px] px-2.5 py-1.5 rounded hover:bg-black transition font-bold uppercase">
                                    Lưu
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400 italic">
                            Chưa phát sinh bất kỳ đơn đặt hàng nào trong hệ thống.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection