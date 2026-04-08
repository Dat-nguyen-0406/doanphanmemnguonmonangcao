@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Quản lý Thành phố</h2>

    <div class="bg-white p-6 rounded-lg shadow-sm mb-8 border border-gray-100">
        <form action="{{ route('admin.cities.store') }}" method="POST" class="flex gap-4">
            @csrf
            <div class="flex-1">
                <input type="text" name="name" required 
                    class="w-full px-4 py-2 border rounded-md outline-none focus:ring-2 focus:ring-pink-500" 
                    placeholder="Nhập tên thành phố mới (VD: Nam Định, Hải Phòng...)">
            </div>
            <button type="submit" class="bg-slate-800 text-white px-6 py-2 rounded-md font-bold hover:bg-black transition">
                THÊM MỚI
            </button>
        </form>
    </div>

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
                @foreach($cities as $city)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $city->name }}</td>
                    <td class="px-6 py-4 text-center text-sm text-gray-500">{{ $city->branches_count }}</td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.cities.destroy', $city->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Xóa thành phố này sẽ xóa toàn bộ chi nhánh thuộc về nó. Bạn chắc chứ?')" 
                                class="text-red-500 hover:text-red-700 font-bold text-xs">
                                <i class="fa-solid fa-trash mr-1"></i> XÓA
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection