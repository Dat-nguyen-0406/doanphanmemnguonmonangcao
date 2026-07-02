@extends('layouts.shop') {{-- Hoặc layout user của bạn --}}

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-8">
        <a href="{{ route('profile.orders.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-pink-600 transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i> Quay lại lịch sử
        </a>
        <div class="text-right">
            <span class="text-xs font-bold uppercase tracking-widest text-gray-400">Mã đơn hàng</span>
            <p class="text-lg font-black text-slate-900">#{{ $order->id }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Ngày đặt hàng</p>
            <p class="text-sm font-bold text-slate-800">{{ $order->created_at->format('d/m/Y H:i') }}</p>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Trạng thái thanh toán</p>
            @if($order->status == 'paid')
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700">
                    Đã thanh toán
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700">
                    Chờ thanh toán
                </span>
            @endif
        </div>

        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Phương thức thanh toán</p>
            <p class="text-sm font-bold text-slate-800">
                {{ $order->vnp_txn_ref ? 'Cổng thanh toán VNPAY' : 'Thanh toán COD' }}
            </p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-gray-50">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider">Danh sách sản phẩm</h3>
        </div>
        <ul class="divide-y divide-gray-100">
            @foreach($order->orderDetails as $detail)
            <li class="p-6 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img class="w-16 h-16 object-cover rounded-xl bg-gray-50 border border-gray-100" 
                         src="{{ asset('storage/' . $detail->product->image) }}" 
                         alt="{{ $detail->product->name }}">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $detail->product->name }}</h4>
                        <p class="text-xs text-gray-400 mt-1">Số lượng: {{ $detail->quantity }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-black text-slate-900">{{ number_format($detail->price) }}đ</p>
                    <p class="text-[10px] text-gray-400 mt-1">Thành tiền: {{ number_format($detail->price * $detail->quantity) }}đ</p>
                </div>
            </li>
            @endforeach
        </ul>

        <div class="bg-slate-50 px-6 py-6 border-t border-gray-100 flex flex-col items-end">
            <div class="w-full sm:w-80 space-y-3">
                <div class="flex items-center justify-between text-xs text-gray-500 font-bold">
                    <span>Tạm tính</span>
                    <span>{{ number_format($order->total_amount) }}đ</span>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 font-bold">
                    <span>Phí vận chuyển</span>
                    <span class="text-green-600 font-bold">Miễn phí</span>
                </div>
                <div class="pt-3 border-t border-gray-200 flex items-center justify-between">
                    <span class="text-sm font-black text-slate-900">Tổng thanh toán</span>
                    <span class="text-xl font-black text-pink-600">{{ number_format($order->total_amount) }}đ</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection