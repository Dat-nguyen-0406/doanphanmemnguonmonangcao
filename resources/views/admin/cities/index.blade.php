@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Quản lý Thành phố</h2>

    {{-- 1. Box Thông báo Thành công (Chỉ hiện khi có session success) --}}
    @if(session('success'))
    <div x-data="{ show: true }" 
         x-show="show" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         class="mb-6">
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm flex justify-between items-center" role="alert">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-check mr-3 text-xl"></i>
                <div>
                    <p class="font-bold">Thành công!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
            <button @click="show = false" class="text-green-500 hover:text-green-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- 2. Box Thông báo Lỗi (Hiện khi có lỗi Validation hoặc lỗi hệ thống) --}}
    @if($errors->any() || session('error'))
    <div x-data="{ show: true }" 
         x-show="show"
         class="mb-6">
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-start space-x-3 shadow-sm">
            <i class="fa-solid fa-triangle-exclamation mt-1 text-red-500"></i>
            <div class="flex-1">
                <strong class="font-bold">Lỗi hệ thống!</strong>
                <ul class="text-sm list-disc list-inside mt-1">
                    @if(session('error')) <li>{{ session('error') }}</li> @endif
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button @click="show = false" class="text-red-400 hover:text-red-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
    </div>
    @endif

    {{-- 3. Form Thêm mới --}}
    <div class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-100">
        <form action="{{ route('admin.cities.store') }}" method="POST" class="flex gap-4">
            @csrf
            <div class="flex-1">
                <input type="text" name="name" required 
                    class="w-full px-4 py-2 border rounded-md outline-none focus:ring-2 focus:ring-pink-500 @error('name') border-red-500 @enderror" 
                    placeholder="Nhập tên thành phố mới (VD: Nam Định, Hải Phòng...)">
            </div>
            <button type="submit" class="bg-slate-800 text-white px-6 py-2 rounded-md font-bold hover:bg-black transition flex items-center">
                <i class="fa-solid fa-plus mr-2"></i> THÊM MỚI
            </button>
        </form>
    </div>

    {{-- 4. Bảng danh sách --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold">
                <tr>
                    <th class="px-6 py-4">Tên Thành Phố</th>
                    <th class="px-6 py-4 text-center">Số lượng Chi nhánh</th>
                    <th class="px-6 py-4 text-center">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($cities as $city)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $city->name }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-500">
                        <span class="bg-gray-100 px-2 py-1 rounded-full text-xs">{{ $city->branches_count }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.cities.destroy', $city->id) }}" method="POST" class="inline-block">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Xóa thành phố này sẽ xóa toàn bộ chi nhánh thuộc về nó. Bạn chắc chứ?')" 
                                class="text-red-500 hover:text-red-700 font-bold text-xs flex items-center mx-auto">
                                <i class="fa-solid fa-trash mr-1"></i> XÓA
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">Chưa có dữ liệu thành phố nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection