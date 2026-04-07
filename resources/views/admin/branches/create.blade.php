@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 hover:text-pink-600">
            <i class="fa-solid fa-arrow-left mr-1"></i> Quay lại Dashboard
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Thêm chi nhánh AEON Mall mới</h2>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <form action="{{ route('admin.branches.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tên chi nhánh</label>
                    <input type="text" name="name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-500 outline-none transition"
                        placeholder="Ví dụ: AEON Mall Long Biên">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Thành phố</label>
                    <select name="city_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-500 outline-none transition">
                        <option value="">-- Chọn thành phố --</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ chi tiết</label>
                <input type="text" name="address" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-500 outline-none transition"
                    placeholder="Số 27 đường Cổ Linh, P. Long Biên...">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Link bản đồ (Google Maps URL)</label>
                <input type="url" name="map_link"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-pink-500 outline-none transition"
                    placeholder="https://goo.gl/maps/...">
            </div>

            <div class="flex justify-end pt-4 border-t">
                <button type="submit" 
                    class="bg-pink-600 text-white px-8 py-3 rounded-md font-bold hover:bg-pink-700 shadow-lg transition transform hover:-translate-y-0.5">
                    LƯU CHI NHÁNH
                </button>
            </div>
        </form>
    </div>
</div>
@endsection