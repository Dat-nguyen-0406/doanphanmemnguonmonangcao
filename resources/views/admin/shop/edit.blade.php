@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.shop.index') }}" class="text-gray-500 hover:text-pink-600 text-sm font-bold flex items-center transition-all">
            <i class="fa-solid fa-arrow-left mr-2"></i> QUAY LẠI DANH SÁCH
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 bg-slate-50/50">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fa-solid fa-pen-to-square mr-2 text-blue-500"></i> Chỉnh sửa sản phẩm
            </h3>
        </div>

        {{-- Lưu ý: Phải có @method('PUT') cho route update --}}
        <form action="{{ route('admin.shop.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tên sản phẩm</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Giá bán (VNĐ)</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" required 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Số lượng trong kho</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required 
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Danh mục</label>
                    <select name="category_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Chi nhánh</label>
                    <select name="branch_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $product->branch_id == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Mô tả sản phẩm</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all">{{ old('description', $product->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Hình ảnh sản phẩm (Để trống nếu không đổi)</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all overflow-hidden">
                        <div id="preview-container" class="flex flex-col items-center justify-center pt-5 pb-6">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" class="h-24 w-full object-contain mb-2">
                                <p class="text-xs text-blue-500 font-bold">NHẤN ĐỂ THAY ĐỔI ẢNH</p>
                            @else
                                <i class="fa-solid fa-cloud-arrow-up text-gray-400 text-2xl mb-2"></i>
                                <p class="text-xs text-gray-500 uppercase font-bold">Nhấn để tải ảnh lên</p>
                            @endif
                        </div>
                        <input type="file" name="image" class="hidden" id="imageInput" onchange="previewImage(this)" />
                    </label>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-50">
                <button type="submit" class="w-full md:w-auto px-8 py-3 rounded-xl bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all">
                    LƯU THAY ĐỔI
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('preview-container');
            container.innerHTML = `<img src="${e.target.result}" class="h-24 w-full object-contain">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection