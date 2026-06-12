@extends('layouts.shop')

@section('title', 'Đặt bàn thành công')

@section('content')
<div style="max-width:680px;margin:40px auto;padding:0 16px;">

    {{-- SUCCESS HEADER --}}
    <div style="background:linear-gradient(135deg,#10b981,#059669);border-radius:20px;padding:36px 24px;text-align:center;color:#fff;margin-bottom:20px;">
        <div style="width:72px;height:72px;background:rgba(255,255,255,.2);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:36px;margin-bottom:16px;">✅</div>
        <h1 style="margin:0 0 8px;font-size:1.7rem;font-weight:900;">Đặt Bàn Thành Công!</h1>
        <p style="margin:0;opacity:.9;font-size:15px;">Nhà hàng đã nhận được yêu cầu đặt bàn của bạn</p>
    </div>

    {{-- BOOKING DETAIL --}}
    <div style="background:#fff;border-radius:16px;box-shadow:0 2px 16px rgba(0,0,0,.07);overflow:hidden;margin-bottom:16px;">
        <div style="padding:20px 20px 0;border-bottom:1px dashed #f0f0f0;margin-bottom:0;">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:20px;">
                <img src="{{ $booking->restaurant->image_url ?? asset('images/aeon-logo.png') }}"
                     style="width:56px;height:56px;border-radius:12px;object-fit:cover;border:1px solid #eee;">
                <div>
                    <h2 style="margin:0 0 4px;font-size:1.1rem;font-weight:800;color:#1a1a1a;">{{ $booking->restaurant->name }}</h2>
                    <p style="margin:0;font-size:13px;color:#888;">📍 {{ $booking->restaurant->branch->name ?? 'AEON Mall' }}</p>
                </div>
                <div style="margin-left:auto;">
                    <span style="background:#d1fae5;color:#065f46;font-size:12px;font-weight:800;padding:6px 14px;border-radius:20px;">✅ Đã xác nhận</span>
                </div>
            </div>
        </div>

        <div style="padding:20px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px;">
                <div style="background:#f9fafb;border-radius:10px;padding:14px;">
                    <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;">📅 Ngày đến</p>
                    <p style="margin:0;font-weight:800;font-size:15px;color:#1a1a1a;">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</p>
                </div>
                <div style="background:#f9fafb;border-radius:10px;padding:14px;">
                    <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;">🕐 Giờ đến</p>
                    <p style="margin:0;font-weight:800;font-size:15px;color:#1a1a1a;">{{ $booking->booking_time }}</p>
                </div>
                <div style="background:#f9fafb;border-radius:10px;padding:14px;">
                    <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;">👥 Số khách</p>
                    <p style="margin:0;font-weight:800;font-size:15px;color:#1a1a1a;">{{ $booking->guests_count }} người</p>
                </div>
                <div style="background:#f9fafb;border-radius:10px;padding:14px;">
                    <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;">🪑 Bàn</p>
                    <p style="margin:0;font-weight:800;font-size:15px;color:#1a1a1a;">
                        {{ $booking->table->label ?? ('Bàn ' . ($booking->table->table_number ?? 'N/A')) }}
                    </p>
                </div>
            </div>

            {{-- PRE-ORDER --}}
            @if($booking->items && $booking->items->isNotEmpty())
            <div style="background:#fffbeb;border-radius:12px;padding:16px;margin-bottom:16px;">
                <h4 style="margin:0 0 12px;font-size:13px;font-weight:800;color:#92400e;">🛒 Món đặt trước</h4>
                @foreach($booking->items as $item)
                <div style="display:flex;justify-content:space-between;font-size:13px;padding:5px 0;">
                    <span style="color:#444;">{{ $item->menuItem->name ?? '—' }} × {{ $item->quantity }}</span>
                    <span style="font-weight:700;">{{ number_format($item->unit_price * $item->quantity) }}đ</span>
                </div>
                @endforeach
            </div>
            @endif

            {{-- NOTE --}}
            @if($booking->note)
            <div style="background:#f9fafb;border-radius:10px;padding:14px;margin-bottom:16px;">
                <p style="margin:0 0 4px;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;">📝 Ghi chú</p>
                <p style="margin:0;font-size:14px;color:#444;">{{ $booking->note }}</p>
            </div>
            @endif

            {{-- PAYMENT SUMMARY --}}
            <div style="border-top:2px solid #f0f0f0;padding-top:14px;">
                <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px;">
                    <span style="color:#666;">Đã thanh toán cọc</span>
                    <span style="font-weight:700;color:#10b981;">{{ number_format($booking->deposit_amount) }}đ ✅</span>
                </div>
                @if(($booking->pre_order_amount ?? 0) > 0)
                <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px;">
                    <span style="color:#666;">Thanh toán trước món ăn</span>
                    <span style="font-weight:700;color:#10b981;">{{ number_format($booking->pre_order_amount) }}đ ✅</span>
                </div>
                @endif
            </div>

            {{-- TRANSACTION ID --}}
            <div style="margin-top:14px;background:#f0f9ff;border-radius:8px;padding:10px 14px;display:flex;justify-content:space-between;align-items:center;">
                <span style="font-size:12px;color:#888;">Mã tham chiếu</span>
                <span style="font-size:12px;font-weight:700;color:#1a1a1a;">{{ $booking->transaction_id }}</span>
            </div>
        </div>
    </div>

    {{-- HƯỚNG DẪN --}}
    <div style="background:#fff3f5;border-radius:14px;padding:18px 20px;margin-bottom:20px;border-left:4px solid #e50050;">
        <h4 style="margin:0 0 10px;font-size:14px;font-weight:800;color:#e50050;">📌 Lưu ý quan trọng</h4>
        <ul style="margin:0;padding-left:18px;font-size:13px;color:#555;line-height:2;">
            <li>Vui lòng có mặt đúng giờ đặt bàn</li>
            <li>Tiền cọc sẽ được trừ vào hóa đơn khi thanh toán tại nhà hàng</li>
            <li>Liên hệ nhà hàng trước ít nhất 2 tiếng nếu muốn hủy để được hoàn cọc</li>
        </ul>
    </div>

    {{-- ACTIONS --}}
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('restaurants.index') }}"
           style="flex:1;min-width:160px;display:block;text-align:center;background:#e50050;color:#fff;padding:14px;border-radius:12px;font-weight:800;font-size:14px;text-decoration:none;">
            🏠 Về trang nhà hàng
        </a>
        <a href="{{ route('profile.index') }}"
           style="flex:1;min-width:160px;display:block;text-align:center;background:#f5f5f5;color:#444;padding:14px;border-radius:12px;font-weight:700;font-size:14px;text-decoration:none;">
            👤 Xem lịch sử đặt bàn
        </a>
    </div>
</div>
@endsection
