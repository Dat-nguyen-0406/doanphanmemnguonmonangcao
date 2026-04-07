<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AEON Mall - {{ $branch->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        .aeon-gradient {
            /* Sử dụng ảnh từ database, nếu không có thì dùng ảnh mặc định */
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('{{ $branch->image_url ?? "https://images.unsplash.com/photo-1519608487953-e999c86e7455?auto=format&fit=crop&w=1350&q=80" }}');
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
            <span>Hệ thống AEON</span>
            <span>/</span>
            <span>{{ $branch->city->name }}</span> <span>/</span>
            <span class="text-gray-800 font-bold uppercase">{{ $branch->name }}</span>
        </div>
    </div>

    <div class="aeon-gradient h-[350px] relative text-white flex items-end pb-10">
        <div class="max-w-7xl mx-auto px-4 w-full flex items-center space-x-6">
            <div class="bg-white p-4 rounded shadow-lg w-32 h-32 flex items-center justify-center">
                <img src="{{ asset('images/aeon-logo.png') }}" 
                                         class="w-full h-full object-contain opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all" 
                                         alt="AEON">
            </div>
            
            <div class="space-y-2">
                <h1 class="text-3xl font-bold uppercase">{{ $branch->name }}</h1>
                <p class="text-sm opacity-90"><i class="fa-solid fa-location-dot mr-2"></i>{{ $branch->address }}</p>
                <div class="flex items-center space-x-2 text-xs pt-2">
                    <i class="fa-solid fa-tag text-pink-400"></i>
                    <span>Ưu đãi thành viên: Giảm 10% khi đặt bàn qua App | Tích điểm đổi quà</span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <h2 class="text-3xl font-bold text-[#a61d6d] text-center mb-8 uppercase italic">Tiện ích khả dụng</h2>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-[#a61d6d]">
                <h3 class="font-bold text-xl mb-2">🎬 Đặt vé xem phim</h3>
                <p class="text-gray-600 text-sm mb-4">Xem lịch chiếu và chọn chỗ ngồi yêu thích tại CGV/Lotte AEON.</p>
                <a href="#" class="block text-center bg-[#a61d6d] text-white px-4 py-2 rounded text-sm font-bold w-full hover:bg-pink-800 transition">ĐẶT VÉ NGAY</a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-[#a61d6d]">
                <h3 class="font-bold text-xl mb-2">🍴 Đặt bàn nhà hàng</h3>
                <p class="text-gray-600 text-sm mb-4">Khám phá khu ẩm thực và đặt chỗ trước để không phải chờ đợi.</p>
                <button class="bg-[#a61d6d] text-white px-4 py-2 rounded text-sm font-bold w-full">KHÁM PHÁ</button>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-[#a61d6d]">
                <h3 class="font-bold text-xl mb-2">🛍️ Mua sắm trực tuyến</h3>
                <p class="text-gray-600 text-sm mb-4">Hàng ngàn sản phẩm từ AEON Supermarket đang chờ bạn.</p>
                <a href="{{ route('shop.index') }}" class="block text-center border border-[#a61d6d] text-[#a61d6d] px-4 py-2 rounded text-sm font-bold w-full hover:bg-pink-50 transition">ĐI CHỢ NGAY</a>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-[#a61d6d]">
                <h3 class="font-bold text-xl mb-2">📍 Bản đồ vị trí</h3>
                <p class="text-gray-600 text-sm mb-4">Tìm kiếm vị trí gian hàng nhanh nhất tại {{ $branch->name }}.</p>
                <a href="{{ $branch->map_link }}" target="_blank" class="block text-center border border-[#a61d6d] text-[#a61d6d] px-4 py-2 rounded text-sm font-bold w-full hover:bg-pink-50 transition">MỞ BẢN ĐỒ</a>
            </div>
        </div>
    </div>

</body>
</html>