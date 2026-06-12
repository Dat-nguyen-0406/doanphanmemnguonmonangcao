<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt vé xem phim - Hệ thống AEON Cinema</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/dist/css/all.min.css">
    <style>
        .cinema-red { color: #e60012; }
        .bg-cinema-red { background-color: #e60012; }
        .cinema-gradient { background: linear-gradient(135deg, #e60012 0%, #ff6b35 100%); }
        .movie-card:hover { transform: translateY(-8px); }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="cinema-red text-white p-2 rounded-lg font-bold text-xl">🎬</div>
                <div>
                    <h1 class="text-xl font-black text-gray-900">AEON Cinema</h1>
                    <p class="text-xs text-gray-500">Hệ thống rạp chiếu phim</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-6">
                @auth
                    <div class="hidden md:flex items-center space-x-2 text-sm">
                        <i class="fa-solid fa-user-circle cinema-red text-lg"></i>
                        <span class="font-medium text-gray-700">{{ Auth::user()->name }}</span>
                    </div>
                    <a href="{{ route('my.bookings') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 transition-colors">
                        <i class="fa-solid fa-ticket mr-2"></i> Vé của tôi
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-cinema-red transition-colors">
                            <i class="fa-solid fa-sign-out-alt"></i>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium text-gray-700 transition-colors">
                        Đăng nhập
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Banner -->
    <div class="cinema-gradient text-white py-16">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-4xl font-black mb-2">Đặt vé xem phim</h2>
                    <p class="text-lg opacity-90">Khám phá hàng loạt phim hay · Chọn chỗ ngồi yêu thích · Thanh toán an toàn</p>
                </div>
                <div class="hidden lg:block text-7xl opacity-20">🍿</div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                <div class="flex-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-location-dot cinema-red mr-2"></i> Chọn rạp
                    </label>
                    <select id="branchFilter" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all font-medium">
                        <option value="">Tất cả rạp</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2">
                        <i class="fa-solid fa-calendar cinema-red mr-2"></i> Ngày chiếu
                    </label>
                    <input type="date" id="dateFilter" class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition-all font-medium" value="{{ date('Y-m-d') }}">
                </div>
                <button onclick="filterMovies()" class="bg-cinema-red hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold transition-all shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-search mr-2"></i> Lọc
                </button>
            </div>
        </div>

        <!-- Movies Grid -->
        <div>
            <h3 class="text-2xl font-black text-gray-900 mb-6">🎬 Phim đang chiếu</h3>
            
            @if($movies->isEmpty())
                <div class="text-center py-16 bg-white rounded-xl border border-gray-100">
                    <i class="fa-solid fa-film text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">Chưa có phim nào trong danh sách</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($movies as $movie)
                        <div class="movie-card bg-white rounded-xl overflow-hidden shadow-md hover:shadow-2xl transition-all border border-gray-100 group cursor-pointer" onclick="selectMovie({{ $movie->id }}, '{{ $movie->title }}')">
                            <!-- Movie Poster -->
 <div class="relative h-72 overflow-hidden">
    <img src="{{ Str::startsWith($movie->poster, 'http') ? $movie->poster : asset('storage/' . $movie->poster) }}" 
         alt="{{ $movie->title }}"
         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
         onerror="this.src='https://via.placeholder.com/300x400?text={{ urlencode($movie->title) }}'" >
</div>
                            
                            <!-- Movie Info -->
                            <div class="p-4">
                                <h4 class="font-black text-gray-900 text-sm mb-2 line-clamp-2">{{ $movie->title }}</h4>
                                <p class="text-xs text-gray-500 mb-3">{{ $movie->genre ?? 'Phim lôi cuốn' }}</p>
                                
                                <!-- Rating and Duration -->
                                <div class="flex items-center justify-between text-xs text-gray-600 mb-4">
                                    <span><i class="fa-solid fa-clock mr-1"></i> {{ $movie->duration ?? 120 }} phút</span>
                                    <span class="px-2 py-1 bg-gray-100 rounded text-gray-700 font-bold">{{ $movie->rating ?? '16+' }}</span>
                                </div>
                                
                                <!-- Action Button -->
                                <button class="w-full bg-cinema-red hover:bg-red-700 text-white py-2 rounded-lg font-bold transition-colors text-sm">
                                    Chọn phim
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Movie Selection Modal -->
    <div id="movieModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-auto">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
                <h3 class="text-2xl font-black text-gray-900" id="modalTitle">Chọn suất chiếu</h3>
                <button onclick="closeMovieModal()" class="text-gray-500 hover:text-gray-700 text-2xl">×</button>
            </div>
            
            <div class="p-6">
                <!-- Step 1: Select Branch -->
                <div id="step1" class="space-y-4">
                    <h4 class="font-bold text-lg text-gray-900 mb-4">
                        <span class="cinema-red">1.</span> Chọn rạp chiếu
                    </h4>
                    <div class="grid grid-cols-2 gap-3" id="branchList"></div>
                </div>

                <!-- Step 2: Select Showtime -->
                <div id="step2" class="space-y-4 hidden">
                    <h4 class="font-bold text-lg text-gray-900 mb-4">
                        <span class="cinema-red">2.</span> Chọn suất chiếu
                    </h4>
                    <div class="grid grid-cols-4 gap-2" id="showtimeList"></div>
                    <button onclick="goBack()" class="text-cinema-red hover:underline font-bold mt-4">← Quay lại</button>
                </div>

                <!-- Step 3: Confirm -->
                <div id="step3" class="text-center hidden">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-6">
                        <i class="fa-solid fa-check-circle text-green-500 text-5xl mb-4 inline-block"></i>
                        <p class="text-gray-700 text-lg font-bold mb-2">Bạn sắp được chuyển đến chọn ghế</p>
                        <p class="text-gray-600 text-sm" id="confirmText"></p>
                    </div>
                    <button onclick="proceedToSeating()" class="w-full bg-cinema-red hover:bg-red-700 text-white py-3 rounded-lg font-bold transition-colors">
                        Tiếp tục chọn ghế →
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedMovie = null;
        let selectedBranch = null;
        let selectedShowtime = null;

        function selectMovie(movieId, movieTitle) {
            selectedMovie = movieId;
            document.getElementById('modalTitle').textContent = movieTitle;
            document.getElementById('movieModal').classList.remove('hidden');
            loadBranches();
        }

        function closeMovieModal() {
            document.getElementById('movieModal').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step3').classList.add('hidden');
            selectedBranch = null;
            selectedShowtime = null;
        }

        function loadBranches() {
            fetch('/api/branches')
                .then(r => r.json())
                .then(branches => {
                    const html = branches.map(b => `
                        <button onclick="selectBranch(${b.id}, '${b.name}')" class="p-3 border-2 border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 font-bold text-gray-700 transition-all">
                            <i class="fa-solid fa-building mr-2"></i> ${b.name}
                        </button>
                    `).join('');
                    document.getElementById('branchList').innerHTML = html;
                });
        }

        function selectBranch(branchId, branchName) {
            selectedBranch = branchId;
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.remove('hidden');
            loadShowtimes(selectedMovie, branchId);
        }

        function loadShowtimes(movieId, branchId) {
            fetch(`/api/showtimes?movie_id=${movieId}&branch_id=${branchId}`)
                .then(r => r.json())
                .then(showtimes => {
                    const html = showtimes.map(s => `
                        <button onclick="selectShowtime(${s.id}, '${s.time}')" class="p-3 border-2 border-gray-200 rounded-lg hover:border-red-500 hover:bg-red-50 font-bold text-gray-700 transition-all">
                            <i class="fa-solid fa-clock mr-1"></i> ${s.time}
                        </button>
                    `).join('');
                    document.getElementById('showtimeList').innerHTML = html || '<p class="col-span-4 text-center text-gray-500">Không có suất chiếu</p>';
                });
        }

        function selectShowtime(showtimeId, showtimeTime) {
            selectedShowtime = showtimeId;
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step3').classList.remove('hidden');
            document.getElementById('confirmText').textContent = `Suất chiếu: ${showtimeTime}`;
        }

        function goBack() {
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step1').classList.remove('hidden');
            selectedBranch = null;
        }

        function proceedToSeating() {
            if (selectedShowtime) {
                window.location.href = `/booking/${selectedShowtime}`;
            }
        }

        function filterMovies() {
            const branch = document.getElementById('branchFilter').value;
            const date = document.getElementById('dateFilter').value;
            window.location.href = `/?branch=${branch}&date=${date}`;
        }
    </script>
</body>
</html>
