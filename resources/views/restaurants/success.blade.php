@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12" style="max-width: 700px; margin: 0 auto;">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden border">
        <div class="bg-green-500 text-white text-center py-6" style="background: #10b981; color: white; padding: 30px; text-align: center;">
            <div style="font-size: 50px; margin-bottom: 10px;">✅</div>
            <h1 class="text-3xl font-bold">Đặt Bàn & Thanh Toán Thành Công!</h1>
            <p class="mt-2">Nhà hàng đã nhận được thông tin giữ chỗ của bạn.</p>
        </div>

        <div class="p-8" style="padding: 30px;">
            <h3 class="text-xl font-bold mb-4 border-b pb-2">Thông tin Đơn đặt bàn (#{{ $booking->transaction_id }})</h3>

            <table style="width: 100%; text-align: left; line-height: 2;">
                <tr>
                    <th style="width: 40%; color: #666;">Nhà hàng:</th>
                    <td style="font-weight: bold;">{{ $booking->restaurant->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th style="color: #666;">Thời gian đến:</th>
                    <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }} lúc {{ $booking->booking_time }}</td>
                </tr>
                <tr>
                    <th style="color: #666;">Số lượng khách:</th>
                    <td>{{ $booking->guests_count }} người</td>
                </tr>
                <tr>
                    <th style="color: #666;">Số tiền đã cọc:</th>
                    <td style="color: #e50050; font-weight: bold;">{{ number_format($booking->deposit_amount) }} VNĐ</td>
                </tr>
                <tr>
                    <th style="color: #666;">Trạng thái:</th>
                    <td><span style="background: #d1fae5; color: #065f46; padding: 5px 10px; border-radius: 15px; font-size: 14px; font-weight: bold;">Đã xác nhận (Confirmed)</span></td>
                </tr>
            </table>

            <div class="mt-8 text-center" style="margin-top: 30px; text-align: center;">
                <a href="{{ route('restaurants.index') }}" style="display: inline-block; background: #e50050; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">Về danh sách nhà hàng</a>
            </div>
        </div>
    </div>
</div>
@endsection