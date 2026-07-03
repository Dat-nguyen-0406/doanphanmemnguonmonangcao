@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <a href="{{ route('admin.restaurant.index') }}" class="text-sm text-pink-600 hover:underline">← Danh sách nhà hàng</a>
        <h1 class="text-2xl font-black text-gray-800 mt-1">🍜 Menu — {{ $restaurant->name }}</h1>
        <p class="text-sm text-gray-500 mt-1">Quản lý thực đơn để khách đặt món trước</p>
    </div>
</div>

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 mb-5 flex items-center gap-2">
    ✅ {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- FORM THÊM MÓN --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h2 class="text-base font-black text-gray-700 mb-4">+ Thêm món mới</h2>
        <form action="{{ route('admin.restaurant.menu.store', $restaurant->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tên món *</label>
                <input type="text" name="name" required placeholder="Phở bò đặc biệt..."
                       class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Danh mục *</label>
                <select name="category" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm bg-white focus:border-pink-500 outline-none">
                    <option value="main">🍜 Món chính</option>
                    <option value="appetizer">🥗 Khai vị</option>
                    <option value="dessert">🍮 Tráng miệng</option>
                    <option value="drink">🥤 Đồ uống</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Giá (VNĐ) *</label>
                <input type="number" name="price" required min="0" step="1000" placeholder="85000"
                       class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mô tả</label>
                <textarea name="description" rows="2" placeholder="Mô tả ngắn về món ăn..."
                          class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none resize-none"></textarea>
            </div>

            {{-- ẢNH - File picker (kéo-thả, giống form chỉnh sửa nhà hàng) --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ảnh món ăn</label>

                <div id="image-drop-zone"
                     class="relative w-full border-2 border-dashed border-gray-300 rounded-xl
                            flex flex-col items-center justify-center gap-2
                            hover:border-pink-400 hover:bg-pink-50 transition-all"
                     style="min-height: 140px;">

                    <img id="image-preview" src="" alt="Preview" class="hidden w-full h-36 object-cover rounded-xl">

                    <div id="image-placeholder"
                         onclick="document.getElementById('image_file').click()"
                         class="flex flex-col items-center gap-2 py-5 cursor-pointer w-full">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-300"></i>
                        <p class="text-xs font-bold text-gray-400">Nhấn để chọn ảnh</p>
                        <p class="text-[10px] text-gray-300">JPG, PNG · Tối đa 5MB</p>
                    </div>

                    <button type="button" id="image-change-btn"
                            onclick="document.getElementById('image_file').click()"
                            class="hidden absolute bottom-2 right-2 bg-white/90 hover:bg-white text-gray-600 text-xs font-bold px-3 py-1.5 rounded-lg shadow border border-gray-200 transition">
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

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_available" id="is_available" checked class="w-4 h-4 accent-pink-600">
                <label for="is_available" class="text-sm text-gray-600">Đang phục vụ</label>
            </div>
            <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3 rounded-xl text-sm transition">
                + Thêm món
            </button>
        </form>
    </div>

    {{-- DANH SÁCH MÓN --}}
    <div class="lg:col-span-2">
        @php $categoryLabels = ['main' => '🍜 Món chính', 'appetizer' => '🥗 Khai vị', 'dessert' => '🍮 Tráng miệng', 'drink' => '🥤 Đồ uống']; @endphp

        @foreach($menuItems->groupBy('category') as $cat => $items)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-5">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-100">
                <h3 class="font-black text-gray-700 text-sm">{{ $categoryLabels[$cat] ?? $cat }}</h3>
            </div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-slate-50">
                    @foreach($items as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                @if($item->image_url)
                                    <img src="{{ \Illuminate\Support\Str::startsWith($item->image_url, ['http://', 'https://']) ? $item->image_url : asset('storage/' . $item->image_url) }}" style="width:40px;height:40px;border-radius:8px;object-fit:cover;">
                                @else
                                    <div style="width:40px;height:40px;border-radius:8px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;font-size:18px;">🍽</div>
                                @endif
                                <div>
                                    <p class="font-bold text-gray-800">{{ $item->name }}</p>
                                    @if($item->description)
                                    <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($item->description, 50) }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3 font-bold text-pink-600">{{ number_format($item->price) }}đ</td>
                        <td class="px-5 py-3 text-center">
                            @if($item->is_available)
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-0.5 rounded-full">● Đang có</span>
                            @else
                                <span class="bg-gray-100 text-gray-500 text-xs font-bold px-2 py-0.5 rounded-full">● Hết</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.restaurant.menu.edit', [$restaurant->id, $item->id]) }}"
                                   class="text-xs bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold px-3 py-1.5 rounded-lg transition">
                                    ✏️ Sửa
                                </a>
                                <form action="{{ route('admin.restaurant.menu.destroy', [$restaurant->id, $item->id]) }}" method="POST"
                                      onsubmit="return confirm('Xoá món này?')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs bg-red-50 text-red-500 hover:bg-red-500 hover:text-white font-bold px-3 py-1.5 rounded-lg transition">🗑</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach

        @if($menuItems->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center text-gray-400">
            <div class="text-5xl mb-4">🍽</div>
            <p class="font-bold text-gray-500">Chưa có món ăn nào. Thêm món đầu tiên!</p>
        </div>
        @endif
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