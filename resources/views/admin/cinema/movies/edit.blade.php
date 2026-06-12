@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Chỉnh Sửa Phim</h2>
    <p class="text-sm text-gray-500 mt-1">Cập nhật thông tin phim</p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-100 max-w-2xl">
    <form action="{{ route('admin.movies.update', $movie->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
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
            <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Tên Phim *</label>
            <input type="text" id="title" name="title" value="{{ $movie->title }}" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                   placeholder="Nhập tên phim">
        </div>

        <div>
            <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Mô Tả</label>
            <textarea id="description" name="description" rows="4"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                      placeholder="Nhập mô tả phim">{{ $movie->description }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="duration" class="block text-sm font-bold text-gray-700 mb-2">Thời Lượng (phút) *</label>
                <input type="number" id="duration" name="duration" value="{{ $movie->duration }}" required min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                       placeholder="Nhập thời lượng">
            </div>

            <div>
                <label for="release_date" class="block text-sm font-bold text-gray-700 mb-2">Ngày Phát Hành *</label>
                <input type="date" id="release_date" name="release_date" value="{{ $movie->release_date }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            </div>
        </div>

        <div>
            <label for="genre" class="block text-sm font-bold text-gray-700 mb-2">Thể Loại</label>
            <input type="text" id="genre" name="genre" value="{{ $movie->genre }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                   placeholder="Vd: Hành động, Kinh dị, Hài hước...">
        </div>

        <div>
            <label for="poster" class="block text-sm font-bold text-gray-700 mb-2">Poster Phim</label>
            @if($movie->poster)
            <div class="mb-4">
                <img src="{{ asset('storage/' . $movie->poster) }}" alt="{{ $movie->title }}" class="w-40 h-56 object-cover rounded-lg">
            </div>
            @endif
            <input type="file" id="poster" name="poster" accept="image/*"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            <p class="text-xs text-gray-500 mt-2">Định dạng: JPG, PNG, GIF. Kích thước tối đa: 2MB</p>
        </div>

        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.movies.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                Hủy
            </a>
            <button type="submit" class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition font-semibold">
                Cập Nhật
            </button>
        </div>
    </form>
</div>
@endsection
