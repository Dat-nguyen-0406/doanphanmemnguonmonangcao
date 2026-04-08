<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AEON Mall</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                <div class="pt-4 pb-2 border-t border-slate-800">
                    <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mb-2 tracking-widest">Phim & Cinema</p>
                    <a href="#" class="block py-2.5 px-4 rounded-xl hover:bg-slate-800 text-gray-400 hover:text-white transition">
                        <i class="fa-solid fa-film mr-2"></i> Lịch chiếu & Phim
                    </a>
                </div>
                @endif
                
                {{-- PHÂN KHU DÀNH CHO FOOD (ROLE 1 & 3) --}}
                @if(Auth::user()->role == 1 || Auth::user()->role == 3)
                <div class="pt-2 border-t border-slate-800">
                    <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mb-2 tracking-widest">Ẩm thực & Quán ăn</p>
                    <a href="#" class="block py-2.5 px-4 rounded-xl hover:bg-slate-800 text-gray-400 hover:text-white transition">
                        <i class="fa-solid fa-utensils mr-2 text-orange-400"></i> Quản lý Thực đơn
                    </a>
                </div>
                @endif

                {{-- PHÂN KHU DÀNH CHO SHOP (ROLE 1 & 4) --}}
                @if(Auth::user()->role == 1 || Auth::user()->role == 4)
                <div class="pt-2 border-t border-slate-800">
                    <p class="text-[10px] font-bold text-slate-500 uppercase px-4 mb-2 tracking-widest">Bán hàng Online</p>
                    <a href="#" class="block py-2.5 px-4 rounded-xl hover:bg-slate-800 text-gray-400 hover:text-white transition">
                        <i class="fa-solid fa-bag-shopping mr-2 text-yellow-400"></i> Quản lý Sản phẩm
                    </a>
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
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>