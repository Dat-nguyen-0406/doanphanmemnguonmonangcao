@extends('layouts.app')

@section('title', 'Chi Tiết Cụm Rạp')

@section('content')
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Cụm Rạp - Lotte Cinema</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .momo-pink {
            background-color: #D82D8B;
        }
        .momo-pink-hover:hover {
            background-color: #A50064;
        }
        .selected-date {
            background-color: #D82D8B;
            color: white;
        }
        .time-slot {
            border: 1px solid #E5E7EB;
            border-radius: 8px;
            padding: 8px 12px;
            margin: 4px;
            display: inline-block;
            cursor: pointer;
            transition: all 0.2s;
        }
        .time-slot:hover, .time-slot.selected {
            background-color: #D82D8B;
            color: white;
            border-color: #D82D8B;
        }
    </style>
</head>
<body class="bg-white">
    <!-- Header -->
    <header class="bg-white shadow-sm px-4 py-3 flex items-center justify-between">
        <button class="text-gray-600 text-xl">&larr;</button>
        <h1 class="text-lg font-semibold text-gray-800">Lotte Cinema</h1>
        <div class="flex space-x-2">
            <button class="text-gray-600 text-xl">🔍</button>
            <button class="text-gray-600 text-xl">📤</button>
        </div>
    </header>

    <!-- Thông tin rạp -->
    <section class="px-4 py-4">
        <img src="https://via.placeholder.com/400x200?text=Lotte+Cinema+Cover" alt="Lotte Cinema Cover" class="w-full h-48 object-cover rounded-xl mb-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Lotte Cinema Hà Nội</h2>
        <p class="text-gray-600 mb-3">Tầng 8, TTTM Vincom Center Bà Triệu, 191 Bà Triệu, Hai Bà Trưng, Hà Nội</p>
        <button class="text-blue-600 font-medium">Xem bản đồ</button>
    </section>

    <!-- Bộ lọc ngày -->
    <section class="px-4 py-2 bg-gray-50">
        <div class="flex space-x-2 overflow-x-auto">
            <button class="flex-shrink-0 px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700 selected-date">Hôm nay<br>Th 4, 09/04</button>
            <button class="flex-shrink-0 px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700">Th 5<br>10/04</button>
            <button class="flex-shrink-0 px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700">Th 6<br>11/04</button>
            <button class="flex-shrink-0 px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700">Th 7<br>12/04</button>
            <button class="flex-shrink-0 px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700">CN<br>13/04</button>
            <button class="flex-shrink-0 px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700">Th 2<br>14/04</button>
            <button class="flex-shrink-0 px-4 py-2 rounded-lg bg-white border border-gray-200 text-gray-700">Th 3<br>15/04</button>
        </div>
    </section>

    <!-- Danh sách phim -->
    <section class="px-4 py-4">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Phim đang chiếu</h3>

        <!-- Phim 1 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4 p-4">
            <div class="flex">
                <img src="https://via.placeholder.com/80x120?text=Poster" alt="Movie Poster" class="w-20 h-30 rounded-lg mr-4">
                <div class="flex-1">
                    <h4 class="text-lg font-semibold text-gray-800">Dune: Part Two</h4>
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="bg-yellow-400 text-black px-2 py-1 rounded text-sm font-medium">C13</span>
                        <span class="text-gray-600">Khoa học viễn tưởng</span>
                        <span class="text-gray-600">166 phút</span>
                    </div>
                    <div class="flex flex-wrap">
                        <span class="time-slot">10:00</span>
                        <span class="time-slot">12:30</span>
                        <span class="time-slot">15:00</span>
                        <span class="time-slot">17:30</span>
                        <span class="time-slot">20:00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Phim 2 -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-4 p-4">
            <div class="flex">
                <img src="https://via.placeholder.com/80x120?text=Poster" alt="Movie Poster" class="w-20 h-30 rounded-lg mr-4">
                <div class="flex-1">
                    <h4 class="text-lg font-semibold text-gray-800">Oppenheimer</h4>
                    <div class="flex items-center space-x-2 mb-2">
                        <span class="bg-red-500 text-white px-2 py-1 rounded text-sm font-medium">C18</span>
                        <span class="text-gray-600">Lịch sử</span>
                        <span class="text-gray-600">180 phút</span>
                    </div>
                    <div class="flex flex-wrap">
                        <span class="time-slot">11:00</span>
                        <span class="time-slot">14:00</span>
                        <span class="time-slot">16:30</span>
                        <span class="time-slot">19:00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thêm phim khác nếu cần -->
    </section>

    <script>
        // JavaScript cho tương tác
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.addEventListener('click', function() {
                this.classList.toggle('selected');
            });
        });

        // Bộ lọc ngày
        document.querySelectorAll('.date-button').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.date-button').forEach(btn => btn.classList.remove('selected-date'));
                this.classList.add('selected-date');
            });
        });
    </script>
</body>
</html>
@endsection