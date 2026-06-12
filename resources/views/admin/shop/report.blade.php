@extends('layouts.admin')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight uppercase">Báo cáo doanh thu Shop</h1>
            <p class="text-sm text-gray-500 font-medium">Phân tích hiệu quả kinh doanh của gian hàng online</p>
        </div>
       
        <div class="flex items-center space-x-2 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
            <span class="text-[10px] font-bold text-gray-400 uppercase px-2">Kỳ báo cáo:</span>
            <span class="text-xs font-bold text-pink-600 bg-pink-50 px-3 py-1 rounded-lg">7 ngày gần nhất</span>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-50 text-blue-500 rounded-2xl"><i class="fa-solid fa-coins text-xl"></i></div>
                <span class="text-[10px] font-black text-blue-500 bg-blue-50 px-2 py-1 rounded-full">+12% vs tuần trước</span>
            </div>
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Tổng doanh thu</p>
            <p class="text-3xl font-black text-slate-900 mt-1">{{ number_format($totalRevenue) }}<span class="text-sm ml-1">đ</span></p>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-pink-50 text-pink-500 rounded-2xl"><i class="fa-solid fa-cart-shopping text-xl"></i></div>
            </div>
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Sản phẩm bán chạy nhất</p>
            <p class="text-xl font-black text-slate-900 mt-1 truncate">
                {{ $bestSellers->first()->name ?? 'N/A' }}
            </p>
        </div>
    </div>

    <!-- Charts & Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Biểu đồ -->
        <div class="lg:col-span-2 bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-sm font-black text-slate-800 uppercase tracking-tighter">Biểu đồ xu hướng</h3>
                <i class="fa-solid fa-ellipsis-vertical text-gray-300"></i>
            </div>
            <div class="h-[300px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Bảng Top Sản Phẩm -->
        <div class="bg-slate-900 p-6 rounded-3xl shadow-2xl text-white">
            <h3 class="text-sm font-black text-pink-500 uppercase tracking-widest mb-6">Top 5 bán chạy</h3>
            <div class="space-y-6">
                @foreach($bestSellers as $item)
                <div class="flex items-center justify-between group">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-[10px] font-black group-hover:bg-pink-600 transition-colors">
                            {{ $loop->iteration }}
                        </div>
                        <div>
                            <p class="text-xs font-bold leading-none">{{ $item->name }}</p>
                            <p class="text-[9px] text-gray-500 uppercase mt-1">Đã bán: {{ $item->sold_count }}</p>
                        </div>
                    </div>
                    <i class="fa-solid fa-chevron-right text-[10px] text-slate-700"></i>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Xử lý dữ liệu từ Laravel gửi sang
    const rawData = {!! json_encode($revenueLast7Days) !!};
    const labels = rawData.map(item => item.date);
    const dataValues = rawData.map(item => item.daily_total);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu ngày',
                data: dataValues,
                borderColor: '#db2777', // Pink-600
                backgroundColor: 'rgba(219, 39, 119, 0.05)',
                borderWidth: 4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#db2777',
                pointBorderWidth: 2,
                pointRadius: 4,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: false }, ticks: { font: { size: 10 } } },
                x: { grid: { display: false }, ticks: { font: { size: 10 } } }
            }
        }
    });
</script>
@endsection