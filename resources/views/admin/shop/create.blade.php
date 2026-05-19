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
                <i class="fa-solid fa-cart-plus mr-2 text-pink-500"></i> Đăng sản phẩm mới
            </h3>
        </div>

        <form action="{{ route('admin.shop.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tên sản phẩm</label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all" placeholder="Ví dụ: Giày Sneaker Nike Air Max">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Giá bán (VNĐ)</label>
                    <input type="number" name="price" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all" placeholder="0">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Số lượng trong kho</label>
                    <input type="number" name="stock" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all" placeholder="0">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Danh mục nhóm hàng</label>
                    <select name="category_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Bán tại chi nhánh</label>
                    <select name="branch_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all">
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Mô tả sản phẩm</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all" placeholder="Thông tin chi tiết về sản phẩm..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Hình ảnh sản phẩm</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-all">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fa-solid fa-cloud-arrow-up text-gray-400 text-2xl mb-2"></i>
                            <p class="text-xs text-gray-500 uppercase font-bold">Nhấn để tải ảnh lên</p>
                        </div>
                        {{-- Tìm đến dòng input file của bạn và sửa thành --}}
                        <input type="file" name="image" class="hidden" id="imageInput" onchange="previewImage(this)" />
                        <script>
                        function previewImage(input) {
                            if (input.files && input.files[0]) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    // Thay đổi icon đám mây thành ảnh vừa chọn
                                    const container = input.closest('label').querySelector('div');
                                    container.innerHTML = `<img src="${e.target.result}" class="h-24 w-full object-contain">`;
                                }
                                reader.readAsDataURL(input.files[0]);
                            }
                        }
                    </script>
                    </label>
                </div>
            </div>
            @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-50">
                <button type="submit" class="w-full md:w-auto px-8 py-3 rounded-xl bg-slate-900 text-white font-bold text-sm hover:bg-pink-600 shadow-lg shadow-slate-200 transition-all">
                    XÁC NHẬN ĐĂNG BÁN
                </button>
            </div>
        </form>
    </div>
</div>
@endsection