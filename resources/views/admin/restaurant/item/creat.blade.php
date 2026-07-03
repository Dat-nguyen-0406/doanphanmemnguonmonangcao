@extends('layouts.admin')
@section('content')
<div class="max-w-xl mx-auto">
    <a href="{{ route('admin.restaurant.index') }}" class="text-sm text-pink-600 hover:underline block mb-4">
        ← Danh sách nhà hàng
    </a>

    <div class="bg-white rounded-2xl shadow-sm p-8">
        <h1 class="text-xl font-black text-gray-800 mb-6">+ Thêm nhà hàng mới</h1>

        <form action="{{ route('admin.restaurant.store') }}" method="POST"
              enctype="multipart/form-data" class="space-y-5">
            @csrf

            @if($errors->any())
            <div class="bg-red-50 text-red-700 rounded-xl px-4 py-3 text-sm">
                @foreach($errors->all() as $e)<p>• {{ $e }}</p>@endforeach
            </div>
            @endif

            {{-- Chi nhánh --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Chi nhánh AEON *</label>
                <select name="branch_id" required
                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm bg-white focus:border-pink-500 outline-none">
                    <option value="">-- Chọn chi nhánh --</option>
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>
                        {{ $b->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Tên nhà hàng --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tên nhà hàng *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nhập tên nhà hàng..."
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
            </div>

            {{-- Loại ẩm thực --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Loại hình ẩm thực</label>
                <input type="text" name="cuisine_type" value="{{ old('cuisine_type') }}" placeholder="Ví dụ: Lẩu nướng, Món Nhật, Fastfood..."
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
            </div>

            {{-- Mô tả --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mô tả nhà hàng</label>
                <textarea name="description" rows="4" placeholder="Nhập thông tin giới thiệu ngắn về nhà hàng..."
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">{{ old('description') }}</textarea>
            </div>

            {{-- ẢNH - File picker (ĐÃ FIX: Bỏ onclick ở div cha) --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Ảnh đại diện</label>

                <div id="image-drop-zone"
                     class="relative w-full border-2 border-dashed border-gray-300 rounded-xl
                            flex flex-col items-center justify-center gap-2
                            hover:border-pink-400 hover:bg-pink-50 transition-all"
                     style="min-height: 160px;">

                    <img id="image-preview" src="" alt="Preview" class="hidden w-full h-40 object-cover rounded-xl">

                    <div id="image-placeholder" 
                         onclick="document.getElementById('image_file').click()"
                         class="flex flex-col items-center gap-2 py-6 cursor-pointer w-full">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-300"></i>
                        <p class="text-sm font-bold text-gray-400">Nhấn để chọn ảnh</p>
                        <p class="text-xs text-gray-300">JPG, PNG · Tối đa 5MB</p>
                    </div>

                    <button type="button" id="image-change-btn"
                            onclick="document.getElementById('image_file').click()"
                            class="hidden absolute bottom-2 right-2 bg-white/90 hover:bg-white text-gray-600 text-xs font-bold px-3 py-1.5 rounded-lg shadow border border-gray-200 transition">
                        <i class="fa-solid fa-pen mr-1"></i> Đổi ảnh
                    </button>
                </div>

                <input type="file" id="image_file" name="image" required
                       accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">

                <p id="image-filename" class="text-xs text-gray-400 mt-1.5 hidden">
                    <i class="fa-solid fa-paperclip mr-1"></i><span></span>
                </p>

                @error('image')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Trạng thái hoạt động --}}
            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" checked
                       class="w-4 h-4 text-pink-600 border-gray-300 rounded focus:ring-pink-500">
                <label for="is_active" class="text-sm font-bold text-gray-700 cursor-pointer select-none">Đang hoạt động</label>
            </div>

            {{-- HÀNH ĐỘNG --}}
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('admin.restaurant.index') }}"
                   class="px-5 py-2.5 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50 transition">
                    Hủy
                </a>
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-pink-600 hover:bg-pink-700 shadow-sm shadow-pink-100 transition">
                    Lưu nhà hàng
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const input    = document.getElementById('image_file');
const preview  = document.getElementById('image-preview');
const holder   = document.getElementById('image-placeholder');
const changeBtn= document.getElementById('image-change-btn');
const filename = document.getElementById('image-filename');
const dropZone = document.getElementById('image-drop-zone');

input.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    showPreview(file);
});

// Drag & drop
dropZone.addEventListener('dragover', function (e) {
    e.preventDefault();
    this.classList.add('border-pink-400', 'bg-pink-50');
});
dropZone.addEventListener('dragleave', function () {
    this.classList.remove('border-pink-400', 'bg-pink-50');
});
dropZone.addEventListener('drop', function (e) {
    e.preventDefault();
    this.classList.remove('border-pink-400', 'bg-pink-50');
    const file = e.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        showPreview(file);
    }
});

function showPreview(file) {
    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        holder.classList.add('hidden');
        changeBtn.classList.remove('hidden');
        filename.classList.remove('hidden');
        filename.querySelector('span').textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
    };
    reader.readAsDataURL(file);
}
</script>
@endsection