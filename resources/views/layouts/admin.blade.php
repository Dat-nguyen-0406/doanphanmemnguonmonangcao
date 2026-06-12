<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AEON Mall</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-slate-900 text-white flex-shrink-0 hidden md:flex flex-col shadow-2xl">
            <div class="p-6 text-2xl font-black border-b border-slate-800 text-center tracking-tighter">
                AEON <span class="text-pink-500 font-black">ADMIN</span>
            </div>
            
            <nav class="flex-1 mt-6 px-4 space-y-2 overflow-y-auto">
                <a href="{{ route('admin.dashboard') }}" 
                class="block py-2.5 px-4 rounded-xl transition duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-pink-600 text-white shadow-lg' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                    <i class="fa-solid fa-chart-line mr-2"></i> Dashboard
                </a>

                {{-- KHU VỰC DÀNH RIÊNG CHO ADMIN TỔNG (ROLE 1) --}}
                @if(Auth::user()->role == 1)
                    <div class="pt-4 pb-2">
                        <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mb-2 tracking-widest">Hệ thống</p>
                        
                        <a href="{{ route('admin.cities.index') }}" 
                        class="block py-2.5 px-4 rounded-xl transition duration-200 {{ request()->routeIs('admin.cities.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                            <i class="fa-solid fa-city mr-2 text-blue-400"></i> Quản lý Thành phố
                        </a>

                        <a href="{{ route('admin.branches.create') }}" 
                        class="block py-2.5 px-4 rounded-xl transition duration-200 {{ request()->routeIs('admin.branches.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                            <i class="fa-solid fa-shop mr-2 text-green-400"></i> Quản lý Chi nhánh
                        </a>

                        <a href="{{ route('admin.users.index') }}" 
                        class="block py-2.5 px-4 rounded-xl transition duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                            <i class="fa-solid fa-users-gear mr-2 text-pink-400"></i> Quản lý Đối tác
                        </a>
                    </div>
                @endif

                {{-- KHU VỰC DÀNH CHO ĐỐI TÁC CINEMA (ROLE 2) --}}
                @if(Auth::user()->role == 1 || Auth::user()->role == 2)
                    <div class="pt-4 pb-2 border-t border-slate-800" 
                        x-data="{ openCinema: {{ request()->routeIs('admin.cinema.*', 'admin.movies.*', 'admin.showtimes.*', 'admin.seats.*') ? 'true' : 'false' }} }">
                        
                        <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mb-2 tracking-widest">Phim & Cinema</p>
                        
                        <button @click="openCinema = !openCinema" 
                            class="w-full flex items-center justify-between py-2.5 px-4 rounded-xl transition duration-200 {{ request()->routeIs('admin.cinema.*', 'admin.movies.*', 'admin.showtimes.*', 'admin.seats.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                            <div class="flex items-center">
                                <i class="fa-solid fa-clapperboard mr-2 text-red-400"></i>
                                <span class="font-bold text-sm">Quản lý Cinema</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300" :class="openCinema ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="openCinema" x-collapse x-cloak class="pl-9 mt-1 space-y-1">
                            <a href="{{ route('admin.cinema.dashboard') }}" 
                            class="block py-2 text-xs {{ request()->routeIs('admin.cinema.dashboard') ? 'text-pink-500 font-bold' : 'text-gray-500 hover:text-white' }} transition-colors">
                                <i class="fa-solid fa-chart-bar mr-2 text-[10px]"></i> Doanh thu
                            </a>

                            <a href="{{ route('admin.movies.index') }}" 
                            class="block py-2 text-xs {{ request()->routeIs('admin.movies.*') ? 'text-pink-500 font-bold' : 'text-gray-500 hover:text-white' }} transition-colors">
                                <i class="fa-solid fa-film mr-2 text-[10px]"></i> Quản lý Phim
                            </a>

                            <a href="{{ route('admin.showtimes.index') }}" 
                            class="block py-2 text-xs {{ request()->routeIs('admin.showtimes.*') ? 'text-pink-500 font-bold' : 'text-gray-500 hover:text-white' }} transition-colors">
                                <i class="fa-solid fa-calendar mr-2 text-[10px]"></i> Lịch Chiếu
                            </a>

                            <a href="{{ route('admin.seats.index') }}" 
                            class="block py-2 text-xs {{ request()->routeIs('admin.seats.*') ? 'text-pink-500 font-bold' : 'text-gray-500 hover:text-white' }} transition-colors">
                                <i class="fa-solid fa-chair mr-2 text-[10px]"></i> Sơ đồ Ghế
                            </a>
                        </div>
                    </div>
                @endif
                
                {{-- PHÂN KHU DÀNH CHO FOOD (ROLE 1 & 3) --}}
                    @if(Auth::user()->role == 1 || Auth::user()->role == 3)
                        <div class="pt-4 pb-2 border-t border-slate-800" 
                            x-data="{ openRestaurant: {{ request()->routeIs('admin.restaurant.*') ? 'true' : 'false' }} }">
                            
                            <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mb-2 tracking-widest">Ẩm thực & Nhà hàng</p>
                            
                            <button @click="openRestaurant = !openRestaurant" 
                                class="w-full flex items-center justify-between py-2.5 px-4 rounded-xl transition duration-200 {{ request()->routeIs('admin.restaurant.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                                <div class="flex items-center">
                                    <i class="fa-solid fa-utensils mr-2 text-orange-400"></i>
                                    <span class="font-bold text-sm">Quản lý Restaurant</span>
                                </div>
                                <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300" :class="openRestaurant ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="openRestaurant" x-collapse x-cloak class="pl-9 mt-1 space-y-1">
                                
                                {{-- Nút Tổng quan: Chỉ sáng khi đang ở đúng route index gốc --}}
                                <a href="{{ route('admin.restaurant.index') }}" 
                                class="block py-2 text-xs {{ request()->routeIs('admin.restaurant.index') ? 'text-pink-500 font-bold' : 'text-gray-500 hover:text-white' }} transition-colors">
                                    <i class="fa-solid fa-house-chimney mr-2 text-[10px]"></i> Tổng quan Nhà hàng
                                </a>

                                <a href="{{ route('admin.restaurant.bookings') }}" 
                                class="block py-2 text-xs {{ request()->routeIs('admin.restaurant.bookings') ? 'text-pink-500 font-bold' : 'text-gray-500 hover:text-white' }} transition-colors">
                                    <span class="flex items-center justify-between w-full">
                                        <span><i class="fa-solid fa-calendar-check mr-2 text-[10px]"></i> Đặt bàn</span>
                                        @if(isset($pendingBookingCount) && $pendingBookingCount > 0)
                                            <span class="bg-orange-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full mr-2">{{ $pendingBookingCount }}</span>
                                        @endif
                                    </span>
                                </a>

                                <a href="{{ route('admin.restaurant.index') }}?section=tables" 
                                class="block py-2 text-xs {{ request()->routeIs('admin.restaurant.tables*') ? 'text-pink-500 font-bold' : 'text-gray-500 hover:text-white' }} transition-colors">
                                    <i class="fa-solid fa-table-cells-large mr-2 text-[10px]"></i> Quản lý Bàn
                                </a>
                                
                                <a href="{{ route('admin.restaurant.index') }}?section=menu" 
                                class="block py-2 text-xs {{ request()->routeIs('admin.restaurant.menu*') ? 'text-pink-500 font-bold' : 'text-gray-500 hover:text-white' }} transition-colors">
                                    <i class="fa-solid fa-book-open mr-2 text-[10px]"></i> Thực đơn / Menu
                                </a>

                                <a href="{{ route('admin.restaurant.create') }}" 
                                class="block py-2 text-xs {{ request()->routeIs('admin.restaurant.create') ? 'text-pink-500 font-bold' : 'text-gray-500 hover:text-white' }} transition-colors">
                                    <i class="fa-solid fa-circle-plus mr-2 text-[10px]"></i> Thêm Nhà hàng
                                </a>
                            </div>
                        </div>
                    @endif

                {{-- PHÂN KHU DÀNH CHO SHOP (ROLE 1 & 4) --}}
                @if(Auth::user()->role == 1 || Auth::user()->role == 4)
                    <div class="pt-4 pb-2 border-t border-slate-800" 
                        x-data="{ openShop: {{ request()->routeIs('admin.shop.*', 'admin.category.*') ? 'true' : 'false' }} }">
                        
                        <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mb-2 tracking-widest">Bán hàng Online</p>
                        
                        <button @click="openShop = !openShop" 
                            class="w-full flex items-center justify-between py-2.5 px-4 rounded-xl transition duration-200 {{ request()->routeIs('admin.shop.*', 'admin.category.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800 text-gray-400 hover:text-white' }}">
                            <div class="flex items-center">
                                <i class="fa-solid fa-bag-shopping mr-2 text-yellow-400"></i>
                                <span class="font-bold text-sm">Quản lý Shop</span>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-300" :class="openShop ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="openShop" x-collapse x-cloak class="pl-9 mt-1 space-y-1">
                            <a href="{{ route('admin.category.index') }}" 
                            class="block py-2 text-xs {{ request()->routeIs('admin.category.*') ? 'text-pink-500 font-bold' : 'text-gray-500 hover:text-white' }} transition-colors">
                                <i class="fa-solid fa-layer-group mr-2 text-[10px]"></i> Quản lý Danh mục
                            </a>

                            <a href="{{ route('admin.shop.index') }}" 
                            class="block py-2 text-xs {{ (request()->routeIs('admin.shop.*') && !request()->routeIs('admin.shop.report')) ? 'text-pink-500 font-bold' : 'text-gray-500 hover:text-white' }} transition-colors">
                                <i class="fa-solid fa-boxes-stacked mr-2 text-[10px]"></i> Quản lý Sản phẩm
                            </a>

                            <a href="{{ route('admin.shop.report') }}" 
                            class="block py-2 text-xs {{ request()->routeIs('admin.shop.report') ? 'text-pink-500 font-bold' : 'text-gray-500 hover:text-white' }} transition-colors">
                                <i class="fa-solid fa-chart-line mr-2 text-[10px]"></i> Báo cáo Doanh thu
                            </a>
                        </div>
                    </div>
                @endif
            </nav>

            <div class="p-4 border-t border-slate-800 text-[10px] text-center text-gray-500 italic">
                &copy; 2026 AEON Mall - HUCE Project
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8 border-b border-gray-100">
                <div class="flex items-center">
                    <button class="md:hidden mr-4 text-gray-600"><i class="fa-solid fa-bars"></i></button>
                    <h2 class="text-lg font-bold text-gray-700 uppercase tracking-tight">Hệ thống Quản trị</h2>
                </div>
                
                <div class="flex items-center space-x-6">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-800 leading-none">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-pink-500 font-bold uppercase">
                            @if(Auth::user()->role == 1) Admin Tổng @else Đối tác AEON @endif
                        </p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white px-4 py-2 rounded-lg text-xs font-bold transition-all duration-300">
                            ĐĂNG XUẤT <i class="fa-solid fa-right-from-bracket ml-1"></i>
                        </button>
                    </form>
                </div>
            </header>

            
                <main class="flex-1 overflow-x-hidden overflow-y-auto p-8 bg-slate-50">

    {{-- THÔNG BÁO --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-check mr-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-exclamation mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow">
            <div class="font-bold mb-2">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                Có lỗi xảy ra:
            </div>

            <ul class="list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>
                
        </div>
    </div>
</body>
</html>