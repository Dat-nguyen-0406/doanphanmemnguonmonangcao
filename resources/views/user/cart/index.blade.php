    @extends('layouts.shop')

    @section('title', 'Giỏ hàng của bạn')

    @section('content')
    <main class="max-w-5xl mx-auto py-20 px-4">
        <div class="flex items-end justify-between mb-16">
            <div>
                <h2 class="text-5xl font-black text-gray-900 italic uppercase tracking-tighter leading-none">Giỏ hàng</h2>
                <div class="mb-8">
    {{-- Thông báo lỗi (Error) --}}
    @if(session('error') || $errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm animate-pulse">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-exclamation text-red-500 mr-3 text-lg"></i>
                <p class="text-red-700 text-xs font-black uppercase tracking-wider">
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        Có lỗi xảy ra, vui lòng kiểm tra lại!
                    @endif
                </p>
            </div>
            @if($errors->any())
                <ul class="mt-2 ml-8 list-disc list-inside text-[10px] text-red-600 font-bold uppercase">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    {{-- Thông báo thành công (Success) --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl shadow-sm">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-check text-green-500 mr-3 text-lg"></i>
                <p class="text-green-700 text-xs font-black uppercase tracking-wider">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    @endif
</div>
                <p class="text-gray-400 font-bold uppercase text-[10px] tracking-[0.3em] mt-3 ml-1">Kiểm tra lại các lựa chọn của bạn</p>
            </div>
            <div class="text-right">
                <span class="bg-white border border-gray-100 text-[#a61d6d] px-6 py-2 rounded-2xl text-xs font-black uppercase shadow-sm">
                    {{ is_array(session('cart')) ? count(session('cart')) : 0 }} mục sản phẩm
                </span>
            </div>
        </div>

        @if(session('cart') && count(session('cart')) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
                
                <div class="lg:col-span-2 space-y-6">
                    @php $total = 0 @endphp
                    @foreach(session('cart') as $id => $details)
                        @php $total += $details['price'] * $details['quantity'] @endphp
                        <div class="group bg-white p-6 rounded-[2.5rem] shadow-xl shadow-gray-100/50 border border-gray-50 flex items-center justify-between transition-all hover:border-pink-100">
                            <div class="flex items-center space-x-8">
                                <div class="w-24 h-24 bg-gray-50 rounded-3xl overflow-hidden border border-gray-100 flex-shrink-0 relative">
                                    <img src="{{ asset('storage/' . ($details['image'] ?? '')) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                </div>
                                
                                <div>
                                    <span class="text-[9px] font-black text-[#a61d6d] uppercase tracking-widest bg-pink-50 px-3 py-1 rounded-full italic">
                                        {{ $details['branch'] }}
                                    </span>
                                    <h4 class="text-xl font-black text-gray-800 tracking-tight mt-2">{{ $details['name'] }}</h4>
                                    <p class="text-gray-400 font-bold text-sm mt-1">{{ number_format($details['price']) }}đ</p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-10">
                                <div class="flex items-center space-x-3 bg-gray-50 p-2 rounded-2xl border border-gray-100">
                                    <span class="px-4 font-black text-gray-700 text-sm italic">x{{ $details['quantity'] }}</span>
                                </div>

                                <div class="text-right min-w-[100px]">
                                    <p class="text-lg font-black text-gray-900 tracking-tighter">{{ number_format($details['price'] * $details['quantity']) }}đ</p>
                                    <form action="{{ route('cart.remove') }}" method="POST">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button type="submit" class="text-[10px] font-black text-gray-300 uppercase tracking-widest hover:text-red-500 transition-colors mt-1">
                                            Xóa bỏ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="lg:sticky lg:top-24">
                    <div class="bg-gray-900 rounded-[3rem] p-10 text-white shadow-2xl shadow-gray-200 relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
                        
                        <h3 class="text-xs font-black uppercase tracking-[0.3em] text-gray-400 mb-8">Tóm tắt đơn hàng</h3>
                        
                        <div class="space-y-4 mb-10">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400 font-bold">Tạm tính</span>
                                <span class="font-bold">{{ number_format($total) }}đ</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400 font-bold">Phí dịch vụ</span>
                                <span class="font-bold">Miễn phí</span>
                            </div>
                            <div class="h-px bg-white/10 my-6"></div>
                            <div class="flex justify-between items-end">
                                <span class="text-xs font-black uppercase italic tracking-widest text-[#a61d6d]">Tổng cộng</span>
                                <span class="text-3xl font-black tracking-tighter">{{ number_format($total) }}đ</span>
                            </div>
                        </div>

                        <form action="{{ route('vnpay.payment') }}" method="POST">
                            @csrf
                            <input type="hidden" name="total_amount" value="{{ $total }}">
                            <button type="submit" class="w-full bg-[#a61d6d] text-white py-5 rounded-[2rem] font-black text-xs uppercase tracking-widest flex items-center justify-center space-x-3">
                                <i class="fa-solid fa-credit-card"></i>
                                <span>Thanh toán VNPay</span>
                            </button>
                        </form>
                        {{-- Tìm đoạn form của VNPay và thêm đoạn này vào dưới --}}
                        <form action="{{ route('cod.payment') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-gray-900 text-white py-5 rounded-[2rem] font-black text-xs uppercase tracking-widest flex items-center justify-center space-x-3 hover:bg-black transition-all">
                                <i class="fa-solid fa-truck-fast"></i>
                                <span>Thanh toán khi nhận hàng (COD)</span>
                            </button>
                        </form>
                        
                        <p class="text-[9px] text-gray-500 text-center mt-6 font-bold uppercase tracking-widest leading-relaxed">
                            Hệ thống đảm bảo bởi<br>AEON Security Service
                        </p>
                    </div>

                    <a href="{{ route('shop.index') }}" class="flex items-center justify-center mt-8 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-[#a61d6d] transition-all">
                        <i class="fa-solid fa-arrow-left-long mr-2"></i> Tiếp tục mua hàng
                    </a>
                </div>
            </div>
        @else
            <div class="bg-white rounded-[4rem] p-32 text-center border-2 border-dashed border-gray-100 shadow-sm shadow-gray-50">
                <div class="relative inline-block mb-8">
                    <div class="absolute inset-0 bg-pink-100 blur-3xl rounded-full opacity-50"></div>
                    <i class="fa-solid fa-bag-shopping text-8xl text-[#a61d6d] relative"></i>
                </div>
                <h3 class="text-3xl font-black text-gray-800 italic uppercase tracking-tighter">Giỏ hàng đang trống</h3>
                <p class="text-gray-400 mt-4 mb-12 max-w-xs mx-auto font-medium leading-relaxed">Có vẻ như Đạt chưa chọn được món đồ nào ưng ý tại AEON. Hãy quay lại khám phá nhé!</p>
                <a href="{{ route('shop.index') }}" class="inline-block bg-gray-900 text-white px-16 py-5 rounded-3xl font-black text-xs uppercase tracking-[0.2em] hover:bg-[#a61d6d] transition-all shadow-xl">
                    Khám phá ngay
                </a>
            </div>
        @endif
    </main>
    @endsection