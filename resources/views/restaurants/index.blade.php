@extends('layouts.app') 

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-center text-red-600" style="color: #e50050;">Khám Phá Khu Ẩm Thực AEON</h1>

    <div class="mb-8 flex justify-center">
        <form action="{{ route('restaurants.index') }}" method="GET" class="w-full max-w-lg flex gap-2" style="display: flex; gap: 10px; max-width: 600px; margin: 0 auto;">
            <select name="branch_id" class="form-select block w-full px-4 py-2 border rounded-lg" style="flex: 1; padding: 10px; border-radius: 5px; border: 1px solid #ccc;">
                <option value="">-- Chọn chi nhánh AEON để xem nhà hàng --</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-lg" style="background-color: #e50050; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
                Tìm kiếm
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 30px;">
        @forelse($restaurants as $restaurant)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow border" style="border: 1px solid #eee; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <img src="{{ $restaurant->image_url ?? asset('images/aeon-logo.png') }}" alt="{{ $restaurant->name }}" class="w-full h-48 object-cover" style="width: 100%; height: 200px; object-fit: cover;">
                
                <div class="p-4" style="padding: 15px;">
                    <h2 class="text-xl font-bold mb-2" style="margin-top: 0; font-size: 1.25rem;">{{ $restaurant->name }}</h2>
                    <p class="text-sm text-gray-600 mb-2" style="color: #666; margin-bottom: 5px;">
                        <strong>Chi nhánh:</strong> {{ $restaurant->branch->name ?? 'AEON Mall' }}
                    </p>
                    <p class="text-sm text-gray-600 mb-2" style="color: #666; margin-bottom: 10px;">
                        <strong>Loại hình:</strong> {{ $restaurant->cuisine_type ?? 'Đa dạng' }}
                    </p>
                    <p class="text-sm text-gray-500 mb-4 line-clamp-2" style="color: #888; margin-bottom: 20px; height: 40px; overflow: hidden;">
                        {{ $restaurant->description }}
                    </p>

                    <a href="{{ url('/restaurants/' . $restaurant->id . '/book') }}" class="block w-full text-center bg-red-500 text-white py-2 rounded font-semibold hover:bg-red-600 transition-colors" style="display: block; text-align: center; background-color: #e50050; color: white; padding: 10px; text-decoration: none; border-radius: 5px;">
                        Đặt Bàn Ngay
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-8 text-gray-500" style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #888;">
                Không tìm thấy nhà hàng nào phù hợp ở chi nhánh này.
            </div>
        @endforelse
    </div>
</div>
@endsection