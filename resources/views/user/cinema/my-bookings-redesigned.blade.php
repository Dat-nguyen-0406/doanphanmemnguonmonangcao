<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé của tôi - AEON Cinema</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .cinema-red { color: #e60012; }
        .bg-cinema-red { background-color: #e60012; }
        .ticket-cutout {
            position: relative;
            background-image: radial-gradient(circle at 0 50%, transparent 10px, white 11px),
                              radial-gradient(circle at 100% 50%, transparent 10px, white 11px);
        }
    </style>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <h1 class="text-2xl font-black text-gray-900 italic">
                <i class="fa-solid fa-ticket cinema-red mr-2"></i>VÉ CỦA TÔI
            </h1>
            <a href="{{ route('home') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm font-bold text-gray-700 transition-all">
                <i class="fa-solid fa-house mr-2"></i>Trang chủ
            </a>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 font-medium rounded-r-lg shadow-sm">
                <i class="fa-solid fa-circle-check mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if($bookings->isEmpty())
            <div class="text-center py-20 bg-white rounded-3xl shadow-sm border border-gray-200">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-ticket-simple text-4xl text-gray-300"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Bạn chưa có vé nào</h2>
                <p class="text-gray-500 mb-8">Hãy chọn phim và trải nghiệm những suất chiếu tuyệt vời tại AEON Cinema.</p>
                <a href="{{ route('home') }}" class="inline-flex items-center px-8 py-3 bg-cinema-red hover:bg-red-700 text-white font-bold rounded-full transition-all shadow-lg shadow-red-200">
                    Đặt vé ngay
                </a>
            </div>
        @else
            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                @foreach($bookings as $booking)
                    <div class="group bg-white rounded-3xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col border border-gray-100">
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ asset('storage/' . $booking->showtime->movie->poster) }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                 onerror="this.src='https://placehold.co/600x400?text=Movie+Poster'">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                            <div class="absolute bottom-4 left-4">
                                <span class="px-2 py-1 bg-yellow-400 text-black text-[10px] font-black rounded mb-2 inline-block italic">PREMIUM</span>
                                <h3 class="text-white font-black text-xl leading-tight uppercase italic">{{ $booking->showtime->movie->title }}</h3>
                            </div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col ticket-cutout">
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="space-y-1">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Ngày chiếu</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $booking->showtime->start_time->format('d/m/Y') }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Suất chiếu</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $booking->showtime->start_time->format('H:i') }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Rạp / Phòng</p>
                                    <p class="text-sm font-bold text-gray-900 line-clamp-1">{{ $booking->showtime->branch->name }} - {{ $booking->showtime->room->name }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Ghế đã chọn</p>
                                    <p class="text-sm font-bold cinema-red">
                                        @foreach($booking->seats as $seat)
                                            {{ $seat->seat_number }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                    </p>
                                </div>
                            </div>

                            <div class="mt-auto pt-6 border-t border-dashed border-gray-200 flex flex-col gap-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs text-gray-500 font-bold uppercase tracking-widest">Tổng tiền</span>
                                    <span class="text-lg font-black text-gray-900">{{ number_format($booking->total_price) }}đ</span>
                                </div>

                                <div class="space-y-2">
                                    @if($booking->status === 'confirmed')
                                        <div class="w-full bg-green-100 text-green-700 py-3 rounded-xl font-black text-xs text-center uppercase border border-green-200">
                                            <i class="fa-solid fa-circle-check mr-2"></i>Đã thanh toán thành công
                                        </div>
                                        <a href="{{ route('booking.ticket', $booking->id) }}" class="flex items-center justify-center w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-black text-xs uppercase transition-all shadow-lg shadow-blue-100">
                                            <i class="fa-solid fa-qrcode mr-2 text-sm"></i>Xem vé điện tử
                                        </a>
                                    @else
                                        <div class="w-full bg-orange-100 text-orange-700 py-3 rounded-xl font-black text-xs text-center uppercase border border-orange-200">
                                            <i class="fa-solid fa-clock mr-2"></i>Chờ thanh toán
                                        </div>
                                        <a href="{{ route('payment.page', $booking->id) }}" class="flex items-center justify-center w-full bg-cinema-red hover:bg-red-700 text-white py-3 rounded-xl font-black text-xs uppercase transition-all shadow-lg shadow-red-100">
                                            <i class="fa-solid fa-credit-card mr-2 text-sm"></i>Thanh toán ngay
                                        </a>
                                    @endif
                                    
                                    <button class="w-full border-2 border-gray-100 text-gray-400 hover:text-gray-600 hover:border-gray-200 py-3 rounded-xl font-black text-[10px] uppercase transition-all tracking-tighter">
                                        <i class="fa-solid fa-circle-info mr-1"></i>Chi tiết đặt vé
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</body>
</html>