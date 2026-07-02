<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán - {{ $booking->showtime->movie->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        .aeon-gradient {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('{{ $booking->showtime->branch->image_url ?? "https://images.unsplash.com/photo-1519608487953-e999c86e7455?auto=format&fit=crop&w=1350&q=80" }}');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-50">

    <nav class="bg-white border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16">
            <div class="flex items-center space-x-8">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="bg-[#D82D8B] text-white p-2 rounded font-bold">LOTTE</div>
                    <span class="text-xs text-[#D82D8B] font-semibold leading-tight">CINEMA<br>VIETNAM</span>
                </a>

                <div class="hidden md:flex space-x-6 text-sm font-medium text-gray-700">
                    <a href="#" class="hover:text-[#D82D8B]">Lịch chiếu</a>
                    <a href="#" class="hover:text-[#D82D8B]">Ẩm thực</a>
                    <a href="#" class="hover:text-[#D82D8B]">Vị trí cửa hàng</a>
                </div>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <span class="text-sm font-medium text-gray-700">Chào, {{ Auth::user()->name }}</span>
                    <a href="{{ route('my.bookings') }}" class="text-sm text-blue-600 hover:underline">Đặt vé của tôi</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:underline">Đăng xuất</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-[#D82D8B] hover:underline">Đăng nhập</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="bg-white py-2 border-b">
        <div class="max-w-7xl mx-auto px-4 text-xs text-gray-500 flex items-center space-x-2">
            <a href="{{ route('home') }}" class="hover:text-[#a61d6d]"><i class="fa-solid fa-house"></i></a>
            <span>/</span>
            <span>Hệ thống AEON</span>
            <span>/</span>
            <span>{{ $booking->showtime->branch->city->name }}</span>
            <span>/</span>
            <span class="text-gray-800 font-bold uppercase">{{ $booking->showtime->branch->name }}</span>
            <span>/</span>
            <span class="text-gray-800 font-bold">{{ $booking->showtime->movie->title }}</span>
            <span>/</span>
            <span class="text-gray-800 font-bold">Thanh toán</span>
        </div>
    </div>

    <div class="aeon-gradient h-[200px] relative text-white flex items-end pb-6">
        <div class="max-w-7xl mx-auto px-4 w-full">
            <div class="flex items-center space-x-4">
                <div class="bg-white p-3 rounded shadow-lg w-16 h-16 flex items-center justify-center">
                    <img src="{{ asset('images/aeon-logo.png') }}"
                         class="w-full h-full object-contain opacity-80"
                         alt="AEON">
                </div>

                <div class="space-y-1">
                    <h1 class="text-xl font-bold">{{ $booking->showtime->movie->title }}</h1>
                    <p class="text-sm opacity-90"><i class="fa-solid fa-credit-card mr-2"></i>Thanh toán vé xem phim</p>
                    <div class="flex items-center space-x-2 text-xs pt-1">
                        <i class="fa-solid fa-clock mr-1"></i>
                        <span>{{ $booking->showtime->start_time->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Booking Summary Card -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6 border border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fa-solid fa-ticket text-[#D82D8B] mr-2"></i>Thông tin đặt vé
                </h2>

                <div class="grid md:grid-cols-2 gap-6 mb-4">
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Phim</p>
                            <p class="font-semibold text-gray-900">{{ $booking->showtime->movie->title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Rạp chiếu</p>
                            <p class="font-semibold text-gray-900">{{ $booking->showtime->branch->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Thời gian chiếu</p>
                            <p class="font-semibold text-gray-900">{{ $booking->showtime->start_time->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Ghế đã chọn</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($booking->seats as $seat)
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-bold">
                                    {{ $seat->row }}{{ $seat->seat_number }}
                                    @if($seat->type === 'vip')
                                        <span class="text-yellow-600">(VIP)</span>
                                    @endif
                                </span>
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Số lượng ghế</p>
                            <p class="font-semibold text-gray-900">{{ $booking->seats->count() }} ghế</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Tổng tiền</p>
                            <p class="text-2xl font-bold text-[#D82D8B]">{{ number_format($booking->total_price) }} VND</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="bg-white rounded-lg shadow-lg p-8 border border-gray-100">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">
                    <i class="fa-solid fa-credit-card text-[#D82D8B] mr-2"></i>Chọn phương thức thanh toán
                </h2>

                <div class="grid md:grid-cols-2 gap-8">
                    <!-- VNPay Payment -->
                    <div class="border-2 border-blue-200 rounded-lg p-6 hover:border-blue-400 transition-colors cursor-pointer bg-gradient-to-br from-blue-50 to-indigo-50">
                        <div class="text-center">
                            <div class="bg-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-credit-card text-2xl"></i>
                            </div>
                            <h3 class="font-bold text-lg mb-2 text-gray-900">Thanh toán VNPay</h3>
                            <p class="text-sm text-gray-600 mb-4">Thanh toán an toàn qua VNPay với nhiều phương thức</p>
                            <ul class="text-xs text-gray-500 space-y-1 mb-6">
                                <li>• Thẻ tín dụng/ghi nợ</li>
                                <li>• Ví điện tử</li>
                                <li>• Internet Banking</li>
                                <li>• Thanh toán qua QR Code</li>
                            </ul>

                            <form action="{{ route('payment.create') }}" method="POST" class="inline-block">
                                @csrf
                                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <i class="fas fa-credit-card mr-2"></i>Thanh toán VNPay
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Cash Payment -->
                    <div class="border-2 border-green-200 rounded-lg p-6 hover:border-green-400 transition-colors cursor-pointer bg-gradient-to-br from-green-50 to-emerald-50">
                        <div class="text-center">
                            <div class="bg-green-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-money-bill-wave text-2xl"></i>
                            </div>
                            <h3 class="font-bold text-lg mb-2 text-gray-900">Thanh toán tại quầy</h3>
                            <p class="text-sm text-gray-600 mb-4">Thanh toán bằng tiền mặt tại quầy vé AEON</p>
                            <ul class="text-xs text-gray-500 space-y-1 mb-6">
                                <li>• Thanh toán trực tiếp</li>
                                <li>• Nhận vé ngay lập tức</li>
                                <li>• Hỗ trợ đổi/trả vé</li>
                                <li>• Không cần đăng ký tài khoản</li>
                            </ul>

                            <button onclick="alert('Vui lòng đến quầy vé AEON để thanh toán và nhận vé. Mã đặt vé của bạn: BK{{ $booking->id }}')" class="bg-green-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-green-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-cash-register mr-2"></i>Thanh toán tại quầy
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment Notes -->
                <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-yellow-600 mt-1 mr-3"></i>
                        <div>
                            <h4 class="font-semibold text-yellow-800 mb-2">Lưu ý quan trọng:</h4>
                            <ul class="text-sm text-yellow-700 space-y-1">
                                <li>• Vé sẽ được giữ trong 10 phút sau khi đặt</li>
                                <li>• Vui lòng hoàn tất thanh toán trong thời gian quy định</li>
                                <li>• Sau khi thanh toán thành công, vé sẽ được gửi qua email</li>
                                <li>• Mã đặt vé: <strong>BK{{ $booking->id }}</strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <div class="text-center mt-8">
                    <a href="{{ route('booking.form', $booking->showtime_id) }}" class="inline-flex items-center text-gray-600 hover:text-[#D82D8B] transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Quay lại chọn ghế
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="bg-[#D82D8B] text-white p-2 rounded font-bold">LOTTE</div>
                        <span class="text-xs font-semibold leading-tight">CINEMA<br>VIETNAM</span>
                    </div>
                    <p class="text-sm text-gray-400">Hệ thống rạp chiếu phim AEON - Trải nghiệm điện ảnh đỉnh cao</p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Liên kết</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">Lịch chiếu</a></li>
                        <li><a href="#" class="hover:text-white">Khuyến mãi</a></li>
                        <li><a href="#" class="hover:text-white">Tuyển dụng</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Hỗ trợ</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
                        <li><a href="#" class="hover:text-white">Liên hệ</a></li>
                        <li><a href="#" class="hover:text-white">Điều khoản sử dụng</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Theo dõi chúng tôi</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; 2024 AEON Cinema. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>
</body>
</html>