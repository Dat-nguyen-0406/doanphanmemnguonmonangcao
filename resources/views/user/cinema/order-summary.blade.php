<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng - AEON Cinema</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        .cinema-red { color: #e60012; }
        .bg-cinema-red { background-color: #e60012; }
        .cinema-gradient { background: linear-gradient(135deg, #e60012 0%, #ff6b35 100%); }
        .step-badge { 
            width: 40px; 
            height: 40px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            border-radius: 50%; 
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-5xl mx-auto px-4 py-4">
            <h1 class="text-2xl font-black text-gray-900">
                <i class="fa-solid fa-receipt cinema-red mr-2"></i> Xác nhận đơn hàng
            </h1>
        </div>
    </header>

    <!-- Progress Indicator -->
    <div class="bg-white border-b">
        <div class="max-w-5xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="step-badge bg-green-500 text-white">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Bước 1</p>
                        <p class="font-bold text-gray-900">Chọn ghế</p>
                    </div>
                </div>

                <div class="flex-1 h-1 bg-gray-200 mx-4"></div>

                <div class="flex items-center space-x-3">
                    <div class="step-badge bg-cinema-red text-white">
                        2
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Bước 2</p>
                        <p class="font-bold text-gray-900">Xác nhận</p>
                    </div>
                </div>

                <div class="flex-1 h-1 bg-gray-200 mx-4"></div>

                <div class="flex items-center space-x-3">
                    <div class="step-badge bg-gray-300 text-gray-500">
                        3
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Bước 3</p>
                        <p class="font-bold text-gray-900">Thanh toán</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Order Info -->
            <div class="lg:col-span-2">
                <!-- Movie Info Card -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">
                        <i class="fa-solid fa-film cinema-red mr-2"></i> Thông tin suất chiếu
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Movie Title -->
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-2">Phim</p>
                            <p class="text-lg font-black text-gray-900">{{ $booking->showtime->movie->title }}</p>
                        </div>

                        <!-- Branch -->
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-2">Rạp chiếu</p>
                            <p class="font-bold text-gray-900">
                                <i class="fa-solid fa-location-dot cinema-red mr-2"></i>
                                {{ $booking->showtime->branch->name }}
                            </p>
                        </div>

                        <!-- Time -->
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-2">Thời gian</p>
                            <p class="font-bold text-gray-900">
                                <i class="fa-solid fa-clock cinema-red mr-2"></i>
                                {{ $booking->showtime->start_time->format('H:i') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $booking->showtime->start_time->format('l, d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Seats Info Card -->
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100 mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">
                        <i class="fa-solid fa-chair cinema-red mr-2"></i> Ghế đã chọn
                    </h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                        @foreach($booking->seats as $seat)
                            @php
                                $price = $booking->seats()
                                    ->where('seats.id', $seat->id)
                                    ->first()
                                    ->pivot
                                    ->price ?? ($seat->type === 'vip' ? $booking->showtime->price * 1.5 : $booking->showtime->price);
                            @endphp
                            <div class="p-4 bg-gradient-to-br from-red-50 to-red-100 rounded-lg border-2 border-red-200">
                                <div class="text-center">
                                    <p class="text-sm font-bold text-gray-900">Ghế {{ $seat->seat_number }}</p>
                                    <p class="text-xs text-gray-600 mt-1">Hàng {{ $seat->row }}</p>
                                    @if($seat->type === 'vip')
                                        <span class="inline-block mt-2 px-2 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded">👑 VIP</span>
                                    @endif
                                    <p class="text-sm font-bold text-cinema-red mt-2">{{ number_format($price, 0) }} đ</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <p class="text-sm text-gray-600">
                        <i class="fa-solid fa-info-circle mr-2"></i>
                        Tổng cộng <strong>{{ $booking->seats->count() }} ghế</strong>
                    </p>
                </div>

                <!-- Important Notice -->
                <div class="bg-amber-50 border-2 border-amber-200 rounded-2xl p-6 mb-6">
                    <div class="flex space-x-4">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-bell text-xl text-amber-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-amber-900 mb-2">Lưu ý quan trọng</h4>
                            <ul class="text-sm text-amber-800 space-y-1">
                                <li>✓ Ghế sẽ được giữ trong <strong>10 phút</strong></li>
                                <li>✓ Vui lòng thanh toán đễ hoàn tất đặt vé</li>
                                <li>✓ Bạn sẽ nhận được mã vé qua email sau khi thanh toán</li>
                                <li>✓ Mã vé có thể sử dụng luôn tại rạp</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 sticky top-24">
                    <!-- Order ID -->
                    <div class="text-center pb-6 border-b border-gray-100 mb-6">
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-2">Mã đặt hàng</p>
                        <p class="font-black text-gray-900 text-lg">#{{ $booking->id }}</p>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="space-y-4 mb-6">
                        @php
                            $regularSeats = $booking->seats->filter(fn($s) => $s->type !== 'vip');
                            $vipSeats = $booking->seats->filter(fn($s) => $s->type === 'vip');
                            
                            $regularPrice = $regularSeats->sum(fn($s) => 
                                $s->pivot->price ?? ($booking->showtime->price)
                            );
                            
                            $vipPrice = $vipSeats->sum(fn($s) => 
                                $s->pivot->price ?? ($booking->showtime->price * 1.5)
                            );
                        @endphp

                        @if($regularSeats->count() > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ $regularSeats->count() }} ghế thường</span>
                                <span class="font-bold text-gray-900">{{ number_format($regularPrice, 0) }} đ</span>
                            </div>
                        @endif

                        @if($vipSeats->count() > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">{{ $vipSeats->count() }} ghế VIP</span>
                                <span class="font-bold text-gray-900">{{ number_format($vipPrice, 0) }} đ</span>
                            </div>
                        @endif

                        <div class="border-t border-gray-100 pt-4 mt-4">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Tổng cộng:</span>
                                <span class="cinema-red">{{ number_format($booking->total_price, 0) }} đ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="mb-6 pb-6 border-b border-gray-100">
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-3">Phương thức thanh toán</p>
                        <div class="space-y-2">
                            <label class="flex items-center p-3 border-2 border-red-500 rounded-lg cursor-pointer bg-red-50">
                                <input type="radio" name="payment_method" value="vnpay" checked class="mr-3">
                                <div>
                                    <p class="font-bold text-gray-900">VNPay</p>
                                    <p class="text-xs text-gray-500">Thanh toán qua VNPay</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <form action="{{ route('payment.create') }}" method="POST">
                        @csrf
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        <button type="submit" class="w-full bg-cinema-red hover:bg-red-700 text-white py-3 rounded-xl font-bold transition-all shadow-md hover:shadow-lg mb-3">
                            <i class="fa-solid fa-credit-card mr-2"></i> Thanh toán ngay
                        </button>
                    </form>

                    <a href="javascript:history.back()" class="block text-center border-2 border-gray-300 text-gray-700 hover:border-gray-400 hover:bg-gray-50 py-2 rounded-lg font-bold transition-colors">
                        ← Quay lại
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="bg-white border-t mt-12">
        <div class="max-w-5xl mx-auto px-4 py-8 text-center text-sm text-gray-600">
            <p>
                <i class="fa-solid fa-shield mr-2 text-cinema-red"></i>
                Thanh toán an toàn 100% | Được bảo vệ bởi VNPay
            </p>
        </div>
    </div>
</body>
</html>
