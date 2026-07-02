<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AEON Mall - @yield('title', 'Mua sắm Online')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .aeon-magenta { color: #a61d6d; }
        .bg-aeon-magenta { background-color: #a61d6d; }
        .custom-scrollbar::-webkit-scrollbar { height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #a61d6d; border-radius: 10px; }
    </style>
</head>
<body class="bg-gray-50 font-sans">

    @if(session('success'))
    <div id="flash-message" class="fixed top-20 right-5 z-[100] transform transition-all duration-500 ease-in-out">
        <div class="bg-gray-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center space-x-4 border-l-4 border-[#a61d6d]">
            <div class="bg-[#a61d6d] p-2 rounded-full">
                <i class="fa-solid fa-check text-white text-xs"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Thông báo</p>
                <p class="text-sm font-bold tracking-tight">{{ session('success') }}</p>
            </div>
            <button onclick="document.getElementById('flash-message').remove()" class="text-gray-500 hover:text-white ml-4">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>

    <script>
        // Tự động ẩn sau 3 giây
        setTimeout(() => {
            const msg = document.getElementById('flash-message');
            if(msg) {
                msg.style.opacity = '0';
                msg.style.transform = 'translateX(20px)';
                setTimeout(() => msg.remove(), 500);
            }
        }, 3000);
    </script>
    @endif

    <nav class="bg-white border-b sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between h-16">
            <div class="flex items-center space-x-8">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="bg-[#a61d6d] text-white p-2 rounded font-bold">AEON</div>
                    <span class="text-xs text-[#a61d6d] font-semibold leading-tight hidden sm:block">MALL<br>UTILITY</span>
                </a>
                <div class="hidden md:flex space-x-6 text-sm font-medium">
                    <a href="{{ route('home') }}" class="hover:text-[#a61d6d]">Hệ thống AEON</a>
                    <a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.index') ? 'text-[#a61d6d] border-b-2 border-[#a61d6d] pb-5' : 'hover:text-[#a61d6d]' }}">Mua sắm trực tuyến</a>
                </div>
            </div>
            <div class="flex items-center space-x-5">
                  @auth
                    <div class="flex items-center space-x-3">
                        <!-- Bấm vào ảnh đại diện để xem Profile -->
                        <a href="{{ route('profile.index') }}" class="flex items-center space-x-3 group">
                            <!-- Ô tròn ảnh đại diện -->
                            <div class="w-10 h-10 rounded-full border-2 border-aeon-magenta overflow-hidden bg-gray-200 flex-shrink-0 shadow-sm group-hover:ring-2 group-hover:ring-offset-2 group-hover:ring-[#a61d6d] transition-all">
    {{-- SỬA: Auth::user()->avatar thành Auth::user()->image --}}
                                @if(Auth::user()->image)
                                    <img src="{{ asset('storage/' . Auth::user()->image) }}" alt="Avatar" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-pink-100 text-aeon-magenta">
                                        <i class="fa-solid fa-user text-lg"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Tên người dùng -->
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-700 text-sm leading-none group-hover:text-aeon-magenta">
                                    Chào, <span class="text-aeon-magenta font-bold">{{ Auth::user()->name }}</span>
                                </span>
                            </div>
                        </a>

                        <!-- Nút Đăng xuất tách biệt -->
                        <form action="{{ route('logout') }}" method="POST" class="inline border-l pl-3 ml-2 border-gray-200">
                            @csrf
                            <button type="submit" class="text-[10px] text-red-500 hover:underline uppercase font-bold tracking-tighter">
                                Thoát
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="hover:text-aeon-magenta font-medium text-sm">Đăng nhập</a>
                @endauth
                <div class="relative cursor-pointer">
                    <a href="{{ route('cart.index') }}">
                        <i class="fa-solid fa-cart-shopping text-gray-600 text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-[#a61d6d] text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">
                            {{ is_array(session('cart')) ? count(session('cart')) : 0 }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="bg-white border-t py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center space-x-2 mb-4">
                <div class="bg-gray-800 text-white px-2 py-1 rounded font-bold italic">AEON</div>
                <span class="text-xs text-gray-800 font-black uppercase tracking-widest">Vietnam</span>
            </div>
            <p class="text-gray-400 text-[10px] font-medium uppercase tracking-widest">Developed for HUCE Software Architecture Course</p>
        </div>
    </footer>

</body>
</html>