@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Quản lý Phim</h2>
            <p class="text-sm text-gray-500 mt-1">Danh sách tất cả các bộ phim</p>
        </div>

        {{-- Phần nút bấm Header --}}
        @if(Auth::user()->role != 1)
            <a href="{{ route('admin.movies.create') }}" class="bg-slate-800 text-white px-4 py-2 rounded-md text-sm hover:bg-black transition flex items-center">
                <i class="fa-solid fa-plus mr-1 text-xs"></i> Thêm Phim Mới
            </a>
        @else
            <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-2 rounded-md text-sm flex items-center">
                <i class="fa-solid fa-eye mr-2"></i> Chế độ xem hệ thống
            </div>
        @endif
    </div>
</div>

@if ($message = Session::get('success'))
<div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center" role="alert">
    <i class="fa-solid fa-check-circle mr-2"></i>
    <span>{{ $message }}</span>
</div>
@endif

{{-- Hiển thị lỗi nếu Admin cố tình thao tác --}}
@if ($error = Session::get('error'))
<div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center" role="alert">
    <i class="fa-solid fa-circle-exclamation mr-2"></i>
    <span>{{ $error }}</span>
</div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden border border-gray-100">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Tên Phim</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Thể Loại</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Thời Lượng</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Ngày Phát Hành</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Suất Chiếu</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-center">Hành Động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($movies as $movie)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @php
                                $posterUrl = \Illuminate\Support\Str::startsWith($movie->poster, ['http://', 'https://']) 
                                    ? $movie->poster 
                                    : asset('storage/' . $movie->poster);
                            @endphp
                            
                            <img src="{{ $posterUrl }}" 
                                 alt="{{ $movie->title }}" 
                                 class="w-10 h-14 object-cover rounded mr-3 shadow-sm"
                                 onerror="this.src='https://placehold.co/400x600?text=No+Poster'">
                            
                            <div>
                                <div class="font-bold text-gray-800">{{ $movie->title }}</div>
                                <div class="text-[10px] text-gray-400 mt-1 max-w-xs truncate">{{ $movie->description }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-purple-50 text-purple-700 px-2 py-1 rounded text-xs font-semibold">{{ $movie->genre ?? 'N/A' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-700 font-semibold">{{ $movie->duration }} phút</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-600">{{ \Carbon\Carbon::parse($movie->release_date)->format('d/m/Y') }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-semibold">{{ $movie->showtimes_count }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if(Auth::user()->role != 1)
                            {{-- Partner: Hiện Sửa và Xóa --}}
                            <div class="flex justify-center space-x-3">
                                <a href="{{ route('admin.movies.edit', $movie->id) }}" class="text-slate-400 hover:text-amber-500 transition" title="Sửa phim">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                
                                <form action="{{ route('admin.movies.destroy', $movie->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-400 hover:text-red-500 transition" 
                                            onclick="return confirm('Xác nhận xóa phim {{ $movie->title }}?')" title="Xóa phim">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        @else
                            {{-- Admin tổng: Hiện nút Chỉ xem giống các trang khác --}}
                            <a href="{{ route('admin.movies.edit', $movie->id) }}" class="text-blue-500 hover:text-blue-700 font-medium text-sm flex items-center justify-center">
                                <i class="fa-solid fa-eye mr-1"></i> Chỉ xem
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                        <i class="fa-solid fa-film text-4xl mb-2 block"></i>
                        <p>Chưa có phim nào trong hệ thống.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6">
    {{ $movies->links() }}
</div>
@endsection