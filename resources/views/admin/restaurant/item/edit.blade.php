@extends('layouts.admin')
@section('content')
<div class="max-w-xl mx-auto">
    <a href="{{ route('admin.restaurant.index') }}" class="text-sm text-pink-600 hover:underline block mb-4">
        ← Danh sách nhà hàng
    </a>

    <div class="bg-white rounded-2xl shadow-sm p-8">
        <h1 class="text-xl font-black text-gray-800 mb-6">✏️ Sửa nhà hàng — {{ $restaurant->name }}</h1>

        <form action="{{ route('admin.restaurant.update', $restaurant->id) }}" method="POST"
              enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

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
                    @foreach($branches as $b)
                    <option value="{{ $b->id }}" {{ $restaurant->branch_id == $b->id ? 'selected' : '' }}>
                        {{ $b->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Tên nhà hàng --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tên nhà hàng *</label>
                <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" required
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
            </div>

            {{-- Loại ẩm thực --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Loại hình ẩm thực</label>
                <input type="text" name="cuisine_type" value="{{ old('cuisine_type', $restaurant->cuisine_type) }}"
                       placeholder="VD: Nhật Bản, Lẩu, BBQ, Hàn Quốc..."
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
            </div>

            {{-- Mô tả --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mô tả</label>
                <textarea name="description" rows="3"
                          class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none resize-none">{{ old('description', $restaurant->description) }}</textarea>
            </div>

            {{-- ẢNH - File picker --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Ảnh đại diện</label>

                <div id="image-drop-zone"
                     onclick="document.getElementById('image_file').click()"
                     class="relative w-full border-2 border-dashed rounded-xl
                            flex flex-col items-center justify-center gap-2 cursor-pointer
                            hover:border-pink-400 hover:bg-pink-50 transition-all
                            {{ $restaurant->image_url ? 'border-gray-200' : 'border-gray-300' }}"
                     style="min-height: 160px;">

                    {{-- Preview: ảnh hiện tại hoặc ảnh mới chọn --}}
                    <img id="image-preview"
                         src="{{ $restaurant->image_url ?? '' }}"
                         alt="Preview"
                         class="{{ $restaurant->image_url ? '' : 'hidden' }} w-full h-40 object-cover rounded-xl">

                    {{-- Placeholder (ẩn nếu đã có ảnh) --}}
                    <div id="image-placeholder" class="flex flex-col items-center gap-2 py-6 {{ $restaurant->image_url ? 'hidden' : '' }}">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-gray-300"></i>
                        <p class="text-sm font-bold text-gray-400">Nhấn để chọn ảnh</p>
                        <p class="text-xs text-gray-300">JPG, PNG · Tối đa 5MB</p>
                    </div>

                    {{-- Nút đổi ảnh --}}
                    <button type="button" id="image-change-btn"
                            class="{{ $restaurant->image_url ? '' : 'hidden' }} absolute bottom-2 right-2
                                   bg-white/90 hover:bg-white text-gray-600 text-xs font-bold
                                   px-3 py-1.5 rounded-lg shadow border border-gray-200 transition">
                        <i class="fa-solid fa-pen mr-1"></i> Đổi ảnh
                    </button>
                </div>

                {{-- Input file ẩn --}}
                <input type="file" id="image_file" name="image"
                       accept="image/jpeg,image/png,image/jpg,image/webp" class="hidden">

                {{-- Tên file mới chọn --}}
                <p id="image-filename" class="text-xs text-gray-400 mt-1.5 hidden">
                    <i class="fa-solid fa-paperclip mr-1"></i><span></span>
                </p>

                @if($restaurant->image_url)
                <p class="text-xs text-gray-400 mt-1.5">
                    <i class="fa-solid fa-image mr-1 text-gray-300"></i>
                    Ảnh hiện tại đang dùng. Chọn ảnh mới để thay thế.
                </p>
                @endif

                @error('image')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Trạng thái --}}
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ $restaurant->is_active ? 'checked' : '' }}
                       class="w-4 h-4 accent-pink-600">
                <label for="is_active" class="text-sm text-gray-700">Đang hoạt động</label>
            </div>

            <button type="submit"
                    class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3.5 rounded-xl text-sm transition">
                Cập nhật nhà hàng
            </button>
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

changeBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    input.click();
});

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