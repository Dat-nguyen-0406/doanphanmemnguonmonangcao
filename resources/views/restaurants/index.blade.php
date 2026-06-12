@extends('layouts.shop')

@section('title', 'Khu Ẩm Thực AEON')

@section('content')
<div style="max-width:1280px;margin:0 auto;padding:32px 16px;">

    {{-- HERO BANNER --}}
    <div style="background:linear-gradient(135deg,#e50050 0%,#a0003a 100%);border-radius:20px;padding:40px 32px;margin-bottom:32px;color:#fff;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
        <div>
            <p style="font-size:13px;font-weight:700;letter-spacing:2px;opacity:.75;text-transform:uppercase;margin:0 0 8px;">AEON Mall</p>
            <h1 style="margin:0 0 10px;font-size:2rem;font-weight:900;line-height:1.2;">🍽 Khu Ẩm Thực AEON</h1>
            <p style="margin:0;opacity:.85;font-size:15px;">Đặt bàn trực tuyến — nhanh chóng, tiện lợi, không chờ đợi</p>
        </div>
        <div style="display:flex;gap:24px;flex-wrap:wrap;">
            <div style="text-align:center;">
                <div style="font-size:2rem;font-weight:900;">{{ $restaurants->count() }}</div>
                <div style="font-size:12px;opacity:.75;">Nhà hàng</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:2rem;font-weight:900;">{{ $branches->count() }}</div>
                <div style="font-size:12px;opacity:.75;">Chi nhánh</div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div style="background:#fff;border-radius:14px;padding:20px 24px;margin-bottom:28px;box-shadow:0 2px 12px rgba(0,0,0,.06);">
        <form action="{{ route('restaurants.index') }}" method="GET"
              style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#888;text-transform:uppercase;margin-bottom:6px;">📍 Chi nhánh</label>
                <select name="branch_id" style="width:100%;padding:10px 14px;border:2px solid #f0f0f0;border-radius:10px;font-size:14px;background:#fff;outline:none;cursor:pointer;">
                    <option value="">Tất cả chi nhánh</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if($cuisineTypes->isNotEmpty())
            <div style="flex:1;min-width:180px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#888;text-transform:uppercase;margin-bottom:6px;">🍜 Loại hình</label>
                <select name="cuisine_type" style="width:100%;padding:10px 14px;border:2px solid #f0f0f0;border-radius:10px;font-size:14px;background:#fff;outline:none;cursor:pointer;">
                    <option value="">Tất cả</option>
                    @foreach($cuisineTypes as $type)
                        <option value="{{ $type }}" {{ request('cuisine_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <button type="submit"
                    style="background:#e50050;color:#fff;border:none;padding:11px 28px;border-radius:10px;font-weight:700;font-size:14px;cursor:pointer;white-space:nowrap;">
                🔍 Tìm kiếm
            </button>
            @if(request()->hasAny(['branch_id','cuisine_type']))
            <a href="{{ route('restaurants.index') }}"
               style="background:#f5f5f5;color:#666;padding:11px 18px;border-radius:10px;font-weight:700;font-size:14px;text-decoration:none;white-space:nowrap;">
                ✕ Xoá bộ lọc
            </a>
            @endif
        </form>
    </div>

    {{-- FLASH MESSAGE --}}
    @if(session('error'))
    <div style="background:#fee2e2;border:1px solid #fca5a5;color:#b91c1c;padding:14px 18px;border-radius:10px;margin-bottom:20px;">
        ⚠️ {{ session('error') }}
    </div>
    @endif

    {{-- GRID KẾT QUẢ --}}
    @if(request()->hasAny(['branch_id','cuisine_type']))
    <p style="font-size:14px;color:#888;margin-bottom:16px;">
        Tìm thấy <strong style="color:#1a1a1a;">{{ $restaurants->count() }}</strong> nhà hàng
    </p>
    @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:22px;">
        @forelse($restaurants as $restaurant)
        <div class="restaurant-card"
             style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.07);border:1px solid #f0f0f0;transition:all .25s;display:flex;flex-direction:column;">

            {{-- ẢNH --}}
            <div style="position:relative;overflow:hidden;">
                <img src="{{ $restaurant->image_url ?? asset('images/aeon-logo.png') }}"
                     alt="{{ $restaurant->name }}"
                     style="width:100%;height:200px;object-fit:cover;transition:transform .4s;"
                     class="restaurant-img">
                {{-- BADGE --}}
                <div style="position:absolute;top:12px;left:12px;">
                    <span style="background:rgba(0,0,0,.6);backdrop-filter:blur(4px);color:#fff;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;">
                        {{ $restaurant->cuisine_type ?? 'Ẩm thực' }}
                    </span>
                </div>
                <div style="position:absolute;top:12px;right:12px;">
                    <span style="background:#fff;color:#e50050;font-size:11px;font-weight:800;padding:4px 10px;border-radius:20px;box-shadow:0 2px 8px rgba(0,0,0,.15);">
                        🪑 {{ $restaurant->tables_count ?? 0 }} bàn
                    </span>
                </div>
            </div>

            {{-- CONTENT --}}
            <div style="padding:18px;flex:1;display:flex;flex-direction:column;">
                <h2 style="margin:0 0 6px;font-size:1.1rem;font-weight:800;color:#1a1a1a;">{{ $restaurant->name }}</h2>
                <p style="margin:0 0 8px;font-size:13px;color:#888;display:flex;align-items:center;gap:4px;">
                    📍 {{ $restaurant->branch->name ?? 'AEON Mall' }}
                </p>
                @if($restaurant->description)
                <p style="margin:0 0 16px;font-size:13px;color:#666;line-height:1.5;flex:1;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $restaurant->description }}
                </p>
                @else
                <div style="flex:1;"></div>
                @endif

                <a href="{{ route('restaurants.book', $restaurant->id) }}"
                   style="display:block;text-align:center;background:#e50050;color:#fff;padding:12px;border-radius:10px;font-weight:800;font-size:14px;text-decoration:none;transition:background .2s;"
                   class="book-btn">
                    Đặt Bàn Ngay →
                </a>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:60px 20px;">
            <div style="font-size:60px;margin-bottom:16px;">🍽</div>
            <h3 style="font-size:1.2rem;font-weight:800;color:#1a1a1a;margin:0 0 8px;">Không tìm thấy nhà hàng</h3>
            <p style="color:#888;font-size:14px;margin:0 0 20px;">Thử thay đổi bộ lọc hoặc xem tất cả chi nhánh.</p>
            <a href="{{ route('restaurants.index') }}"
               style="display:inline-block;background:#e50050;color:#fff;padding:11px 24px;border-radius:10px;font-weight:700;font-size:14px;text-decoration:none;">
                Xem tất cả nhà hàng
            </a>
        </div>
        @endforelse
    </div>
</div>

<style>
.restaurant-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.12) !important; }
.restaurant-card:hover .restaurant-img { transform: scale(1.05); }
.book-btn:hover { background: #c0003c !important; }
</style>
@endsection
