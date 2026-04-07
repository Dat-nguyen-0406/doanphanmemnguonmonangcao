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
        /* Tùy chỉnh thanh cuộn cho danh sách chi nhánh */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #a61d6d; border-radius: 10px; }
    </style>
</head>
<body class="bg-gray-50">

    <nav class="bg-white border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16">
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <div class="bg-aeon-magenta text-white p-2 rounded font-bold shadow-sm">AEON</div>
                </div>
                <div class="hidden md:flex space-x-4 text-sm font-medium text-gray-600">
                    <a href="{{ route('home') }}" class="aeon-magenta border-b-2 border-aeon-magenta pb-5 transition-all">Hệ thống AEON</a>
                    <a href="{{ route('shop.index') }}" class="hover:text-aeon-magenta transition-colors">Mua sắm trực tuyến</a>
                    <a href="#" class="hover:text-aeon-magenta transition-colors">Khuyến mãi</a>
                </div>
            </div>
            
            <div class="flex items-center space-x-4 text-gray-500 text-sm">
                @auth
                    <span class="font-medium text-gray-700">Chào, <span class="text-aeon-magenta">{{ Auth::user()->name }}</span></span>
                @else
                    <a href="{{ route('login') }}" class="hover:text-aeon-magenta font-medium">Đăng nhập</a>
                @endauth
                <i class="fa-solid fa-magnifying-glass cursor-pointer hover:text-aeon-magenta transition-colors"></i>
            </div>
        </div>
    </nav>

    <div class="relative bg-black h-64 flex items-center overflow-hidden">
        <img src="https://images.unsplash.com/photo-1563298723-dcfebaa392e3?auto=format&fit=crop&w=1200" class="absolute w-full h-full object-cover opacity-50">
        <div class="relative max-w-7xl mx-auto px-4 w-full flex items-center text-white">
            <div class="bg-white p-4 rounded-xl w-24 h-24 flex items-center justify-center shadow-2xl border border-pink-100">
                <img src="{{ asset('images/aeon-logo.png') }}" 
                     class="w-full object-contain" 
                     alt="AEON Logo"
                     onerror="this.src='https://via.placeholder.com/100x40?text=AEON'">
            </div>
            <div class="ml-6">
                <h1 class="text-4xl font-black tracking-tight">Hệ thống AEON Mall Việt Nam</h1>
                <p class="text-base mt-2 italic opacity-90 font-light text-pink-100">
                    <i class="fa-solid fa-building-circle-check mr-2"></i> Trải nghiệm dịch vụ tiện ích tiêu chuẩn Nhật Bản
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 py-12">
        <h2 class="text-2xl font-black text-center aeon-magenta mb-10 uppercase tracking-widest">Chọn trung tâm AEON của bạn</h2>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="p-5 border-b bg-gray-50/50 flex items-center space-x-4">
                <span class="text-xs font-black text-gray-500 uppercase tracking-tighter">Vị trí hiện tại:</span>
                <select class="border-gray-200 rounded-lg px-4 py-1.5 text-sm outline-none focus:ring-2 focus:ring-aeon-magenta/20 focus:border-aeon-magenta transition-all font-medium text-gray-700">
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex flex-col md:flex-row">
                <div class="w-full md:w-1/3 border-r h-[450px] overflow-y-auto bg-white custom-scrollbar">
                    @foreach($cities as $city)
                        @foreach($city->branches as $branch)
                            <a href="{{ route('aeon.detail', $branch->id) }}" class="p-5 flex items-center space-x-4 hover:bg-pink-50/50 border-b last:border-b-0 block transition-all group">
                                <div class="w-12 h-12 border border-gray-100 rounded-xl flex-shrink-0 flex items-center justify-center bg-white shadow-sm group-hover:border-aeon-magenta/30 transition-all p-1.5">
                                    <img src="{{ asset('images/aeon-logo.png') }}" 
                                         class="w-full h-full object-contain opacity-80 group-hover:opacity-100 group-hover:scale-110 transition-all" 
                                         alt="AEON">
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm group-hover:text-aeon-magenta transition-colors">{{ $branch->name }}</p>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">
                                        <i class="fa-solid fa-location-dot mr-1 text-aeon-magenta/50"></i> {{ $city->name }}
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    @endforeach
                </div>

                <div class="w-full md:w-2/3 p-12 bg-slate-50/30 flex flex-col items-center justify-center text-center">
                    <div class="bg-white p-8 rounded-full shadow-inner mb-6">
                        <i class="fa-solid fa-map-location-dot text-7xl text-gray-200"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-700 mb-2 font-mono">CHƯA CHỌN ĐỊA ĐIỂM</h3>
                    <p class="text-gray-400 max-w-sm text-sm leading-relaxed font-medium">Vui lòng chọn một chi nhánh từ danh sách bên trái để xem thông tin chi tiết, bản đồ và các ưu đãi hiện có.</p>
                </div>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('keydown', function(e) {
        // Nhấn Ctrl + Shift + S để vào trang Login Admin
        if (e.ctrlKey && e.shiftKey && e.code === 'KeyS') {
            window.location.href = "{{ route('admin.login') }}";
        }
    });
</script>
</body>
</html>