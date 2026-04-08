<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>AEON Mall - Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
</head>
<body class="bg-slate-900 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-md border-b-8 border-gray-800">
        <div class="text-center mb-6">
            <div class="inline-block bg-gray-800 text-white p-3 rounded font-bold text-xl mb-2">
                AEON <span class="text-pink-500">ADMIN</span>
            </div>
            <h2 class="text-xl font-bold text-gray-700">Hệ thống quản trị</h2>
        </div>

        <form action="{{ route('admin.login') }}" method="POST" class="space-y-4">
            @csrf
            @if(session('error'))
                <div class="bg-red-100 text-red-600 p-3 rounded text-xs border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">TÀI KHOẢN QUẢN TRỊ</label>
                <input type="email" name="email" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-gray-800 outline-none" placeholder="admin@aeon.com.vn" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1">MẬT MÃ</label>
                <input type="password" name="password" class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-gray-800 outline-none" placeholder="••••••••" required>
            </div>

            <button type="submit" class="w-full bg-gray-800 text-white font-bold py-3 rounded hover:bg-black transition shadow-lg">
                ĐĂNG NHẬP HỆ THỐNG
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-xs text-gray-400 hover:text-gray-600 underline">Quay lại trang chủ</a>
        </div>
    </div>

</body>
</html>