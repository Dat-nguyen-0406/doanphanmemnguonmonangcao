<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn ghế - {{ $showtime->movie->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        .aeon-gradient {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('{{ $showtime->branch->image_url ?? "https://images.unsplash.com/photo-1519608487953-e999c86e7455?auto=format&fit=crop&w=1350&q=80" }}');
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
            <span>{{ $showtime->branch->city->name }}</span>
            <span>/</span>
            <span class="text-gray-800 font-bold uppercase">{{ $showtime->branch->name }}</span>
            <span>/</span>
            <span class="text-gray-800 font-bold">{{ $showtime->movie->title }}</span>
            <span>/</span>
            <span class="text-gray-800 font-bold">Chọn ghế</span>
        </div>
    </div>

    <div class="aeon-gradient h-[200px] relative text-white flex items-end pb-6">
        <div class="max-w-7xl mx-auto px-4 w-full">
            <div class="flex items-center space-x-4">
                   @if($showtime->movie->poster)
                        <div class="bg-white p-1 rounded shadow-lg w-16 h-24 overflow-hidden">
                            @php
                                // Định nghĩa biến $movie từ $showtime
                                $movie = $showtime->movie; 
                                
                                $posterUrl = \Illuminate\Support\Str::startsWith($movie->poster, ['http://', 'https://']) 
                                    ? $movie->poster 
                                    : asset('storage/' . $movie->poster);
                            @endphp
                            
                            <img src="{{ $posterUrl }}"
                                alt="{{ $movie->title }}"
                                class="w-full h-full object-cover transition-transform duration-500"
                                onerror="this.src='https://placehold.co/400x600?text={{ urlencode($movie->title) }}'" />
                        </div>
                    @else
                        <div class="bg-white p-3 rounded shadow-lg w-16 h-16 flex items-center justify-center">
                            <i class="fa-solid fa-film text-[#D82D8B] text-2xl"></i>
                        </div>
                    @endif

                <div class="space-y-1">
                    <h1 class="text-xl font-bold">{{ $showtime->movie->title }}</h1>
                    <p class="text-sm opacity-90"><i class="fa-solid fa-chair mr-2"></i>Chọn ghế ngồi</p>
                    <div class="flex items-center space-x-2 text-xs pt-1">
                        <i class="fa-solid fa-clock mr-1"></i>
                        <span>{{ $showtime->start_time->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="max-w-4xl mx-auto">
            <!-- Movie Info Card -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6 border border-gray-100">
                <div class="grid md:grid-cols-2 gap-6 text-center">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="fa-solid fa-building mr-1 text-[#D82D8B]"></i>Rạp chiếu
                        </p>
                        <p class="font-bold text-gray-900 text-lg">{{ $showtime->branch->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">
                            <i class="fa-solid fa-clock mr-1 text-[#D82D8B]"></i>Thời gian chiếu
                        </p>
                        <p class="font-bold text-gray-900 text-lg">{{ $showtime->start_time->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('booking.store') }}" method="POST" id="bookingForm">
                @csrf
                <input type="hidden" name="showtime_id" value="{{ $showtime->id }}">

                <div class="bg-white rounded-lg shadow-lg p-8 border border-gray-100">
                    <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">
                        <i class="fa-solid fa-chair text-[#D82D8B] mr-2"></i>Chọn ghế ngồi
                    </h2>

                    <!-- Screen -->
                    <div class="text-center mb-10">
                        <div class="bg-gray-800 text-white py-3 px-12 rounded-t-lg mx-auto max-w-sm shadow-lg">
                            <i class="fas fa-film mr-2"></i>MÀN HÌNH CHIẾU
                        </div>
                        <div class="h-3 bg-gradient-to-r from-gray-300 via-gray-400 to-gray-300 rounded-b-lg mx-auto max-w-sm shadow-sm"></div>
                    </div>

                    <!-- Seats -->
                    <div class="seat-selection mb-8">
                        @foreach($seats as $row => $rowSeats)
                        <div class="flex justify-center items-center mb-6">
                            <span class="text-sm font-bold text-gray-700 w-10 text-center bg-gray-100 py-1 px-2 rounded">{{ $row }}</span>
                            <div class="flex flex-wrap justify-center gap-3 ml-6">
                                @foreach($rowSeats as $seat)
                                <label class="seat-label {{ in_array($seat->id, $bookedSeats) ? 'booked' : 'available' }} {{ $seat->type === 'vip' ? 'vip' : 'normal' }}">
                                    <input type="checkbox" name="seats[]" value="{{ $seat->id }}"
                                           @if(in_array($seat->id, $bookedSeats)) disabled @endif
                                           class="seat-checkbox">
                                    <span class="seat-number">{{ $seat->seat_number }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Legend -->
                    <div class="flex justify-center space-x-8 mb-8 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-green-500 rounded mr-3 shadow-sm"></div>
                            <span class="text-sm font-medium text-gray-700">Ghế trống</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-red-500 rounded mr-3 shadow-sm"></div>
                            <span class="text-sm font-medium text-gray-700">Đã đặt</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-yellow-500 rounded mr-3 shadow-sm"></div>
                            <span class="text-sm font-medium text-gray-700">Ghế VIP</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-5 h-5 bg-blue-500 rounded mr-3 shadow-sm"></div>
                            <span class="text-sm font-medium text-gray-700">Đã chọn</span>
                        </div>
                    </div>

                    <!-- Selected seats and total -->
                    <div id="bookingSummary" class="hidden bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg mb-8 border border-blue-200">
                        <h3 class="font-bold text-lg mb-4 text-gray-900 flex items-center">
                            <i class="fa-solid fa-ticket text-blue-600 mr-2"></i>Thông tin đặt vé
                        </h3>
                        <div id="selectedSeats" class="mb-4"></div>
                        <div class="flex justify-between items-center pt-4 border-t border-blue-200">
                            <span class="font-semibold text-gray-700">Tổng tiền:</span>
                            <span id="totalPrice" class="text-2xl font-bold text-[#D82D8B]"></span>
                        </div>
                    </div>

                    <div class="text-center">
                        <button type="submit" id="submitBtn" class="bg-[#D82D8B] text-white px-10 py-4 rounded-lg font-bold text-lg hover:bg-pink-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-shopping-cart mr-2"></i>ĐẶT VÉ NGAY
                        </button>
                        <p class="text-sm text-gray-500 mt-3">Ghế sẽ được giữ trong 10 phút sau khi đặt</p>
                    </div>
                </div>
            </form>
        </div>
    </div>

<style>
.seat-selection {
    max-width: 900px;
    margin: 0 auto;
}

.seat-label {
    position: relative;
    display: inline-block;
    width: 45px;
    height: 45px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
    line-height: 45px;
    font-size: 12px;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.seat-label.available {
    background-color: #10b981;
    color: white;
    border: 2px solid #059669;
}

.seat-label.available:hover {
    background-color: #059669;
    transform: scale(1.1);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.seat-label.booked {
    background-color: #ef4444;
    color: white;
    cursor: not-allowed;
    border: 2px solid #dc2626;
    opacity: 0.7;
}

.seat-label.vip {
    background-color: #eab308;
    color: black;
    border: 2px solid #ca8a04;
}

.seat-label.vip.available:hover {
    background-color: #ca8a04;
}

.seat-label.selected {
    background-color: #3b82f6 !important;
    color: white;
    border: 2px solid #2563eb;
    animation: pulse 0.5s;
}

.seat-checkbox {
    display: none;
}

.seat-number {
    pointer-events: none;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.seat-checkbox');
    const submitBtn = document.getElementById('submitBtn');
    const bookingSummary = document.getElementById('bookingSummary');
    const selectedSeatsDiv = document.getElementById('selectedSeats');
    const totalPriceSpan = document.getElementById('totalPrice');

    const normalPrice = {{ $showtime->price }};
    const vipPrice = {{ $showtime->price * 1.5 }};

    function updateSummary() {
        const selectedSeats = document.querySelectorAll('.seat-checkbox:checked');
        const selectedLabels = document.querySelectorAll('.seat-label.selected');

        if (selectedSeats.length > 0) {
            bookingSummary.classList.remove('hidden');
            submitBtn.disabled = false;

            let summary = '<div class="grid grid-cols-2 gap-4 mb-3"><div><p class="font-medium text-gray-700 mb-2"><strong>Ghế đã chọn:</strong></p><div class="flex flex-wrap gap-2">';
            let total = 0;

            selectedLabels.forEach(label => {
                const seatId = label.querySelector('.seat-checkbox').value;
                const seatNumber = label.querySelector('.seat-number').textContent;
                const row = label.closest('.flex').querySelector('span').textContent;
                const isVip = label.classList.contains('vip');

                summary += `<span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-bold">${row}${seatNumber}</span>`;
                total += isVip ? vipPrice : normalPrice;
            });

            summary += '</div></div><div><p class="font-medium text-gray-700 mb-2"><strong>Chi tiết giá:</strong></p>';
            const normalCount = Array.from(selectedLabels).filter(l => !l.classList.contains('vip')).length;
            const vipCount = Array.from(selectedLabels).filter(l => l.classList.contains('vip')).length;

            if (normalCount > 0) {
                summary += `<p class="text-sm text-gray-600">${normalCount} ghế thường: ${normalCount * normalPrice} VND</p>`;
            }
            if (vipCount > 0) {
                summary += `<p class="text-sm text-gray-600">${vipCount} ghế VIP: ${vipCount * vipPrice} VND</p>`;
            }
            summary += '</div></div>';

            selectedSeatsDiv.innerHTML = summary;
            totalPriceSpan.textContent = total.toLocaleString() + ' VND';
        } else {
            bookingSummary.classList.add('hidden');
            submitBtn.disabled = true;
        }
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const label = this.closest('.seat-label');
            if (this.checked) {
                label.classList.add('selected');
            } else {
                label.classList.remove('selected');
            }
            updateSummary();
        });
    });

    // Initial state
    updateSummary();
});
</script>
</body>
</html>