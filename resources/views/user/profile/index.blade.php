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
                <div class="bg-white p-6 rounded-lg shadow-sm">
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
    {{-- Vòng lặp chính để hiển thị từng hàng đơn hàng --}}
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
                                        {{-- Đổi $order->details thành $order->orderDetails ở dòng dưới --}}
                                            @if($order->orderDetails && $order->orderDetails->count() > 0)
                                                @foreach($order->orderDetails as $detail)
                                                    {{ $detail->product->name ?? 'Sản phẩm' }}{{ !$loop->last ? ',' : '' }}
                                                @endforeach
                                            @else
                                                <span class="text-gray-400 italic text-xs">Không có chi tiết</span>
                                            @endif
                                        </td>
                                        
                                        <td class="px-4 py-4 text-right font-bold">
                                            {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                        </td>
                                        
                                        <td class="px-4 py-4 text-center flex items-center justify-center space-x-2">
                                            <span class="px-2 py-1 rounded-full text-[10px] font-bold {{ $order->status == 'paid' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600' }}">
                                                {{ $order->status == 'paid' ? 'ĐÃ THANH TOÁN' : 'CHỜ XỬ LÝ' }}
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
</script>
@endsection