<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AEON Mall - Chọn địa điểm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        .aeon-magenta { color: #a61d6d; }
        .bg-aeon-magenta { background-color: #a61d6d; }
    </style>
</head>
<body class="bg-gray-50">

    <nav class="bg-white border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16">
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <div class="bg-aeon-magenta text-white p-2 rounded font-bold">AEON</div>
                </div>
                <div class="hidden md:flex space-x-4 text-sm font-medium text-gray-600">
                    <a href="{{ route('home') }}" class="aeon-magenta border-b-2 border-aeon-magenta pb-5">Hệ thống AEON</a>
                    <a href="{{ route('shop.index') }}" class="hover:aeon-magenta">Mua sắm trực tuyến</a>
                    <a href="#" class="hover:aeon-magenta">Khuyến mãi</a>
                </div>
            </div>
            
            <div class="flex items-center space-x-4 text-gray-500 text-sm">
                @auth
                    <span>Chào, {{ Auth::user()->name }}</span>
                @else
                    <a href="{{ route('login') }}" class="hover:text-aeon-magenta">Đăng nhập</a>
                @endauth
                <i class="fa-solid fa-magnifying-glass cursor-pointer"></i>
            </div>
        </div>
    </nav>

    <div class="relative bg-black h-64 flex items-center overflow-hidden">
        <img src="https://images.unsplash.com/photo-1563298723-dcfebaa392e3?auto=format&fit=crop&w=1200" class="absolute w-full h-full object-cover opacity-50">
        <div class="relative max-w-7xl mx-auto px-4 w-full flex items-center text-white">
            <div class="bg-white p-4 rounded w-24 h-24 flex items-center justify-center shadow-lg">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/44/Aeon_logo.svg/1200px-Aeon_logo.svg.png" class="w-full">
            </div>
            <div class="ml-6">
                <h1 class="text-3xl font-bold">Hệ thống AEON Mall Việt Nam</h1>
                <p class="text-sm mt-1 italic opacity-80"><i class="fa-solid fa-building mr-1"></i> Trải nghiệm dịch vụ tiện ích tiêu chuẩn Nhật Bản</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-10">
        <h2 class="text-2xl font-bold text-center aeon-magenta mb-8 uppercase">Chọn trung tâm AEON của bạn</h2>

        <div class="bg-white rounded-lg shadow-md border overflow-hidden">
            <div class="p-4 border-b bg-gray-50 flex items-center space-x-4">
                <span class="text-sm font-semibold text-gray-600">Vị trí:</span>
                <select class="border rounded px-4 py-1 text-sm outline-none focus:border-aeon-magenta">
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col md:flex-row">
                <div class="w-full md:w-1/3 border-r max-h-[400px] overflow-y-auto bg-white">
                    @foreach($cities as $city)
                        @foreach($city->branches as $branch)
                            <a href="{{ route('aeon.detail', $branch->id) }}" class="p-4 flex items-center space-x-3 hover:bg-pink-50 border-b block transition">
                                <img src="{{ $branch->image_url ?? 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/44/Aeon_logo.svg/1024px-Aeon_logo.svg.png' }}" class="w-10 h-10 border rounded object-cover">
                                <div>
                                    <p class="font-bold text-sm">{{ $branch->name }}</p>
                                    <p class="text-[10px] text-gray-500 uppercase">{{ $city->name }}</p>
                                </div>
                            </a>
                        @endforeach
                    @endforeach
                </div>

                <div class="w-full md:w-2/3 p-12 bg-gray-50 flex flex-col items-center justify-center text-center">
                    <i class="fa-solid fa-map-location-dot text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Vui lòng chọn một chi nhánh từ danh sách bên trái để xem chi tiết các dịch vụ tiện ích và mua sắm.</p>
                </div>
            </div>
        </div>
    </div>

</body>
</html>