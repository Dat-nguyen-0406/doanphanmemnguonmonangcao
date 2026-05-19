@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12" style="max-width: 600px; margin: 0 auto; text-align: center;">
    <div class="bg-white rounded-lg shadow-xl p-8 border-t-4 border-blue-500" style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-top: 5px solid #3b82f6;">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Cổng Thanh Toán AEON Pay</h2>
        <p class="text-gray-500 mb-8">Vui lòng hoàn tất cọc để giữ bàn</p>

        <div class="bg-gray-50 p-6 rounded mb-8 text-left" style="background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: left; margin-bottom: 20px;">
            <p><strong>Mã giao dịch:</strong> <span class="text-blue-600">{{ $booking->transaction_id }}</span></p>
            <p><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }} - {{ $booking->booking_time }}</p>
            <p><strong>Số tiền cần thanh toán:</strong> <span class="text-2xl text-red-600 font-bold block mt-2" style="color: #e50050; font-size: 24px; font-weight: bold;">{{ number_format($booking->deposit_amount) }} VNĐ</span></p>
        </div>




        <!-- Thanh toan bang vNpay -->
        <form action="{{ route('booking.vnpay.process', $booking->id) }}" method="POST">
            @csrf
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors" style="width: 100%; background: #005baa; color: white; padding: 15px; border: none; border-radius: 8px; font-size: 18px; cursor: pointer;">
                Thanh toán qua VNPAY
            </button>
        </form>
    </div>
</div>
@endsection