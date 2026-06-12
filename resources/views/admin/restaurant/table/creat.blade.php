@extends('layouts.admin')
@section('content')
<div class="max-w-xl mx-auto">
    <a href="{{ route('admin.restaurant.tables', $restaurant->id) }}" class="text-sm text-pink-600 hover:underline block mb-4">← Quản lý bàn</a>
    <div class="bg-white rounded-2xl shadow-sm p-8">
        <h1 class="text-xl font-black text-gray-800 mb-6">+ Thêm bàn mới — {{ $restaurant->name }}</h1>
        <form action="{{ route('admin.restaurant.tables.store', $restaurant->id) }}" method="POST">
            @csrf
            <div class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Số bàn *</label>
                        <input type="text" name="table_number" value="{{ old('table_number') }}" required placeholder="VD: 1, A1..."
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nhãn hiển thị</label>
                        <input type="text" name="label" value="{{ old('label') }}" placeholder="VD: Bàn VIP 1..."
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Sức chứa (người) *</label>
                        <input type="number" name="capacity" value="{{ old('capacity', 4) }}" min="1" max="50" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tầng *</label>
                        <input type="number" name="floor" value="{{ old('floor', 1) }}" min="1" max="10" required
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Hình dạng</label>
                        <select name="shape" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm bg-white focus:border-pink-500 outline-none">
                            <option value="square">⬜ Vuông</option>
                            <option value="round">🔵 Tròn</option>
                            <option value="long">⬛ Dài</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Trạng thái</label>
                        <select name="is_active" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm bg-white focus:border-pink-500 outline-none">
                            <option value="1">● Hoạt động</option>
                            <option value="0">● Tạm đóng</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Vị trí X (cột sơ đồ)</label>
                        <input type="number" name="position_x" value="{{ old('position_x', 0) }}" min="0"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Vị trí Y (hàng sơ đồ)</label>
                        <input type="number" name="position_y" value="{{ old('position_y', 0) }}" min="0"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:border-pink-500 outline-none">
                    </div>
                </div>
                <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-bold py-3.5 rounded-xl text-sm">
                    Thêm bàn
                </button>
            </div>
        </form>
    </div>
</div>
@endsection