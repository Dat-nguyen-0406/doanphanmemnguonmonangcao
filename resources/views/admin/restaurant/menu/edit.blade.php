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

        <form action="{{ route('admin.restaurant.menu.update', [$restaurant->id, $item->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
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

            @php
                $itemImageSrc = $item->image_url
                    ? (\Illuminate\Support\Str::startsWith($item->image_url, ['http://', 'https://'])
                        ? $item->image_url
                        : asset('storage/' . $item->image_url))
                    : '';
            @endphp

            {{-- ẢNH - File picker (kéo-thả, giống form chỉnh sửa nhà hàng) --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Ảnh món ăn</label>

                <div id="image-drop-zone"
                     class="relative w-full border-2 border-dashed rounded-xl
                            flex flex-col items-center justify-center gap-2
                            hover:border-pink-400 hover:bg-pink-50 transition-all
                            {{ $itemImageSrc ? 'border-gray-200' : 'border-gray-300' }}"
                     style="min-height: 160px;">

                    <img id="image-preview"
                         src="{{ $itemImageSrc }}"
                         alt="Preview"
                         class="{{ $itemImageSrc ? '' : 'hidden' }} w-full h-40 object-cover rounded-xl">

                    <div id="image-placeholder"
                         onclick="document.getElementById('image_file').click()"
                         class="flex flex-col items-center gap-2 py-6 cursor-pointer w-full {{ $itemImageSrc ? 'hidden' : '' }}">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-300"></i>
                        <p class="text-sm font-bold text-gray-400">Nhấn để chọn ảnh</p>
                        <p class="text-xs text-gray-300">JPG, PNG · Tối đa 5MB</p>
                    </div>

                    <button type="button" id="image-change-btn"
                            onclick="document.getElementById('image_file').click()"
                            class="{{ $itemImageSrc ? '' : 'hidden' }} absolute bottom-2 right-2 bg-white/90 hover:bg-white text-gray-600 text-xs font-bold px-3 py-1.5 rounded-lg shadow border border-gray-200 transition">
                        <i class="fa-solid fa-pen mr-1"></i> Đổi ảnh
                    </button>
                </div>

                <input type="file" id="image_file" name="image"
                       accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">

                <p id="image-filename" class="text-xs text-gray-400 mt-1.5 hidden">
                    <i class="fa-solid fa-paperclip mr-1"></i><span></span>
                </p>

                @error('image')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
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