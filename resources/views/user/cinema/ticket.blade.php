<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé điện tử - AEON Cinema</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .ticket-gradient { background: linear-gradient(135deg, #D82D8B 0%, #A50064 100%); }
        .ticket-border { border: 2px dashed rgba(255,255,255,0.4); }
    </style>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Vé điện tử</h1>
                <p class="text-sm text-gray-500">Mã vé: <span class="font-semibold text-[#D82D8B]">BK{{ $booking->id }}</span></p>
            </div>
            <a href="{{ route('my.bookings') }}" class="text-sm text-[#D82D8B] hover:underline">Quay lại</a>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-10">
        <div class="bg-white rounded-3xl overflow-hidden shadow-xl">
            <div class="ticket-gradient p-8 text-white">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                    <div>
                        <p class="text-sm uppercase tracking-[0.2em] opacity-80">Lotte Cinema / VNPay</p>
                        <h2 class="text-4xl font-black mt-4">{{ $booking->showtime->movie->title }}</h2>
                        <p class="mt-3 text-sm opacity-90">{{ $booking->showtime->branch->name }} • {{ $booking->showtime->branch->address ?? 'Địa chỉ rạp' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="bg-white/10 rounded-3xl px-5 py-4 inline-block">
                            <p class="text-xs uppercase opacity-80">Tổng thanh toán</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($booking->total_price) }} VND</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-8 bg-gray-50">
                <div class="space-y-5">
                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-[0.18em] mb-3">Thông tin suất chiếu</p>
                        <div class="space-y-3 text-gray-700">
                            <div class="flex justify-between">
                                <span class="font-semibold">Ngày/giờ</span>
                                <span>{{ $booking->showtime->start_time->format('l, d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-semibold">Phòng chiếu</span>
                                <span>{{ $booking->showtime->screen ?? 'Phòng A' }}</span>
                            </div>
                            @php
                            $ticketTypes = $booking->seats->pluck('type')->unique()->map(function($type) {
                                return strtoupper($type);
                            })->implode(', ');
                        @endphp
                        <div class="flex justify-between">
                                <span class="font-semibold">Loại vé</span>
                                <span>{{ $ticketTypes }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-200">
                        <p class="text-xs text-gray-500 uppercase tracking-[0.18em] mb-3">Ghế đã đặt</p>
                        <div class="flex flex-wrap gap-3">
                            @foreach($booking->seats as $seat)
                                <span class="px-4 py-3 bg-[#FCE8F7] text-[#A50064] rounded-2xl font-semibold shadow-sm">
                                    {{ $seat->row }}{{ $seat->seat_number }}
                                    @if($seat->type === 'vip') <span class="text-xs text-yellow-700">VIP</span> @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-200 space-y-6">
                    <div class="text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-[0.18em] mb-4">Mã vé điện tử</p>
                        <div class="inline-flex items-center justify-center w-48 h-48 rounded-3xl bg-gray-900 text-white shadow-lg">
                            <div>
                                <i class="fa-solid fa-qrcode text-5xl mb-3"></i>
                                <p class="text-sm">BK{{ $booking->id }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-[#FEF0F6] text-[#A50064] rounded-3xl p-4">
                            <p class="text-sm font-semibold">Trạng thái</p>
                            <p class="text-lg font-bold">{{ ucfirst($booking->status) }}</p>
                        </div>
                        <div class="bg-[#fff3cd] text-[#856404] rounded-3xl p-4">
                            <p class="text-sm font-semibold">Lưu ý</p>
                            <ul class="text-sm list-disc list-inside">
                                <li>Đến rạp trước 20 phút.</li>
                                <li>Xuất vé từ mã số hoặc trình vé điện tử tại quầy.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 border-t border-gray-200 bg-white">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Vui lòng sử dụng vé để check-in tại quầy hoặc quét QR code.</p>
                        <p class="text-sm text-gray-500">Mã vé được gửi về email nếu bạn đã đăng ký.</p>
                    </div>
                    <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 bg-[#D82D8B] text-white px-6 py-3 rounded-full font-semibold hover:bg-[#A50064] transition-colors">
                        <i class="fa-solid fa-print"></i> In vé
                    </button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>