@extends('layouts.shop')

@section('title', 'Thanh toán đặt bàn')

@section('content')
<div style="max-width:640px;margin:40px auto;padding:0 16px;">

    {{-- BREADCRUMB --}}
    <nav style="margin-bottom:20px;font-size:13px;color:#888;">
        <a href="{{ route('restaurants.index') }}" style="color:#e50050;text-decoration:none;">Nhà hàng</a>
        <span style="margin:0 8px;">›</span>
        <a href="{{ route('restaurants.book', $booking->restaurant_id) }}" style="color:#e50050;text-decoration:none;">{{ $booking->restaurant->name }}</a>
        <span style="margin:0 8px;">›</span>
        <span>Thanh toán</span>
    </nav>

    {{-- HEADER --}}
    <div style="text-align:center;margin-bottom:24px;">
        <div style="width:64px;height:64px;background:#fff3f5;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:28px;margin-bottom:12px;">💳</div>
        <h1 style="margin:0 0 6px;font-size:1.5rem;font-weight:900;color:#1a1a1a;">Xác nhận & Thanh toán</h1>
        <p style="margin:0;color:#888;font-size:14px;">Hoàn tất thanh toán để giữ bàn của bạn</p>
    </div>

    {{-- THÔNG TIN ĐẶT BÀN --}}
    <div style="background:#fff;border-radius:16px;box-shadow:0 2px 16px rgba(0,0,0,.07);overflow:hidden;margin-bottom:16px;">
        <div style="background:linear-gradient(135deg,#e50050,#c0003c);color:#fff;padding:16px 20px;display:flex;align-items:center;gap:12px;">
            <img src="{{ $booking->restaurant->image_url ?? asset('images/aeon-logo.png') }}"
                 style="width:48px;height:48px;border-radius:10px;object-fit:cover;border:2px solid rgba(255,255,255,.3);">
            <div>
                <h3 style="margin:0;font-size:1rem;font-weight:800;">{{ $booking->restaurant->name }}</h3>
                <p style="margin:2px 0 0;font-size:12px;opacity:.8;">📍 {{ $booking->restaurant->branch->name ?? 'AEON Mall' }}</p>
            </div>
        </div>

        <div style="padding:20px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                <div style="background:#f9fafb;border-radius:10px;padding:14px;">
                    <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;">📅 Ngày đến</p>
                    <p style="margin:0;font-weight:800;color:#1a1a1a;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</p>
                </div>
                <div style="background:#f9fafb;border-radius:10px;padding:14px;">
                    <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;">🕐 Giờ đến</p>
                    <p style="margin:0;font-weight:800;color:#1a1a1a;">{{ $booking->booking_time }}</p>
                </div>
                <div style="background:#f9fafb;border-radius:10px;padding:14px;">
                    <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;">👥 Số khách</p>
                    <p style="margin:0;font-weight:800;color:#1a1a1a;">{{ $booking->guests_count }} người</p>
                </div>
                <div style="background:#f9fafb;border-radius:10px;padding:14px;">
                    <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;">🪑 Bàn</p>
                    <p style="margin:0;font-weight:800;color:#1a1a1a;">
                        {{ $booking->table->label ?? ('Bàn ' . ($booking->table->table_number ?? 'N/A')) }}
                    </p>
                </div>
            </div>

            {{-- Mã giao dịch --}}
            <div style="background:#f0f9ff;border-radius:10px;padding:12px 16px;margin-bottom:16px;">
                <p style="margin:0;font-size:12px;color:#888;">Mã giao dịch</p>
                <p style="margin:4px 0 0;font-weight:700;color:#1a1a1a;font-size:13px;word-break:break-all;">{{ $booking->transaction_id }}</p>
            </div>

            {{-- Pre-order items --}}
            @if($booking->items && $booking->items->isNotEmpty())
            <div style="margin-bottom:16px;">
                <h4 style="margin:0 0 10px;font-size:13px;font-weight:700;color:#444;">🛒 Món đặt trước</h4>
                @foreach($booking->items as $item)
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:6px 0;border-bottom:1px dashed #f0f0f0;">
                    <span>{{ $item->menuItem->name ?? 'Món ăn' }} × {{ $item->quantity }}</span>
                    <span style="font-weight:700;">{{ number_format($item->unit_price * $item->quantity) }}đ</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- TỔNG CHI PHÍ --}}
            <div style="border-top:2px solid #f0f0f0;padding-top:14px;">
                <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px;">
                    <span style="color:#666;">Phí cọc giữ bàn</span>
                    <span style="font-weight:700;">{{ number_format($booking->deposit_amount) }}đ</span>
                </div>
                @if($booking->pre_order_amount > 0)
                <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px;">
                    <span style="color:#666;">Tiền đặt món trước</span>
                    <span style="font-weight:700;">{{ number_format($booking->pre_order_amount) }}đ</span>
                </div>
                @endif
                <div style="display:flex;justify-content:space-between;font-size:18px;font-weight:900;margin-top:8px;">
                    <span>Tổng thanh toán</span>
                    <span style="color:#e50050;">{{ number_format($booking->deposit_amount + ($booking->pre_order_amount ?? 0)) }}đ</span>
                </div>
            </div>
        </div>
    </div>

    {{-- NÚT THANH TOÁN --}}
    <div style="background:#fff;border-radius:16px;box-shadow:0 2px 16px rgba(0,0,0,.07);padding:20px;margin-bottom:16px;">
        <h3 style="margin:0 0 16px;font-size:14px;font-weight:700;color:#444;">Chọn phương thức thanh toán</h3>

        <form action="{{ route('booking.vnpay.process', $booking->id) }}" method="POST">
            @csrf
            <button type="submit"
                    style="width:100%;display:flex;align-items:center;justify-content:center;gap:12px;background:#005baa;color:#fff;border:none;padding:16px;border-radius:12px;font-size:16px;font-weight:800;cursor:pointer;margin-bottom:12px;transition:background .2s;"
                    onmouseover="this.style.background='#004a8f'" onmouseout="this.style.background='#005baa'">
                <span style="font-size:20px;">💳</span> Thanh toán qua VNPAY
            </button>
        </form>

        <p style="margin:12px 0 0;font-size:12px;color:#aaa;text-align:center;">
            🔒 Thanh toán an toàn qua cổng VNPAY. Thông tin của bạn được bảo mật tuyệt đối.
        </p>
    </div>

    <div style="text-align:center;">
        <a href="{{ route('restaurants.book', $booking->restaurant_id) }}"
           style="color:#e50050;font-size:13px;text-decoration:none;">← Quay lại chỉnh sửa đặt bàn</a>
    </div>
</div>
@endsection
