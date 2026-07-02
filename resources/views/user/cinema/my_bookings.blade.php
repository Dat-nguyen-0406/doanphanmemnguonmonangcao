<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đặt vé - AEON Cinema</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        .aeon-gradient {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('https://images.unsplash.com/photo-1519608487953-e999c86e7455?auto=format&fit=crop&w=1350&q=80');
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
                    <div class="bg-[#a61d6d] text-white p-2 rounded font-bold">AEON</div>
                    <span class="text-xs text-[#a61d6d] font-semibold leading-tight">MALL<br>UTILITY</span>
                </a>

                <div class="hidden md:flex space-x-6 text-sm font-medium text-gray-700">
                    <a href="#" class="hover:text-[#a61d6d]">Lịch chiếu</a>
                    <a href="#" class="hover:text-[#a61d6d]">Ẩm thực</a>
                    <a href="#" class="hover:text-[#a61d6d]">Vị trí cửa hàng</a>
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
                    <a href="{{ route('login') }}" class="text-sm font-medium text-[#a61d6d] hover:underline">Đăng nhập</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="bg-white py-2 border-b">
        <div class="max-w-7xl mx-auto px-4 text-xs text-gray-500 flex items-center space-x-2">
            <a href="{{ route('home') }}" class="hover:text-[#a61d6d]"><i class="fa-solid fa-house"></i></a>
            <span>/</span>
            <span class="text-gray-800 font-bold">Lịch sử đặt vé</span>
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
                    <h1 class="text-xl font-bold">Lịch sử đặt vé</h1>
                    <p class="text-sm opacity-90"><i class="fa-solid fa-ticket mr-2"></i>Quản lý vé đã đặt của bạn</p>
                    <div class="flex items-center space-x-2 text-xs pt-1">
                        <i class="fa-solid fa-user mr-1"></i>
                        <span>{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="max-w-6xl mx-auto">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-3"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if($bookings->isEmpty())
                <div class="bg-white rounded-lg shadow-lg p-12 text-center border border-gray-100">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-ticket-alt text-4xl text-gray-400"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Chưa có vé nào được đặt</h2>
                    <p class="text-gray-600 mb-8">Bạn chưa đặt vé xem phim nào. Hãy khám phá các bộ phim đang chiếu và đặt vé ngay!</p>
                    <a href="{{ route('home') }}" class="inline-flex items-center bg-[#a61d6d] text-white px-8 py-4 rounded-lg font-bold hover:bg-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-film mr-2"></i>Đặt vé ngay
                    </a>
                </div>
            @else
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Vé đã đặt của bạn</h2>
                    <p class="text-gray-600">Tổng cộng {{ $bookings->count() }} vé đã đặt</p>
                </div>

                <div class="grid gap-6">
                    @foreach($bookings as $booking)
                    <div class="bg-white rounded-lg shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-[#a61d6d] to-pink-600 p-6 text-white">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div>
                                    <h3 class="text-xl font-bold mb-1">{{ $booking->showtime->movie->title }}</h3>
                                    <p class="text-sm opacity-90">{{ $booking->showtime->movie->genre ?? 'Phim' }}</p>
                                </div>
                                <div class="mt-4 md:mt-0 text-right">
                                    <div class="text-3xl font-bold mb-1">{{ number_format($booking->total_price) }} VND</div>
                                    <div class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                        @if($booking->status === 'confirmed') bg-green-500 text-white
                                        @elseif($booking->status === 'pending') bg-yellow-500 text-white
                                        @else bg-red-500 text-white @endif">
                                        @if($booking->status === 'confirmed')
                                            <i class="fas fa-check-circle mr-1"></i>Đã xác nhận
                                        @elseif($booking->status === 'pending')
                                            <i class="fas fa-clock mr-1"></i>Đang xử lý
                                        @else
                                            <i class="fas fa-times-circle mr-1"></i>Đã hủy
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="p-6">
                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt text-[#a61d6d] w-5 mr-3"></i>
                                        <div>
                                            <p class="text-sm text-gray-600">Rạp chiếu</p>
                                            <p class="font-semibold text-gray-900">{{ $booking->showtime->branch->name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-clock text-[#a61d6d] w-5 mr-3"></i>
                                        <div>
                                            <p class="text-sm text-gray-600">Thời gian chiếu</p>
                                            <p class="font-semibold text-gray-900">{{ $booking->showtime->start_time->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-alt text-[#a61d6d] w-5 mr-3"></i>
                                        <div>
                                            <p class="text-sm text-gray-600">Ngày đặt</p>
                                            <p class="font-semibold text-gray-900">{{ $booking->booking_date->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm text-gray-600 mb-2 flex items-center">
                                            <i class="fas fa-chair text-[#a61d6d] mr-2"></i>Ghế ngồi
                                        </p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($booking->seats as $seat)
                                            <span class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-2 rounded-lg text-sm font-bold">
                                                {{ $seat->row }}{{ $seat->seat_number }}
                                                @if($seat->type === 'vip')
                                                    <span class="ml-1 text-yellow-600">(VIP)</span>
                                                @endif
                                            </span>
                                            @endforeach
                                        </div>
                                    </div>

                                    @if($booking->payment)
                                    <div>
                                        <p class="text-sm text-gray-600 mb-2 flex items-center">
                                            <i class="fas fa-credit-card text-[#a61d6d] mr-2"></i>Thanh toán
                                        </p>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                            @if($booking->payment->status === 'success') bg-green-100 text-green-800
                                            @elseif($booking->payment->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            @if($booking->payment->status === 'success')
                                                <i class="fas fa-check-circle mr-1"></i>Đã thanh toán
                                            @elseif($booking->payment->status === 'pending')
                                                <i class="fas fa-clock mr-1"></i>Chờ thanh toán
                                            @else
                                                <i class="fas fa-times-circle mr-1"></i>Thanh toán thất bại
                                            @endif
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="border-t pt-6">
                                <div class="flex flex-col sm:flex-row gap-3 justify-end">
                                    @php $ticketCode = 'BK' . $booking->id; @endphp
                            @if($booking->status === 'confirmed' && $booking->showtime->start_time > now())
                                        <a href="{{ route('booking.ticket', $booking->id) }}" class="inline-flex items-center px-4 py-2 bg-[#D82D8B] text-white rounded-lg hover:bg-[#A50064] transition-colors">
                                            <i class="fas fa-ticket-alt mr-2"></i>Xem vé điện tử
                                        </a>
                                        <button onclick="printTicket({{ $booking->id }})" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                            <i class="fas fa-print mr-2"></i>In vé
                                        </button>
                                        <button onclick="cancelBooking({{ $booking->id }})" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                            <i class="fas fa-times mr-2"></i>Hủy vé
                                        </button>
                                    @elseif($booking->status === 'pending')
                                        <a href="{{ route('payment.page', $booking->id) }}" class="inline-flex items-center px-4 py-2 bg-[#D82D8B] text-white rounded-lg hover:bg-[#A50064] transition-colors">
                                            <i class="fas fa-credit-card mr-2"></i>Thanh toán ngay
                                        </a>
                                    @endif
                                    <a href="mailto:support@aeoncinema.vn?subject=Hỗ trợ đặt vé {{ $ticketCode }}" class="inline-flex items-center px-4 py-2 bg-[#D82D8B] text-white rounded-lg hover:bg-[#A50064] transition-colors">
                                        <i class="fas fa-envelope mr-2"></i>Liên hệ hỗ trợ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if(method_exists($bookings, 'hasPages') && $bookings->hasPages())
                <div class="mt-8">
                    {{ $bookings->links() }}
                </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="bg-[#a61d6d] text-white p-2 rounded font-bold">AEON</div>
                        <span class="text-xs font-semibold leading-tight">MALL<br>UTILITY</span>
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

    <script>
    function printTicket(bookingId) {
        // Open print dialog or redirect to print page
        window.open(`/booking/${bookingId}/print`, '_blank');
    }

    function cancelBooking(bookingId) {
        if (confirm('Bạn có chắc chắn muốn hủy vé này?')) {
            // Submit cancellation form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/booking/${bookingId}/cancel`;
            form.innerHTML = '@csrf';
            document.body.appendChild(form);
            form.submit();
        }
    }
    </script>
</body>
</html>