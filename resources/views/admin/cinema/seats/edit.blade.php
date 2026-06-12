@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Chỉnh Sửa Ghế</h2>
    <p class="text-sm text-gray-500 mt-1">Cập nhật thông tin ghế</p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 max-w-2xl">
    <form action="{{ route('admin.seats.update', $seat->id) }}" method="POST" class="p-6 space-y-6">
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
            <label for="branch_id" class="block text-sm font-bold text-gray-700 mb-2">Chi Nhánh *</label>
            <select id="branch_id" name="branch_id" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                <option value="">-- Chọn Chi Nhánh --</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ $seat->branch_id == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="row" class="block text-sm font-bold text-gray-700 mb-2">Hàng Ghế *</label>
            <input type="text" id="row" name="row" value="{{ $seat->row }}" required 
                   maxlength="10" placeholder="Vd: A, B, C..."
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
        </div>

        <div>
            <label for="seat_number" class="block text-sm font-bold text-gray-700 mb-2">Số Ghế *</label>
            <input type="number" id="seat_number" name="seat_number" value="{{ $seat->seat_number }}" 
                   required min="1" 
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                   placeholder="Vd: 1, 2, 3...">
        </div>

        <div>
            <label for="type" class="block text-sm font-bold text-gray-700 mb-2">Loại Ghế *</label>
            <select id="type" name="type" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                <option value="">-- Chọn Loại --</option>
                <option value="normal" {{ $seat->type == 'normal' ? 'selected' : '' }}>Ghế Thường</option>
                <option value="vip" {{ $seat->type == 'vip' ? 'selected' : '' }}>Ghế VIP</option>
            </select>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                <i class="fa-solid fa-info-circle mr-2"></i>
                <strong>Ký hiệu ghế:</strong> Hàng ghế + Số ghế (vd: A1, A2, B1, B2...)
            </p>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.seats.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Hủy
            </a>
            <button type="submit" class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition font-semibold">
                Cập Nhật
            </button>
        </div>
    </form>
</div>
@endsection
