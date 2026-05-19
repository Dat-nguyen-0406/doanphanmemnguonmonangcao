@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-8">
                <h3 class="font-bold text-gray-800 mb-4 italic">Thêm danh mục mới</h3>
                <form action="{{ route('admin.category.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tên danh mục</label>
                        <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-pink-500/20 focus:border-pink-500 outline-none transition-all" placeholder="Ví dụ: Đồ gia dụng">
                    </div>
                    <button type="submit" class="w-full bg-slate-900 text-white py-3 rounded-xl font-bold text-sm hover:bg-pink-600 transition-all shadow-lg">
                        XÁC NHẬN LƯU
                    </button>
                </form>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-50 bg-slate-50/50">
                    <h3 class="font-bold text-gray-700 text-sm uppercase tracking-widest">Danh sách hiện có</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                <th class="px-6 py-4 text-center w-16">ID</th>
                                <th class="px-6 py-4">Tên danh mục</th>
                                <th class="px-6 py-4">Slug</th>
                                <th class="px-6 py-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($categories as $cat)
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="px-6 py-4 text-center text-sm text-gray-400">#{{ $cat->id }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-700">{{ $cat->name }}</td>
                                <td class="px-6 py-4 text-xs text-gray-400">{{ $cat->slug }}</td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.category.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Xóa danh mục sẽ xóa toàn bộ sản phẩm thuộc nhóm này?')">
                                        @csrf @method('DELETE')
                                        <button class="text-gray-300 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-10 text-center text-gray-400 italic">Chưa có dữ liệu</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection