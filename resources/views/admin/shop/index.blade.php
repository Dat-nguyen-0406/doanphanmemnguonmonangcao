@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Kho hàng Online</h2>
            <p class="text-sm text-gray-500">
                @if(Auth::user()->role == 1)
                    Chế độ giám sát: Xem danh sách sản phẩm toàn hệ thống
                @else
                    Quản lý danh sách sản phẩm và tồn kho của bạn
                @endif
            </p>
        </div>

        {{-- 1. ẨN NÚT THÊM SẢN PHẨM VỚI ADMIN TỔNG --}}
        @if(Auth::user()->role != 1)
        <a href="{{ route('admin.shop.create') }}" class="bg-slate-900 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-pink-600 transition-all shadow-lg flex items-center justify-center">
            <i class="fa-solid fa-plus mr-2"></i> THÊM SẢN PHẨM
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 relative">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Danh mục</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Giá bán</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Kho</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($products as $product)
                    <tr class="hover:bg-slate-50/50 transition-all">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0 border border-gray-100">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fa-solid fa-image text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-800">{{ $product->name }}</div>
                                    <div class="text-[10px] text-pink-500 font-medium uppercase tracking-tighter italic">
                                        <i class="fa-solid fa-location-dot mr-1"></i>{{ $product->branch->name }}
                                    </div>
                                    {{-- Hiển thị chủ sở hữu nếu là Admin tổng --}}
                                    @if(Auth::user()->role == 1)
                                        <div class="text-[9px] text-blue-500 font-bold uppercase">
                                            Sở hữu: {{ $product->user->name ?? 'N/A' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold">
                                {{ $product->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-black text-gray-700">
                            {{ number_format($product->price, 0, ',', '.') }}đ
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm {{ $product->stock <= 5 ? 'text-red-500 font-bold' : 'text-gray-600' }}">
                                {{ $product->stock }} cái
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            {{-- 2. ĐIỀU KIỆN HIỂN THỊ NÚT THAO TÁC --}}
                            @if(Auth::user()->role != 1)
                                <a href="{{ route('admin.shop.edit', $product->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white transition-all">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('admin.shop.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            @else
                                {{-- Admin tổng chỉ thấy trạng thái hoặc icon xem --}}
                                <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded">CHỈ XEM</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">Chưa có sản phẩm nào trong kho.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection