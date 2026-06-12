@extends('layouts.admin')
@section('content')
<div class="max-w-xl mx-auto">
    <a href="{{ route('admin.restaurant.tables', $restaurant->id) }}" class="text-sm text-pink-600 hover:underline block mb-4">← Quản lý bàn</a>
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <h1 class="text-xl font-black text-gray-800 mb-6">✏️ Sửa bàn — {{ $table->label ?? $table->table_number }}</h1>
        <form action="{{ route('admin.restaurant.tables.update', [$restaurant->id, $table->id]) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Số bàn *</label>
                        <input type="text" name="table_number" value="{{ old('table_number', $table->table_number) }}" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nhãn hiển thị</label>
                        <input type="text" name="label" value="{{ old('label', $table->label) }}"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Sức chứa *</label>
                        <input type="number" name="capacity" value="{{ old('capacity', $table->capacity) }}" min="1" max="50" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tầng *</label>
                        <input type="number" name="floor" value="{{ old('floor', $table->floor) }}" min="1" max="10" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Hình dạng</label>
                        <select name="shape" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm bg-white focus:border-pink-500 outline-none">
                            <option value="square" {{ $table->shape === 'square' ? 'selected' : '' }}>⬜ Vuông</option>
                            <option value="round" {{ $table->shape === 'round' ? 'selected' : '' }}>🔵 Tròn</option>
                            <option value="long" {{ $table->shape === 'long' ? 'selected' : '' }}>⬛ Dài</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Trạng thái</label>
                        <div class="flex items-center gap-3 py-3">
                            <input type="checkbox" name="is_active" id="is_active" {{ $table->is_active ? 'checked' : '' }} class="w-4 h-4 accent-pink-600">
                            <label for="is_active" class="text-sm text-gray-700">Đang hoạt động</label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Vị trí X</label>
                        <input type="number" name="position_x" value="{{ old('position_x', $table->position_x) }}" min="0"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Vị trí Y</label>
                        <input type="number" name="position_y" value="{{ old('position_y', $table->position_y) }}" min="0"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                    </div>
                </div>
                <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3.5 rounded-xl text-sm">
                    Cập nhật bàn
                </button>
            </div>
        </form>
    </div>
</div>
@endsection