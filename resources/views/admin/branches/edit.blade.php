@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-50 bg-slate-50/50">
            <h3 class="text-lg font-bold text-gray-800">
                <i class="fa-solid fa-pen-to-square mr-2 text-pink-500"></i> Chỉnh sửa chi nhánh
            </h3>
        </div>

            <form action="{{ route('admin.branches.update', $branch->id) }}" method="POST" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tên chi nhánh</label>
                    <input type="text" name="name" value="{{ $branch->name }}" 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Thuộc Thành phố</label>
                    <select name="city_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all">
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ $branch->city_id == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Địa chỉ chi tiết</label>
                    <input type="text" name="address" value="{{ $branch->address }}" 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all"
                        placeholder="Số 27 Cổ Linh, Long Biên, Hà Nội...">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Link Google Maps (Tùy chọn)</label>
                    <input type="text" name="map_link" value="{{ $branch->map_link }}" 
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all"
                        placeholder="https://goo.gl/maps/...">
                </div>

                <div class="p-4 bg-pink-50 rounded-xl flex items-center space-x-4">
                    <img src="{{ asset('images/aeon-logo.png') }}" class="w-12 h-12 object-contain bg-white p-1 rounded-lg shadow-sm">
                    <p class="text-xs text-pink-600 font-medium italic">
                        Hệ thống đang sử dụng Logo AEON mặc định (.png) cho chi nhánh này.
                    </p>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4">
                    <a href="{{ route('admin.dashboard') }}" class="px-6 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-bold text-sm hover:bg-gray-50 transition-all">
                        HỦY BỎ
                    </a>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-sm hover:bg-pink-600 shadow-lg shadow-slate-200 transition-all">
                        LƯU THAY ĐỔI
                    </button>
                </div>
            </form>
    </div>
</div>
@endsection