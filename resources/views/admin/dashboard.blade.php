@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Bảng điều khiển hệ thống</h2>
    <p class="text-sm text-gray-500">Xin chào, <strong>{{ Auth::user()->name }}</strong>. Chúc bạn một ngày làm việc hiệu quả!</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
        <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Tổng chi nhánh</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totalBranches }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
        <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Tổng thành viên</p>
        <p class="text-3xl font-bold text-gray-800">{{ $totalUsers }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-pink-500">
        <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Trạng thái hệ thống</p>
        <div class="flex items-center mt-1">
            <span class="relative flex h-3 w-3 mr-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            </span>
            <p class="text-sm font-bold text-green-500 uppercase">Hoạt động</p>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-700 text-lg flex items-center">
            <i class="fa-solid fa-shop mr-2 text-pink-500"></i> Danh sách AEON Mall hiện có
        </h3>
        
        {{-- CHỈ ADMIN TỔNG (ROLE 1) MỚI THẤY NÚT THÊM MỚI --}}
        @if(Auth::user()->role == 1)
        <a href="{{ route('admin.branches.create') }}" class="bg-slate-800 text-white px-4 py-2 rounded-md text-sm hover:bg-black transition flex items-center">
            <i class="fa-solid fa-plus mr-1 text-xs"></i> Thêm mới
        </a>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Tên chi nhánh</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Thành phố</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Hành động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($branches as $branch)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-800">{{ $branch->name }}</div>
                        <div class="text-[10px] text-gray-400 uppercase tracking-tighter">AEON Mall Vietnam</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-xs font-semibold">
                            {{ $branch->city->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center space-x-3">
                        {{-- AI CŨNG CÓ THỂ XEM (XEM CHI TIẾT CHƯA PHÂN QUYỀN) --}}
                        <a href="#" class="text-slate-400 hover:text-blue-500 transition" title="Xem chi tiết">
                            <i class="fa-solid fa-eye"></i>
                        </a>

                        {{-- CHỈ ADMIN TỔNG (ROLE 1) MỚI CÓ QUYỀN SỬA/XÓA --}}
                        @if(Auth::user()->role == 1)
                            <a href="{{ route('admin.branches.edit', $branch->id) }}" class="text-slate-400 hover:text-amber-500 transition" title="Sửa chi nhánh">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            
                            <form action="{{ route('admin.branches.destroy', $branch->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-red-500 transition" 
                                        onclick="return confirm('Xác nhận xóa chi nhánh {{ $branch->name }}?')" title="Xóa chi nhánh">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        @else
                            <span class="text-[10px] text-gray-300 italic">Chỉ đọc</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if($branches->isEmpty())
    <div class="p-8 text-center text-gray-400">
        <i class="fa-solid fa-folder-open text-4xl mb-2"></i>
        <p>Chưa có chi nhánh nào trong hệ thống.</p>
    </div>
    @endif
</div>
@endsection