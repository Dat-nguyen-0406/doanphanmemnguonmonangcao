@extends('layouts.shop')

@section('content')
<div class="bg-gray-50 min-h-screen py-10">
    <div class="max-w-6xl mx-auto px-4">
        <h1 class="text-2xl font-bold text-[#a61d6d] mb-8 uppercase italic">Hồ sơ thành viên AEON</h1>
     
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-[#a61d6d]">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex flex-col items-center mb-6">
                        <div class="w-24 h-24 rounded-full border-4 border-pink-100 overflow-hidden mb-4 shadow-inner relative group">
                                <img id="preview" 
                                 src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/default-avatar.png') }}" 
                                 alt="Avatar" 
                                 class="w-full h-full object-cover">
                            
                            <label class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                                <i class="fa-solid fa-camera text-white"></i>
                                <input type="file" name="avatar" class="hidden" onchange="previewImage(this)">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 italic">Bấm vào ảnh để thay đổi</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase">Họ và tên</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="w-full border-b py-2 focus:border-[#a61d6d] outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase">Địa chỉ Email</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="w-full border-b py-2 focus:border-[#a61d6d] outline-none text-sm bg-gray-50" readonly>
                            <p class="text-[10px] text-gray-400 italic">* Email dùng để đăng nhập nên không thể thay đổi</p>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase">Số điện thoại</label>
                            <input type="text" name="phone" value="{{ $user->phone }}" class="w-full border-b py-2 focus:border-[#a61d6d] outline-none text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase">Địa chỉ nhận hàng</label>
                            <textarea name="address" class="w-full border-b py-2 focus:border-[#a61d6d] outline-none text-sm" rows="2">{{ $user->address }}</textarea>
                        </div>
                        <button type="submit" class="w-full bg-[#a61d6d] text-white py-3 rounded font-bold text-sm hover:bg-pink-800 transition shadow-lg mt-4">
                            LƯU THAY ĐỔI
                        </button>
                    </div>
                </form>
            </div>

            <div class="md:col-span-2 space-y-6">
                <!-- TAB NAVIGATION -->
                <div class="bg-white rounded-lg shadow-sm border-b">
                    <div class="flex">
                        <button onclick="switchTab('orders')" id="tab-orders-btn" class="flex-1 px-6 py-4 text-center font-bold text-gray-700 border-b-2 border-[#a61d6d] tab-btn" style="border-color: #a61d6d;">
                            <i class="fa-solid fa-shopping-bag mr-2"></i> Lịch sử đi chợ
                        </button>
                        <button onclick="switchTab('bookings')" id="tab-bookings-btn" class="flex-1 px-6 py-4 text-center font-bold text-gray-400 border-b-2 border-gray-200 tab-btn">
                            <i class="fa-solid fa-utensils mr-2"></i> Lịch sử đặt bàn
                        </button>
                    </div>
                </div>

                <!-- TAB 1: ORDERS -->
                <div id="tab-orders" class="bg-white p-6 rounded-lg shadow-sm tab-content">
                    <h2 class="font-bold text-lg text-gray-800 mb-6 flex items-center">
                        <i class="fa-solid fa-clock-rotate-left mr-2 text-[#a61d6d]"></i> Lịch sử đi chợ AEON
                    </h2>

                    @if($orders->isEmpty())
                        <div class="text-center py-10 border-2 border-dashed border-gray-100 rounded-xl">
                            <p class="text-gray-400">Bạn chưa có đơn hàng nào.</p>
                            <a href="{{ route('home') }}" class="text-[#a61d6d] font-bold text-sm hover:underline mt-2 inline-block">ĐI CHỢ NGAY</a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-600 uppercase text-[10px]">
                                    <tr>
                                        <th class="px-4 py-3">Mã đơn</th>
                                        <th class="px-4 py-3">Ngày đặt</th>
                                        <th class="px-4 py-3">Sản phẩm</th>
                                        <th class="px-4 py-3 text-right">Tổng tiền</th>
                                        <th class="px-4 py-3 text-center">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($orders as $order)
                                    <tr class="hover:bg-pink-50 transition-colors cursor-pointer group" 
                                        onclick="window.location='{{ route('profile.orders.show', $order->id) }}'">
                                        
                                        <td class="px-4 py-4 font-bold text-[#a61d6d] group-hover:underline">
                                            #{{ $order->id }}
                                        </td>
                                        
                                        <td class="px-4 py-4 text-gray-500">
                                            {{ $order->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        
                                        <td class="px-4 py-4 max-w-[200px] truncate">
                                            @if($order->orderDetails && $order->orderDetails->count() > 0)
                                                @foreach($order->orderDetails as $detail)
                                                    {{ $detail->product->name ?? 'San pham' }}{{ !$loop->last ? ',' : '' }}
                                                @endforeach
                                            @else
                                                <span class="text-gray-400 italic text-xs">Khong co chi tiet</span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-4 py-4 text-right font-bold">
                                            {{ number_format($order->total_amount, 0, ',', '.') }}d
                                        </td>
                                        
                                        <td class="px-4 py-4 text-center flex items-center justify-center space-x-2">
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $order->status == 'paid' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                                                {{ $order->status == 'paid' ? 'DA THANH TOAN' : 'CHO XU LY' }}
                                            </span>
                                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-300 group-hover:text-[#a61d6d]"></i>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- TAB 2: RESTAURANT BOOKINGS -->
                <div id="tab-bookings" class="bg-white p-6 rounded-lg shadow-sm tab-content hidden">
                    <h2 class="font-bold text-lg text-gray-800 mb-6 flex items-center">
                        <i class="fa-solid fa-clock-rotate-left mr-2 text-[#a61d6d]"></i> Lịch sử đặt bàn AEON
                    </h2>

                    @if($restaurantBookings->isEmpty())
                        <div class="text-center py-10 border-2 border-dashed border-gray-100 rounded-xl">
                            <p class="text-gray-400">Bạn chưa có lịch đặt bàn nào.</p>
                            <a href="{{ route('restaurants.index') }}" class="text-[#a61d6d] font-bold text-sm hover:underline mt-2 inline-block">DAT BAN NGAY</a>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($restaurantBookings as $booking)
                            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow hover:border-[#a61d6d]">
                                <div class="grid md:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-bold">Nha hang</p>
                                        <p class="font-bold text-gray-800">{{ $booking->restaurant->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">Ban {{ $booking->table->table_number ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 uppercase font-bold">Thoi gian</p>
                                        <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($booking->booking_date . ' ' . $booking->booking_time)->format('d/m/Y H:i') }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->guests_count }} khach</p>
                                    </div>
                                    <div class="flex items-end justify-between md:justify-end">
                                        <div>
                                            <p class="text-xs text-gray-500 uppercase font-bold">Trang thai</p>
                                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold 
                                                {{ $booking->status == 'confirmed' ? 'bg-green-100 text-green-600' : ($booking->status == 'pending' ? 'bg-orange-100 text-orange-600' : 'bg-red-100 text-red-600') }}">
                                                {{ $booking->status == 'confirmed' ? 'DA XAC NHAN' : ($booking->status == 'pending' ? 'CHO XAC NHAN' : 'DA HUY') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Switch between tabs
    function switchTab(tabName) {
        // Hide all tabs
        const tabs = document.querySelectorAll('.tab-content');
        tabs.forEach(tab => tab.classList.add('hidden'));
        
        // Show selected tab
        document.getElementById('tab-' + tabName).classList.remove('hidden');
        
        // Update button styles
        const btns = document.querySelectorAll('.tab-btn');
        btns.forEach(btn => {
            btn.style.borderColor = '#e0e0e0';
            btn.style.color = '#999';
        });
        
        const activeBtn = document.getElementById('tab-' + tabName + '-btn');
        activeBtn.style.borderColor = '#a61d6d';
        activeBtn.style.color = '#333';
    }
</script>
@endsection