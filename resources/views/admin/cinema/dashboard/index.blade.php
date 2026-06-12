@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Dashboard Doanh Thu</h2>
    <p class="text-sm text-gray-500 mt-1">Thống kê doanh thu theo thời gian và danh mục</p>
</div>

<!-- Bộ lọc -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-8 p-6">
    <form action="{{ route('admin.cinema.dashboard') }}" method="GET" class="flex gap-4 flex-wrap items-end">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Từ Ngày</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Đến Ngày</label>
            <input type="date" name="date_to" value="{{ $dateTo }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
        </div>
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-2">Kiểu Thống Kê</label>
            <select name="period" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Theo Ngày</option>
                <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Theo Tuần</option>
                <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Theo Tháng</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition font-semibold">
            <i class="fa-solid fa-filter mr-1"></i> Lọc
        </button>
    </form>
</div>

<!-- Thống kê tổng quát -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-sm border-l-4 border-pink-500 p-6">
        <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Tổng Doanh Thu</p>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalRevenue, 0, ',', '.') }} <span class="text-lg">₫</span></p>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border-l-4 border-blue-500 p-6">
        <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Tổng Vé Bán</p>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $completedBookings }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $totalBookings }} đơn hàng tổng cộng</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-500 p-6">
        <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Giá Trung Bình</p>
        <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($averageTicketPrice, 0, ',', '.') }} <span class="text-lg">₫</span></p>
        <p class="text-xs text-gray-400 mt-1">Trên mỗi vé</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm border-l-4 border-purple-500 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Export Dữ Liệu</p>
                <p class="text-sm text-gray-600 mt-2">Xuất báo cáo CSV</p>
            </div>
            <button onclick="openExportModal()" class="px-3 py-2 bg-purple-100 hover:bg-purple-200 text-purple-600 rounded-lg text-sm font-semibold transition">
                <i class="fa-solid fa-download mr-1"></i>Xuất
            </button>
        </div>
    </div>
</div>

<!-- Biểu đồ doanh thu theo thời gian -->
<div class="bg-white rounded-lg shadow-sm border border-gray-100 mb-8 p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">Doanh Thu {{ $period == 'day' ? 'Theo Ngày' : ($period == 'week' ? 'Theo Tuần' : 'Theo Tháng') }}</h3>
    
    <div class="overflow-x-auto">
        <div style="height: 300px">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<!-- Doanh thu theo phim -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Top 10 Phim Bán Chạy Nhất</h3>
        
        <div class="space-y-3">
            @foreach($revenueByMovie as $movie)
            <div class="border-b border-gray-100 pb-3 last:border-0">
                <div class="flex justify-between items-start mb-2">
                    <span class="font-semibold text-gray-800 text-sm">{{ $movie->title }}</span>
                    <span class="text-pink-600 font-bold">{{ number_format($movie->revenue, 0, ',', '.') }} ₫</span>
                </div>
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>{{ $movie->booking_count }} vé</span>
                    <div class="w-24 bg-gray-200 rounded-full h-2">
                        <div class="bg-pink-500 h-2 rounded-full" style="width: {{ ($movie->revenue / $revenueByMovie[0]->revenue * 100) }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Doanh thu theo chi nhánh -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Doanh Thu Theo Chi Nhánh</h3>
        
        <div class="space-y-3">
            @foreach($revenueByBranch as $branch)
            <div class="border-b border-gray-100 pb-3 last:border-0">
                <div class="flex justify-between items-start mb-2">
                    <span class="font-semibold text-gray-800 text-sm">{{ $branch->name }}</span>
                    <span class="text-blue-600 font-bold">{{ number_format($branch->revenue, 0, ',', '.') }} ₳</span>
                </div>
                <div class="flex justify-between items-center text-xs text-gray-500">
                    <span>{{ $branch->booking_count }} vé</span>
                    <div class="w-24 bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($branch->revenue / $revenueByBranch[0]->revenue * 100) }}%"></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal Export -->
<div id="exportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Xuất Báo Cáo</h3>
        </div>
        
        <form action="{{ route('admin.cinema.export-revenue') }}" method="GET" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Loại Báo Cáo *</label>
                <select name="type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                    <option value="movie">Theo Phim</option>
                    <option value="branch">Theo Chi Nhánh</option>
                    <option value="daily">Theo Ngày</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Từ Ngày *</label>
                <input type="date" name="date_from" value="{{ $dateFrom }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Đến Ngày *</label>
                <input type="date" name="date_to" value="{{ $dateTo }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded p-3">
                <p class="text-xs text-blue-800">
                    <i class="fa-solid fa-info-circle mr-1"></i>
                    Báo cáo sẽ được tải xuống ở định dạng CSV
                </p>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeExportModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Hủy
                </button>
                <button type="submit" class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 font-semibold flex items-center">
                    <i class="fa-solid fa-download mr-2"></i>Xuất CSV
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function openExportModal() {
    document.getElementById('exportModal').classList.remove('hidden');
}

function closeExportModal() {
    document.getElementById('exportModal').classList.add('hidden');
}

// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
const data = @json($revenueByPeriod);
const labels = Object.keys(data);
const values = Object.values(data);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Doanh Thu',
            data: values,
            borderColor: '#ec4899',
            backgroundColor: 'rgba(236, 72, 153, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#ec4899',
            pointBorderColor: '#fff',
            pointBorderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                labels: {
                    font: {
                        size: 12
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN', {
                            style: 'currency',
                            currency: 'VND',
                            maximumFractionDigits: 0
                        }).format(value);
                    }
                }
            }
        }
    }
});
</script>
@endsection
