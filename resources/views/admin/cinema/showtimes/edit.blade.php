@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Chỉnh Sửa Lịch Chiếu</h2>
    <p class="text-sm text-gray-500 mt-1">Cập nhật thông tin lịch chiếu</p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 max-w-2xl">
    <form action="{{ route('admin.showtimes.update', $showtime->id) }}" method="POST" class="p-6 space-y-6">
        @csrf
        @method('PUT')

        @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <p class="font-bold mb-2">Lỗi xác thực:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div>
            <label for="movie_id" class="block text-sm font-bold text-gray-700 mb-2">Phim *</label>
            <select id="movie_id" name="movie_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                <option value="">-- Chọn Phim --</option>
                @foreach($movies as $movie)
                    <option value="{{ $movie->id }}" {{ $showtime->movie_id == $movie->id ? 'selected' : '' }}>
                        {{ $movie->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="branch_id" class="block text-sm font-bold text-gray-700 mb-2">Chi Nhánh *</label>
            <select id="branch_id" name="branch_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                <option value="">-- Chọn Chi Nhánh --</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $showtime->branch_id == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="start_time" class="block text-sm font-bold text-gray-700 mb-2">Thời Gian Chiếu *</label>
            <input type="datetime-local" id="start_time" name="start_time" 
                   value="{{ $showtime->start_time->format('Y-m-d\TH:i') }}" 
                   required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="price_normal" class="block text-sm font-bold text-gray-700 mb-2">Giá Ghế Thường (VND) *</label>
                <input type="number" id="price_normal" name="price_normal" 
                       value="{{ $showtime->price_normal }}" 
                       required min="0" step="1000"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                       placeholder="Nhập giá ghế thường">
            </div>

            <div>
                <label for="price_vip" class="block text-sm font-bold text-gray-700 mb-2">Giá Ghế VIP (VND) *</label>
                <input type="number" id="price_vip" name="price_vip" 
                       value="{{ $showtime->price_vip }}" 
                       required min="0" step="1000"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                       placeholder="Nhập giá ghế VIP">
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                <i class="fa-solid fa-info-circle mr-2"></i>
                <strong>Lưu ý:</strong> Giá ghế sẽ được áp dụng cho tất cả ghế loại tương ứng tại chi nhánh này.
            </p>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.showtimes.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Hủy
            </a>
            <button type="submit" class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition font-semibold">
                Cập Nhật
            </button>
        </div>
    </form>
</div>
@endsection
