<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tiện Ích AEON</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <nav class="bg-white shadow-md p-4 mb-6">
        <div class="container mx-auto max-w-6xl flex justify-between items-center">
            <a href="{{ url('/') }}" class="text-2xl font-bold text-red-600">
                🛒 AEON MALL
            </a>
            <div class="space-x-4">
                <a href="{{ route('restaurants.index') }}" class="text-gray-700 hover:text-red-500 font-semibold transition-colors">
                    🍽️ Đặt Bàn Nhà Hàng
                </a>
            </div>
        </div>
    </nav>

    <main class="min-h-screen">
        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white text-center p-4 mt-8">
        <p>&copy; 2026 Hệ thống Website Tiện ích AEON - Đồ án Môn học</p>
    </footer>

</body>

</html>