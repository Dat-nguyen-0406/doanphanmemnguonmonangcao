@extends('layouts.admin')
@section('content')
<div class="max-w-xl mx-auto">
    <a href="{{ route('admin.restaurant.menu', $restaurant->id) }}" class="text-sm text-pink-600 hover:underline block mb-4">← Quản lý Menu — {{ $restaurant->name }}</a>
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <h1 class="text-xl font-black text-gray-800 mb-6">✏️ Sửa món — {{ $item->name }}</h1>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-5 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.restaurant.menu.update', [$restaurant->id, $item->id]) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tên món *</label>
                <input type="text" name="name" value="{{ old('name', $item->name) }}" required
                       placeholder="Phở bò đặc biệt..."
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Danh mục *</label>
                    <select name="category" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm bg-white focus:border-pink-500 outline-none">
                        <option value="main"      {{ old('category', $item->category) === 'main'      ? 'selected' : '' }}>🍜 Món chính</option>
                        <option value="appetizer" {{ old('category', $item->category) === 'appetizer' ? 'selected' : '' }}>🥗 Khai vị</option>
                        <option value="dessert"   {{ old('category', $item->category) === 'dessert'   ? 'selected' : '' }}>🍮 Tráng miệng</option>
                        <option value="drink"     {{ old('category', $item->category) === 'drink'     ? 'selected' : '' }}>🥤 Đồ uống</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Giá (VNĐ) *</label>
                    <input type="number" name="price" value="{{ old('price', $item->price) }}" required min="0" step="1000"
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mô tả</label>
                <textarea name="description" rows="3"
                          placeholder="Mô tả ngắn về món ăn..."
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none resize-none">{{ old('description', $item->description) }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">URL ảnh</label>
                <input type="url" name="image_url" value="{{ old('image_url', $item->image_url) }}" placeholder="https://..."
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                @if($item->image_url)
                <div class="mt-2">
                    <img src="{{ $item->image_url }}" style="height:80px;border-radius:10px;object-fit:cover;" onerror="this.style.display='none'">
                </div>
                @endif
            </div>

            <div class="flex items-center gap-3 py-1">
                <input type="checkbox" name="is_available" id="is_available"
                       {{ old('is_available', $item->is_available) ? 'checked' : '' }}
                       class="w-4 h-4 accent-pink-600">
                <label for="is_available" class="text-sm text-gray-700 font-medium">Đang phục vụ</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-pink-600 hover:bg-pink-700 text-white font-bold py-3.5 rounded-xl text-sm transition">
                    Cập nhật món ăn
                </button>
                <a href="{{ route('admin.restaurant.menu', $restaurant->id) }}"
                   class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3.5 rounded-xl text-sm transition">
                    Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection