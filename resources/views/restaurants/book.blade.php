@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8" style="max-width: 800px; margin: 0 auto;">
    <a href="{{ route('restaurants.index') }}" class="text-blue-500 mb-4 inline-block" style="text-decoration: none; color: #007bff; margin-bottom: 20px;">
        &larr; Quay lại danh sách
    </a>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden border" style="border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
        <div class="bg-gray-50 p-6 border-b" style="background-color: #f8f9fa; padding: 20px; border-bottom: 1px solid #ddd;">
            <h1 class="text-2xl font-bold text-red-600" style="color: #e50050; margin-top:0;">Đặt bàn tại: {{ $restaurant->name }}</h1>
            <p class="text-gray-600" style="color: #666;"><strong>Loại hình:</strong> {{ $restaurant->cuisine_type ?? 'Đang cập nhật' }}</p>
        </div>

        <div class="p-6" style="padding: 20px;">
            <form action="{{ route('restaurants.book.submit', $restaurant->id) }}" method="POST">
                @csrf
                @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" style="background-color: #fee2e2; border-color: #f87171; color: #b91c1c; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    {{ session('error') }}
                </div>
                @endif

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label for="booking_date" style="display: block; font-weight: bold; margin-bottom: 5px;">Ngày đến (*)</label>
                        <input type="date" id="booking_date" name="booking_date" required min="{{ date('Y-m-d') }}" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>

                    <div>
                        <label for="booking_time" style="display: block; font-weight: bold; margin-bottom: 5px;">Giờ đến (*)</label>
                        <input type="time" id="booking_time" name="booking_time" required style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="guests_count" style="display: block; font-weight: bold; margin-bottom: 5px;">Số lượng người (*)</label>
                    <input type="number" id="guests_count" name="guests_count" min="1" max="50" required placeholder="Ví dụ: 4" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="note" style="display: block; font-weight: bold; margin-bottom: 5px;">Ghi chú cho nhà hàng (Tùy chọn)</label>
                    <textarea id="note" name="note" rows="3" placeholder="Ví dụ: Lấy bàn gần cửa sổ, có ghế trẻ em..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; resize: vertical;"></textarea>
                </div>

                <button type="submit" style="display: block; width: 100%; background-color: #e50050; color: white; padding: 15px; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer;">
                    Chuyển sang Bước Xác nhận & Đặt Cọc
                </button>
            </form>
        </div>
    </div>
</div>
@endsection