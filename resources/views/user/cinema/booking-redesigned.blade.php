<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $showtime->movie->title }} - Chọn ghế</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        .cinema-red { color: #e60012; }
        .bg-cinema-red { background-color: #e60012; }
        .seat { 
            width: 32px; 
            height: 32px; 
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
        }
        .seat.available { 
            background-color: #f3f4f6; 
            border-color: #d1d5db;
        }
        .seat.available:hover { 
            background-color: #e5e7eb;
            border-color: #9ca3af;
            transform: scale(1.1);
        }
        .seat.selected { 
            background-color: #e60012; 
            color: white;
            border-color: #e60012;
        }
        .seat.booked { 
            background-color: #ef4444; 
            color: white;
            border-color: #ef4444;
            cursor: not-allowed;
            opacity: 0.6;
        }
        .seat.vip { 
            background-color: #fbbf24; 
            border-color: #f59e0b;
        }
        .seat.vip.selected { 
            background-color: #d97706;
            border-color: #d97706;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <h1 class="text-2xl font-black text-gray-900">
                <i class="fa-solid fa-film cinema-red mr-2"></i> Chọn ghế
            </h1>
            <div class="text-right">
                <p class="text-sm text-gray-600">{{ $showtime->movie->title }}</p>
                <p class="text-xs text-gray-500">{{ $showtime->branch->name }} • {{ $showtime->start_time->format('H:i') }}</p>
            </div>
        </div>
    </header>

    <div class="max-w-5xl mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Seat Selection (Main) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <!-- Instructions -->
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">🎬 Màn hình chiếu</h3>
                        <div class="relative h-2 bg-gradient-to-r from-red-300 via-red-500 to-red-300 rounded-full mb-6 opacity-70"></div>
                        
                        <!-- Seat Legend -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <div class="seat available"></div>
                                <span class="text-xs font-medium text-gray-700">Ghế trống</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="seat selected"></div>
                                <span class="text-xs font-medium text-gray-700">Ghế chọn</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="seat booked"></div>
                                <span class="text-xs font-medium text-gray-700">Ghế đã bán</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="seat vip"></div>
                                <span class="text-xs font-medium text-gray-700">Ghế VIP</span>
                            </div>
                        </div>
                    </div>

                    <!-- Seat Grid -->
                    <div class="space-y-3 mb-8">
                        @foreach($seats as $row => $rowSeats)
                            <div class="flex items-center space-x-2 justify-center">
                                <span class="w-8 text-center text-sm font-bold text-gray-600">{{ $row }}</span>
                                <div class="flex gap-1">
                                    @foreach($rowSeats as $seat)
                                        <button type="button"
                                                class="seat @if($seat->type === 'vip') vip @endif @if(in_array($seat->id, $bookedSeats)) booked @endif available"
                                                data-seat-id="{{ $seat->id }}"
                                                data-seat-number="{{ $seat->seat_number }}"
                                                data-seat-type="{{ $seat->type }}"
                                                @if(in_array($seat->id, $bookedSeats)) disabled @endif
                                                onclick="toggleSeat(this)">
                                            {{ $seat->seat_number }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Info -->
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                        <i class="fa-solid fa-info-circle mr-2"></i> Chọn vị trí ghế yêu thích của bạn. Nhấn vào ghế để chọn/bỏ chọn.
                    </div>
                </div>
            </div>

            <!-- Order Summary (Sidebar) -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 sticky top-24">
                    <!-- Movie Info -->
                    <div class="mb-6 pb-6 border-b border-gray-100">
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-2">Thông tin suất chiếu</p>
                        <p class="font-bold text-gray-900">{{ $showtime->movie->title }}</p>
                        <p class="text-sm text-gray-600 mt-2">
                            <i class="fa-solid fa-map-pin mr-1"></i> {{ $showtime->branch->name }}
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fa-solid fa-clock mr-1"></i> {{ $showtime->start_time->format('H:i · d/m/Y') }}
                        </p>
                    </div>

                    <!-- Selected Seats -->
                    <div class="mb-6 pb-6 border-b border-gray-100">
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-3">Ghế đã chọn</p>
                        <div id="selectedSeatsList" class="space-y-2">
                            <p class="text-sm text-gray-500 italic">Chưa chọn ghế</p>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="space-y-3 mb-6 pb-6 border-b border-gray-100">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Giá vé thường:</span>
                            <span id="regularPrice" class="font-bold text-gray-900">0 đ</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Giá ghế VIP:</span>
                            <span id="vipPrice" class="font-bold text-gray-900">0 đ</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold pt-3 border-t border-gray-100">
                            <span>Tổng cộng:</span>
                            <span id="totalPrice" class="cinema-red">0 đ</span>
                        </div>
                    </div>

                    <!-- Quantity -->
                    <div class="mb-6 pb-6 border-b border-gray-100">
                        <p class="text-xs text-gray-500 uppercase tracking-widest font-bold mb-2">
                            Số ghế: <span id="seatCount" class="cinema-red font-black text-base">0</span>
                        </p>
                    </div>

                    <!-- Proceed Button -->
                    <form id="bookingForm" action="{{ route('booking.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="showtime_id" value="{{ $showtime->id }}">
                        <input type="hidden" id="seatsInput" name="seats" value="">

                        <button type="submit" 
                                id="proceedBtn" 
                                disabled
                                class="w-full bg-gray-300 text-gray-600 py-3 rounded-xl font-bold transition-all cursor-not-allowed">
                            <i class="fa-solid fa-lock mr-2"></i> Chọn ghế trước
                        </button>
                    </form>

                    <button type="button" onclick="history.back()" class="w-full mt-3 border-2 border-gray-300 text-gray-700 hover:border-gray-400 py-2 rounded-lg font-bold transition-colors">
                        ← Quay lại
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const basePrice = {{ $showtime->price }};
        const selectedSeats = new Map();

        function toggleSeat(element) {
            if (element.classList.contains('booked')) return;

            const seatId = element.dataset.seatId;
            const seatNumber = element.dataset.seatNumber;
            const seatType = element.dataset.seatType;

            if (element.classList.contains('selected')) {
                element.classList.remove('selected');
                selectedSeats.delete(seatId);
            } else {
                element.classList.add('selected');
                selectedSeats.set(seatId, {
                    number: seatNumber,
                    type: seatType,
                    price: seatType === 'vip' ? basePrice * 1.5 : basePrice
                });
            }

            updateSummary();
        }

        function updateSummary() {
            const seatIds = Array.from(selectedSeats.keys());
            const seatCount = selectedSeats.size;
            
            document.getElementById('seatsInput').value = JSON.stringify(seatIds);
            document.getElementById('seatCount').textContent = seatCount;

            // Calculate prices
            let regularTotal = 0;
            let vipTotal = 0;
            const seatsList = [];

            selectedSeats.forEach((seat, id) => {
                seatsList.push(`Ghế ${seat.number}`);
                if (seat.type === 'vip') {
                    vipTotal += seat.price;
                } else {
                    regularTotal += seat.price;
                }
            });

            const totalPrice = regularTotal + vipTotal;

            document.getElementById('regularPrice').textContent = regularTotal.toLocaleString('vi-VN') + ' đ';
            document.getElementById('vipPrice').textContent = vipTotal.toLocaleString('vi-VN') + ' đ';
            document.getElementById('totalPrice').textContent = totalPrice.toLocaleString('vi-VN') + ' đ';

            // Update seats list
            const listHTML = seatsList.length > 0 
                ? `<div class="flex flex-wrap gap-2">${seatsList.map(s => `<span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">${s}</span>`).join('')}</div>`
                : '<p class="text-sm text-gray-500 italic">Chưa chọn ghế</p>';
            
            document.getElementById('selectedSeatsList').innerHTML = listHTML;

            // Enable/disable button
            const btn = document.getElementById('proceedBtn');
            if (seatCount > 0) {
                btn.disabled = false;
                btn.classList.remove('bg-gray-300', 'text-gray-600', 'cursor-not-allowed');
                btn.classList.add('bg-cinema-red', 'text-white', 'hover:bg-red-700');
                btn.innerHTML = '<i class="fa-solid fa-credit-card mr-2"></i> Tiến hành thanh toán';
            } else {
                btn.disabled = true;
                btn.classList.add('bg-gray-300', 'text-gray-600', 'cursor-not-allowed');
                btn.classList.remove('bg-cinema-red', 'text-white', 'hover:bg-red-700');
                btn.innerHTML = '<i class="fa-solid fa-lock mr-2"></i> Chọn ghế trước';
            }
        }

        // Initialize
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            if (selectedSeats.size === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một ghế');
            }
        });
    </script>
</body>
</html>
