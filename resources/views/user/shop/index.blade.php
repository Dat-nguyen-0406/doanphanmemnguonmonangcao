@extends('layouts.shop')

@section('title', 'Gian hàng Online')

@section('content')
    <header class="bg-white py-8 border-b">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="flex-shrink-0">
                <h1 class="text-3xl font-black text-gray-900 italic uppercase tracking-tighter">Gian hàng AEON Online</h1>
                <p class="text-gray-500 font-medium">Chi nhánh: 
                    <span class="text-[#a61d6d] font-extrabold">{{ $products->first()->branch->name ?? 'Tất cả hệ thống' }}</span>
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full lg:w-auto">
                <div class="flex items-center bg-gray-50 p-1.5 rounded-2xl border border-gray-100 w-full sm:w-auto">
                    <span class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Danh mục</span>
                    <form action="{{ route('shop.index') }}" method="GET" class="m-0">
                        <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                        <input type="hidden" name="partner_id" value="{{ request('partner_id') }}">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="category_id" onchange="this.form.submit()" class="bg-white border-none rounded-xl text-sm font-bold py-2.5 px-6 outline-none shadow-sm cursor-pointer focus:ring-0">
                            <option value="">Tất cả</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="relative w-full sm:w-64 lg:w-80">
                    <form action="{{ route('shop.index') }}" method="GET" class="m-0">
                        {{-- Giữ các tham số lọc cũ khi tìm kiếm --}}
                        <input type="hidden" name="branch_id" value="{{ request('branch_id') }}">
                        <input type="hidden" name="category_id" value="{{ request('category_id') }}">
                        <input type="hidden" name="partner_id" value="{{ request('partner_id') }}">
                        
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="Tìm sản phẩm..." 
                               class="w-full pl-12 pr-10 py-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold outline-none focus:bg-white focus:border-[#a61d6d] focus:ring-4 focus:ring-pink-50 transition-all shadow-sm">
                        
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </div>

                        @if(request('search'))
                            <a href="{{ route('shop.index', request()->except('search')) }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-300 hover:text-red-500 transition-colors">
                                <i class="fa-solid fa-circle-xmark"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

    <div class="bg-white border-b overflow-x-auto custom-scrollbar">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-5">Thương hiệu đối tác</h3>
            <div class="flex space-x-8">
                <a href="{{ route('shop.index', ['branch_id' => request('branch_id')]) }}" class="flex-shrink-0 flex flex-col items-center group">
                    <div class="w-16 h-16 rounded-2xl border-2 {{ !request('partner_id') ? 'border-[#a61d6d] bg-pink-50' : 'border-gray-100' }} flex items-center justify-center transition-all group-hover:scale-105">
                        <i class="fa-solid fa-border-all {{ !request('partner_id') ? 'text-[#a61d6d]' : 'text-gray-300' }} text-xl"></i>
                    </div>
                    <span class="text-[10px] font-bold mt-3 uppercase tracking-tight">Tất cả</span>
                </a>

                @foreach($partners as $partner)
                    <a href="{{ route('shop.index', ['branch_id' => request('branch_id'), 'partner_id' => $partner->id]) }}" 
                    class="flex-shrink-0 flex flex-col items-center group">
                        
                        {{-- Khối chứa Avatar --}}
                        <div class="w-16 h-16 rounded-2xl border-2 {{ request('partner_id') == $partner->id ? 'border-[#a61d6d] bg-pink-50' : 'border-gray-100' }} 
                                    flex items-center justify-center overflow-hidden transition-all duration-300 group-hover:scale-110 group-hover:shadow-lg bg-white relative">
                            
                            @if($partner->image) {{-- Kiểm tra nếu đối tác có ảnh đại diện --}}
                                <img src="{{ asset('storage/' . $partner->image) }}" 
                                    alt="{{ $partner->name }}" 
                                    class="w-full h-full object-cover">
                            @else
                                {{-- Nếu không có ảnh, hiển thị 2 chữ cái đầu của tên trên nền màu --}}
                                <div class="w-full h-full bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center">
                                    <span class="text-sm font-black text-gray-400 uppercase italic">
                                        {{ substr($partner->name, 0, 2) }}
                                    </span>
                                </div>
                            @endif

                            {{-- Lớp phủ khi đang được chọn (Active state) --}}
                            @if(request('partner_id') == $partner->id)
                                <div class="absolute inset-0 border-2 border-[#a61d6d] rounded-2xl pointer-events-none"></div>
                            @endif
                        </div>

                        {{-- Tên đối tác --}}
                        <span class="text-[10px] font-bold mt-3 uppercase tracking-tight transition-colors 
                                    {{ request('partner_id') == $partner->id ? 'text-[#a61d6d]' : 'text-gray-500 group-hover:text-gray-900' }}">
                            {{ $partner->name }}
                        </span>
                    </a>
                    @endforeach
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 py-16">
        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                @foreach($products as $product)
                <div class="group bg-white rounded-[2rem] overflow-hidden border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-500">
                    <div class="relative h-72 bg-gray-50 overflow-hidden">
                        <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                        <div class="absolute top-5 left-5">
                            <span class="bg-white/90 backdrop-blur text-[#a61d6d] text-[10px] font-black px-4 py-1.5 rounded-full shadow-sm uppercase tracking-wider">
                                <i class="fa-solid fa-location-dot mr-1"></i> {{ $product->branch->name ?? 'AEON' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-8">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-[10px] font-black text-pink-500 uppercase tracking-widest">{{ $product->category->name }}</span>
                            <span class="text-[10px] font-bold text-gray-300 uppercase italic"><i class="fa-solid fa-shop mr-1"></i> {{ $product->user->name }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 line-clamp-1 tracking-tight">{{ $product->name }}</h3>
                        <div class="mt-6 flex items-center justify-between">
                            <p class="text-2xl font-black text-[#a61d6d] tracking-tighter">{{ number_format($product->price) }}<span class="text-xs ml-0.5 font-bold uppercase">đ</span></p>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Stock</p>
                                <p class="text-sm font-black text-gray-700">{{ $product->stock }}</p>
                            </div>
                        </div>
                        <a href="{{ route('cart.add', $product->id) }}" 
                        class="w-full mt-6 block text-center bg-gray-900 text-white py-3 rounded-2xl font-bold text-sm hover:bg-[#a61d6d] transition-all">
                            THÊM VÀO GIỎ
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-[3rem] p-24 text-center border-2 border-dashed border-gray-100">
                <i class="fa-solid fa-box-open text-7xl text-gray-100 mb-6"></i>
                <h3 class="text-2xl font-black text-gray-800 italic uppercase">Hết hàng rồi ơi!</h3>
                <p class="text-gray-400 mt-3 max-w-sm mx-auto">Thương hiệu này hiện chưa có mặt hàng nào tại chi nhánh bạn chọn.</p>
                <a href="{{ route('shop.index', ['branch_id' => request('branch_id')]) }}" class="mt-10 inline-block bg-[#a61d6d] text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest">Xem tất cả hàng</a>
            </div>
        @endif
    </main>
@endsection