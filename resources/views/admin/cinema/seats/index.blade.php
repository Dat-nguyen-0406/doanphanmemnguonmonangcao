@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Quản lý Ghế</h2>
            <p class="text-sm text-gray-500 mt-1">Danh sách tất cả các ghế</p>
        </div>
        
        <div class="space-x-2 flex items-center">
            {{-- Kiểm tra quyền để hiện nút tạo --}}
            @if(Auth::user()->role != 1)
                <a href="{{ route('admin.seats.create') }}" class="bg-slate-800 text-white px-4 py-2 rounded-md text-sm hover:bg-black transition inline-flex items-center">
                    <i class="fa-solid fa-plus mr-1 text-xs"></i> Thêm Ghế
                </a>
                <button onclick="openBulkModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 transition inline-flex items-center">
                    <i class="fa-solid fa-layer-group mr-1 text-xs"></i> Tạo Hàng Loạt
                </button>
            @else
                <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-2 rounded-md text-sm flex items-center shadow-sm">
                    <i class="fa-solid fa-eye mr-2"></i> Chế độ xem hệ thống
                </div>
            @endif
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center" role="alert">
    <i class="fa-solid fa-check-circle mr-2"></i>
    <span>{{ $message }}</span>
</div>
@endif

@if ($error = Session::get('error'))
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center" role="alert">
    <i class="fa-solid fa-triangle-exclamation mr-2"></i>
    <span>{{ $error }}</span>
</div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Chi Nhánh</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Ghế</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Loại</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Hành Động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($seats as $seat)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <span class="font-semibold text-gray-800">{{ $seat->branch->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-700 font-mono">{{ $seat->row }}{{ $seat->seat_number }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($seat->type === 'vip')
                            <span class="bg-purple-50 text-purple-700 px-2 py-1 rounded text-xs font-semibold">VIP</span>
                        @else
                            <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-semibold">Thường</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if(Auth::user()->role != 1)
                            <div class="flex justify-center space-x-3">
                                <a href="{{ route('admin.seats.edit', $seat->id) }}" class="text-slate-400 hover:text-amber-500 transition" title="Sửa ghế">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                
                                <form action="{{ route('admin.seats.destroy', $seat->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition" 
                                            onclick="return confirm('Xác nhận xóa ghế {{ $seat->row }}{{ $seat->seat_number }}?')" title="Xóa ghế">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('admin.seats.edit', $seat->id) }}" class="text-blue-500 hover:text-blue-700 font-medium text-sm flex items-center justify-center">
                                <i class="fa-solid fa-eye mr-1"></i> Chỉ xem
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                        <i class="fa-solid fa-chair text-4xl mb-2 block"></i>
                        <p>Chưa có ghế nào trong hệ thống.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $seats->links() }}
</div>

{{-- Chỉ render Modal nếu là Partner --}}
@if(Auth::user()->role != 1)
<div id="bulkModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">Tạo Ghế Hàng Loạt</h3>
        </div>
        
        <form action="{{ route('admin.seats.bulk-create') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label for="bulk_branch_id" class="block text-sm font-bold text-gray-700 mb-2">Chi Nhánh *</label>
                <select id="bulk_branch_id" name="branch_id" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                    <option value="">-- Chọn Chi Nhánh --</option>
                    @foreach(\App\Models\Branch::all() as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="rows" class="block text-sm font-bold text-gray-700 mb-2">Các Hàng Ghế *</label>
                <input type="text" id="rows" name="rows" placeholder="Vd: A,B,C,D,E" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                <p class="text-xs text-gray-500 mt-1">Nhập các hàng cách nhau bởi dấu phẩy</p>
            </div>

            <div>
                <label for="seats_per_row" class="block text-sm font-bold text-gray-700 mb-2">Số Ghế Trên Mỗi Hàng *</label>
                <input type="number" id="seats_per_row" name="seats_per_row" value="10" required min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
            </div>

            <div>
                <label for="bulk_type" class="block text-sm font-bold text-gray-700 mb-2">Loại Ghế *</label>
                <select id="bulk_type" name="type" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500">
                    <option value="">-- Chọn Loại --</option>
                    <option value="normal">Ghế Thường</option>
                    <option value="vip">Ghế VIP</option>
                </select>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded p-3">
                <p class="text-xs text-blue-800">
                    <i class="fa-solid fa-info-circle mr-1"></i>
                    Sẽ tạo tổng cộng <span id="seatCount" class="font-bold">0</span> ghế
                </p>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" onclick="closeBulkModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Hủy
                </button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                    Tạo
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection