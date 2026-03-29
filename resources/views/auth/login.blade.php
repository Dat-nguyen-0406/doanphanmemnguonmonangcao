<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AEON Mall - Đăng nhập</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        .aeon-magenta { color: #a61d6d; }
        .bg-aeon-magenta { background-color: #a61d6d; }
        .focus-magenta:focus { border-color: #a61d6d; ring-color: #a61d6d; }
    </style>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-xl w-full max-w-md border-t-8 border-aeon-magenta">
        <div class="text-center mb-8">
            <div class="inline-block bg-aeon-magenta text-white p-3 rounded-lg font-bold text-2xl mb-4 shadow-md">
                AEON
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Đăng nhập Khách hàng</h2>
            <p class="text-sm text-gray-500 mt-1">Chào mừng bạn trở lại với hệ thống tiện ích AEON</p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf
            
            @if ($errors->any())
                <div class="bg-red-50 text-red-500 p-3 rounded text-sm mb-4 border border-red-100">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $errors->first() }}
                </div>
            @endif

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Địa chỉ Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-envelope"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full pl-10 pr-4 py-2 border rounded-md outline-none focus:border-aeon-magenta transition-all" 
                        placeholder="email@example.com" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Mật khẩu</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" 
                        class="w-full pl-10 pr-4 py-2 border rounded-md outline-none focus:border-aeon-magenta transition-all" 
                        placeholder="••••••••" required>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center text-gray-500 cursor-pointer">
                    <input type="checkbox" class="mr-1 accent-[#a61d6d]"> Ghi nhớ đăng nhập
                </label>
                <a href="#" class="aeon-magenta hover:underline font-semibold">Quên mật khẩu?</a>
            </div>

            <button type="submit" 
                class="w-full bg-aeon-magenta text-white font-bold py-3 rounded-md shadow-lg hover:bg-pink-800 transition duration-300 transform hover:-translate-y-0.5">
                ĐĂNG NHẬP NGAY
            </button>
        </form>

        <div class="mt-8 pt-6 border-t text-center">
            <p class="text-sm text-gray-600">
                Chưa có tài khoản thành viên? 
                <a href="{{ route('register') }}" class="aeon-magenta font-bold hover:underline">Đăng ký ngay</a>
            </p>
        </div>
    </div>

</body>
</html>