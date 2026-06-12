<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suất chiếu - {{ $branch->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .hero-bg {
            background: linear-gradient(180deg, rgba(216,45,139,0.95), rgba(212,42,136,0.85)), url('{{ $branch->image_url ?? "https://images.unsplash.com/photo-1519608487953-e999c86e7455?auto=format&fit=crop&w=1350&q=80" }}');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-50">

    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-[#D82D8B] flex items-center justify-center text-white font-bold">L</div>
                <div>
                    <p class="text-sm font-semibold text-gray-700">Lotte Cinema</p>
                    <p class="text-xs text-gray-500">Rạp và suất chiếu</p>
                </div>
            </a>
        </div>
    </header>

    <div class="hero-bg h-[280px] rounded-b-[2rem] shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-white/90"></div>
        <div class="max-w-7xl mx-auto px-4 h-full flex items-end pb-10">
            <div class="bg-white/90 backdrop-blur-xl rounded-3xl p-6 shadow-xl w-full sm:w-3/4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <p class="text-sm uppercase tracking-[0.3em] text-[#D82D8B] font-semibold">Lotte Cinema</p>
                        <h1 class="text-3xl sm:text-4xl font-black text-gray-900">{{ $branch->name }}</h1>
                        <p class="mt-2 text-sm text-gray-600">{{ $branch->address ?? 'Địa chỉ rạp chưa có' }}</p>
                    </div>
                    <a href="#showtimes" class="inline-flex items-center gap-2 bg-[#D82D8B] hover:bg-[#A50064] text-white rounded-full px-5 py-3 font-semibold transition-all">
                        <i class="fa-solid fa-arrow-down"></i> Xem suất chiếu
                    </a>
                </div>
            </div>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-4 py-10" id="showtimes">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <p class="text-xs text-gray-500 uppercase tracking-[0.2em] mb-2">Suất chiếu</p>
                <h2 class="text-3xl font-bold text-gray-900">Chọn suất chiếu phù hợp</h2>
            </div>
            <div class="inline-flex items-center gap-2 rounded-2xl bg-white border border-gray-200 px-4 py-3 shadow-sm">
                <i class="fa-solid fa-calendar-days text-[#D82D8B]"></i>
                <span class="text-sm text-gray-600">{{ $showtimesGrouped->sum(fn($group) => $group->count()) }} suất chiếu</span>
            </div>
        </div>

        @if($showtimesGrouped->isEmpty())
            <div class="text-center py-16 bg-white rounded-3xl shadow-sm border border-gray-200">
                <i class="fas fa-film text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Hiện tại chưa có suất chiếu nào.</p>
                <a href="{{ route('home') }}" class="inline-block mt-4 bg-[#D82D8B] text-white px-6 py-3 rounded-full font-semibold hover:bg-[#A50064] transition-colors">
                    <i class="fa-solid fa-arrow-left mr-2"></i>Quay về trang chủ
                </a>
            </div>
        @else
            <div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
                @foreach($showtimesGrouped as $movieId => $movieShowtimes)
                    @php $movie = $movieShowtimes->first()->movie; @endphp
                    <div class="group bg-white rounded-3xl border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                        <div class="relative h-64 bg-gray-200 overflow-hidden">
   
                            @php
                                $posterUrl = \Illuminate\Support\Str::startsWith($movie->poster, ['http://', 'https://']) 
                                    ? $movie->poster 
                                    : asset('storage/' . $movie->poster);
                            @endphp
                            
                            <img src="{{ $posterUrl }}"
                                alt="{{ $movie->title }}"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                onerror="this.src='https://placehold.co/400x600?text={{ urlencode($movie->title) }}'" />
                        </div> 
                        <div class="p-5">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $movie->title }}</h3>
                            <p class="text-sm text-gray-600 mb-1">{{ $movie->genre ?? 'Phim chiếu rạp' }} • {{ $movie->duration ?? '120' }} phút</p>
                            <p class="text-sm text-gray-500 mb-4">{{ \Illuminate\Support\Str::limit($movie->description ?? 'Mô tả phim...', 80) }}</p>
                            
                            <div class="mb-4">
                                <p class="text-sm font-semibold text-gray-700 mb-3 border-b pb-2">Lịch chiếu hôm nay:</p>
                                <div class="grid gap-2">
                                    @foreach($movieShowtimes as $showtime)
                                        <a href="{{ route('booking.form', $showtime->id) }}" 
                                           class="flex items-center justify-between p-3 bg-gray-50 hover:bg-[#D82D8B] group/item rounded-xl transition-all border border-transparent hover:border-[#D82D8B]">
                                            <div class="flex items-center gap-3">
                                                <i class="fa-solid fa-clock text-gray-400 group-hover/item:text-white"></i>
                                                <span class="font-bold text-gray-700 group-hover/item:text-white">{{ $showtime->start_time->format('H:i') }}</span>
                                            </div>
                                            <div class="text-right">
                                                <span class="font-bold text-[#D82D8B] group-hover/item:text-white block">{{ number_format($showtime->price) }}đ</span>
                                                <span class="text-[10px] text-gray-500 group-hover/item:text-pink-100 uppercase">Còn {{ $showtime->availableSeatsCount() }} ghế</span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>
</body>
</html>