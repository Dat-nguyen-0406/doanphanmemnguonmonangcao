@extends('layouts.shop')

@section('content')
<div style="max-width:1200px;margin:0 auto;padding:20px 16px;">

    {{-- BREADCRUMB --}}
    <nav style="margin-bottom:16px;font-size:14px;color:#888;">
        <a href="{{ route('restaurants.index') }}" style="color:#e50050;text-decoration:none;">Nhà hàng</a>
        <span style="margin:0 8px;">›</span>
        <span>{{ $restaurant->name }}</span>
    </nav>

    {{-- HEADER --}}
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:24px;">
        <img src="{{ $restaurant->image_url ?? asset('images/aeon-logo.png') }}"
             alt="{{ $restaurant->name }}"
             style="width:72px;height:72px;border-radius:12px;object-fit:cover;border:2px solid #eee;">
        <div>
            <h1 style="margin:0;font-size:1.6rem;font-weight:800;color:#1a1a1a;">{{ $restaurant->name }}</h1>
            <p style="margin:4px 0 0;color:#888;font-size:14px;">
                <span style="background:#fff3f5;color:#e50050;padding:2px 10px;border-radius:20px;font-size:12px;font-weight:700;">
                    {{ $restaurant->cuisine_type ?? 'Ẩm thực' }}
                </span>
                &nbsp;&nbsp;<span>📍 {{ $restaurant->branch->name }}</span>
            </p>
        </div>
    </div>

    @if(session('error'))
    <div style="background:#fee2e2;border:1px solid #f87171;color:#b91c1c;padding:12px 16px;border-radius:8px;margin-bottom:20px;">
        ⚠️ {{ session('error') }}
    </div>
    @endif

    <form id="bookingForm" action="{{ route('restaurants.book.submit', $restaurant->id) }}" method="POST">
        @csrf

        {{-- BƯỚC 1: CHỌN NGÀY GIỜ --}}
        <div class="step-card" style="background:#fff;border-radius:16px;box-shadow:0 2px 16px rgba(0,0,0,.07);padding:24px;margin-bottom:20px;">
            <h2 style="margin:0 0 16px;font-size:1rem;font-weight:800;color:#e50050;display:flex;align-items:center;gap:8px;">
                <span style="background:#e50050;color:#fff;width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:13px;">1</span>
                Chọn ngày & giờ
            </h2>

            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                <div>
                    <label style="display:block;font-size:13px;font-weight:700;color:#444;margin-bottom:6px;">📅 Ngày đến</label>
                    <input type="date" id="booking_date" name="booking_date"
                           value="{{ $date }}"
                           min="{{ date('Y-m-d') }}"
                           required
                           style="width:100%;padding:10px 12px;border:2px solid #e5e7eb;border-radius:10px;font-size:14px;box-sizing:border-box;">
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:700;color:#444;margin-bottom:6px;">🕐 Giờ đến</label>
                    <select id="booking_time" name="booking_time"
                            style="width:100%;padding:10px 12px;border:2px solid #e5e7eb;border-radius:10px;font-size:14px;box-sizing:border-box;background:#fff;">
                        @foreach(['10:00','10:30','11:00','11:30','12:00','12:30','13:00','13:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30','21:00'] as $t)
                            <option value="{{ $t }}" {{ $time == $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:13px;font-weight:700;color:#444;margin-bottom:6px;">👥 Số người</label>
                    <input type="number" name="guests_count" id="guests_count"
                           min="1" max="50" value="{{ old('guests_count', 2) }}"
                           required
                           style="width:100%;padding:10px 12px;border:2px solid #e5e7eb;border-radius:10px;font-size:14px;box-sizing:border-box;">
                </div>
            </div>

            <button type="button" id="checkAvailBtn"
                    style="margin-top:16px;background:#e50050;color:#fff;border:none;padding:11px 24px;border-radius:10px;font-weight:700;font-size:14px;cursor:pointer;">
                🔍 Xem bàn trống
            </button>
        </div>

        {{-- BƯỚC 2: SƠ ĐỒ BÀN --}}
        <div class="step-card" style="background:#fff;border-radius:16px;box-shadow:0 2px 16px rgba(0,0,0,.07);padding:24px;margin-bottom:20px;">
            <h2 style="margin:0 0 8px;font-size:1rem;font-weight:800;color:#e50050;display:flex;align-items:center;gap:8px;">
                <span style="background:#e50050;color:#fff;width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:13px;">2</span>
                Chọn bàn
            </h2>

            {{-- Legend --}}
            <div style="display:flex;gap:20px;margin-bottom:16px;flex-wrap:wrap;">
                <div style="display:flex;align-items:center;gap:6px;font-size:13px;">
                    <div style="width:28px;height:28px;border-radius:6px;background:#e8f5e9;border:2px solid #4caf50;"></div>
                    <span>Còn trống</span>
                </div>
                <div style="display:flex;align-items:center;gap:6px;font-size:13px;">
                    <div style="width:28px;height:28px;border-radius:50%;background:#e3f2fd;border:2px solid #2196f3;"></div>
                    <span>Bàn tròn - trống</span>
                </div>
                <div style="display:flex;align-items:center;gap:6px;font-size:13px;">
                    <div style="width:28px;height:28px;border-radius:6px;background:#f5f5f5;border:2px solid #bbb;"></div>
                    <span>Đã đặt</span>
                </div>
                <div style="display:flex;align-items:center;gap:6px;font-size:13px;">
                    <div style="width:28px;height:28px;border-radius:6px;background:#e50050;border:2px solid #e50050;"></div>
                    <span>Bạn đang chọn</span>
                </div>
            </div>

            {{-- Tab tầng --}}
            @if($floors->count() > 1)
            <div style="display:flex;gap:8px;margin-bottom:16px;border-bottom:2px solid #f0f0f0;padding-bottom:0;">
                @foreach($floors as $floor)
                <button type="button" class="floor-tab" data-floor="{{ $floor }}"
                        style="padding:8px 20px;border:none;background:none;font-weight:700;font-size:14px;cursor:pointer;color:#888;border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .2s;">
                    Tầng {{ $floor }}
                </button>
                @endforeach
            </div>
            @endif

            <input type="hidden" id="selected_table_id" name="table_id" required>
            <input type="hidden" id="active_floor" value="{{ $floors->first() }}">

            <div id="tableMapContainer">
                @foreach($floors as $floor)
                <div class="floor-map" data-floor="{{ $floor }}"
                     style="display:{{ $loop->first ? 'block' : 'none' }};">
                    <p style="font-size:12px;color:#aaa;margin-bottom:12px;text-align:center;">
                        Tầng {{ $floor }} &mdash; Nhấn vào bàn để chọn
                    </p>
                    <div style="position:relative;min-height:300px;background:linear-gradient(135deg,#fafafa 25%,#f5f5f5 25%,#f5f5f5 50%,#fafafa 50%,#fafafa 75%,#f5f5f5 75%);background-size:30px 30px;border-radius:12px;overflow:hidden;padding:24px;">

                        {{-- Cửa vào --}}
                        <div style="position:absolute;top:0;left:50%;transform:translateX(-50%);background:#e50050;color:#fff;font-size:11px;font-weight:700;padding:4px 16px;border-radius:0 0 8px 8px;">
                            🚪 LỐI VÀO
                        </div>

                        {{-- Grid bàn --}}
                        <div style="display:flex;flex-wrap:wrap;gap:16px;margin-top:20px;justify-content:center;">
                            @foreach($tables->where('floor', $floor) as $table)
                            <div class="table-seat {{ $table->is_booked ? 'booked' : 'available' }}"
                                 data-id="{{ $table->id }}"
                                 data-capacity="{{ $table->capacity }}"
                                 data-label="{{ $table->label ?? $table->table_number }}"
                                 data-shape="{{ $table->shape }}"
                                 data-floor="{{ $table->floor }}"
                                 data-booked="{{ $table->is_booked ? '1' : '0' }}"
                                 onclick="selectTable(this)"
                                 title="{{ $table->is_booked ? 'Đã được đặt' : 'Bàn ' . ($table->label ?? $table->table_number) . ' — ' . $table->capacity . ' người' }}"
                                 style="
                                    cursor:{{ $table->is_booked ? 'not-allowed' : 'pointer' }};
                                    width:{{ $table->shape === 'long' ? '100px' : '76px' }};
                                    height:{{ $table->shape === 'long' ? '48px' : '76px' }};
                                    border-radius:{{ $table->shape === 'round' ? '50%' : '10px' }};
                                    background:{{ $table->is_booked ? '#f5f5f5' : '#e8f5e9' }};
                                    border:2px solid {{ $table->is_booked ? '#bbb' : '#4caf50' }};
                                    display:flex;flex-direction:column;align-items:center;justify-content:center;
                                    transition:all .2s;position:relative;
                                    box-shadow:0 2px 6px rgba(0,0,0,.06);
                                 ">
                                <span style="font-size:11px;font-weight:800;color:{{ $table->is_booked ? '#bbb' : '#2e7d32' }};">
                                    {{ $table->label ?? ('Bàn ' . $table->table_number) }}
                                </span>
                                <span style="font-size:10px;color:{{ $table->is_booked ? '#ccc' : '#888' }};">
                                    👤 {{ $table->capacity }}
                                </span>
                                @if($table->is_booked)
                                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#ccc;font-size:20px;">✕</div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Thông tin bàn đang chọn --}}
            <div id="selectedTableInfo" style="display:none;margin-top:16px;padding:14px 18px;background:#fff3f5;border-radius:10px;border-left:4px solid #e50050;">
                <strong style="color:#e50050;">✅ Bạn đã chọn:</strong>
                <span id="selectedTableLabel" style="font-weight:700;margin-left:8px;"></span>
                <span style="color:#888;font-size:13px;" id="selectedTableMeta"></span>
            </div>

            <div id="tableError" style="display:none;color:#e50050;font-size:13px;margin-top:8px;">
                ⚠️ Vui lòng chọn một bàn trước khi tiếp tục.
            </div>
        </div>

        {{-- BƯỚC 3: ĐẶT MÓN TRƯỚC --}}
        @if($menuItems->isNotEmpty())
        <div class="step-card" style="background:#fff;border-radius:16px;box-shadow:0 2px 16px rgba(0,0,0,.07);padding:24px;margin-bottom:20px;">
            <h2 style="margin:0 0 4px;font-size:1rem;font-weight:800;color:#e50050;display:flex;align-items:center;gap:8px;">
                <span style="background:#e50050;color:#fff;width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:13px;">3</span>
                Đặt món trước (Tùy chọn)
            </h2>
            <p style="color:#888;font-size:13px;margin-bottom:16px;">Đặt món trước để nhà hàng chuẩn bị — thanh toán khi đến.</p>

            @php
                $categoryLabels = ['main' => '🍜 Món chính', 'appetizer' => '🥗 Khai vị', 'dessert' => '🍮 Tráng miệng', 'drink' => '🥤 Đồ uống'];
            @endphp

            @foreach($menuItems as $cat => $items)
            <div style="margin-bottom:20px;">
                <h3 style="font-size:13px;font-weight:800;color:#555;margin:0 0 12px;text-transform:uppercase;letter-spacing:1px;">
                    {{ $categoryLabels[$cat] ?? $cat }}
                </h3>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px;">
                    @foreach($items as $idx => $item)
                    <div style="display:flex;align-items:center;gap:12px;padding:12px;border:1.5px solid #f0f0f0;border-radius:12px;transition:border-color .2s;" class="menu-card">
                        @if($item->image_url)
                        <img src="{{ $item->image_url }}" alt="{{ $item->name }}"
                             style="width:60px;height:60px;border-radius:8px;object-fit:cover;flex-shrink:0;">
                        @else
                        <div style="width:60px;height:60px;border-radius:8px;background:#f5f5f5;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:24px;">
                            🍽
                        </div>
                        @endif
                        <div style="flex:1;min-width:0;">
                            <p style="margin:0 0 2px;font-weight:700;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item->name }}</p>
                            <p style="margin:0 0 6px;color:#e50050;font-weight:800;font-size:13px;">{{ number_format($item->price) }}đ</p>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <button type="button" onclick="changeQty({{ $item->id }}, -1)"
                                        style="width:28px;height:28px;border-radius:50%;border:1.5px solid #ddd;background:#f9f9f9;cursor:pointer;font-size:16px;line-height:1;">−</button>
                                <input type="number"
                                       id="qty_{{ $item->id }}"
                                       name="pre_order[{{ $idx }}][qty]"
                                       value="0"
                                       min="0" max="20"
                                       data-price="{{ $item->price }}"
                                       data-name="{{ $item->name }}"
                                       onchange="updateTotal()"
                                       style="width:36px;text-align:center;border:1.5px solid #ddd;border-radius:6px;padding:4px;font-size:14px;font-weight:700;">
                                <input type="hidden" name="pre_order[{{ $idx }}][id]" value="{{ $item->id }}">
                                <button type="button" onclick="changeQty({{ $item->id }}, 1)"
                                        style="width:28px;height:28px;border-radius:50%;border:1.5px solid #e50050;background:#e50050;color:#fff;cursor:pointer;font-size:16px;line-height:1;">+</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            {{-- Tóm tắt đặt món --}}
            <div id="preOrderSummary" style="display:none;background:#1a1a1a;color:#fff;border-radius:12px;padding:16px;margin-top:8px;">
                <h4 style="margin:0 0 10px;font-size:13px;font-weight:800;color:#ffd700;">🛒 Món đã chọn</h4>
                <div id="preOrderList" style="font-size:13px;"></div>
                <div style="border-top:1px solid #333;margin-top:10px;padding-top:10px;display:flex;justify-content:space-between;">
                    <span style="font-weight:700;">Tổng tiền món:</span>
                    <span id="preOrderTotal" style="color:#ffd700;font-weight:800;">0đ</span>
                </div>
            </div>
        </div>
        @endif

        {{-- BƯỚC 4: GHI CHÚ + XÁC NHẬN --}}
        <div class="step-card" style="background:#fff;border-radius:16px;box-shadow:0 2px 16px rgba(0,0,0,.07);padding:24px;margin-bottom:20px;">
            <h2 style="margin:0 0 16px;font-size:1rem;font-weight:800;color:#e50050;display:flex;align-items:center;gap:8px;">
                <span style="background:#e50050;color:#fff;width:28px;height:28px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:13px;">{{ $menuItems->isNotEmpty() ? '4' : '3' }}</span>
                Ghi chú & Xác nhận
            </h2>

            <textarea name="note" rows="3" placeholder="Ghi chú cho nhà hàng: bàn góc, có trẻ em, dị ứng thức ăn..."
                      style="width:100%;padding:12px;border:2px solid #e5e7eb;border-radius:10px;font-size:14px;resize:vertical;box-sizing:border-box;"></textarea>

            {{-- Tóm tắt chi phí --}}
            <div style="background:#f9fafb;border-radius:12px;padding:16px;margin-top:16px;">
                <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px;">
                    <span style="color:#666;">Phí cọc giữ bàn:</span>
                    <span style="font-weight:700;">100.000đ</span>
                </div>
                <div id="preOrderCostRow" style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px;display:none;">
                    <span style="color:#666;">Tiền đặt món trước:</span>
                    <span id="preOrderCostDisplay" style="font-weight:700;color:#e50050;">0đ</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:16px;font-weight:800;border-top:2px solid #e5e7eb;padding-top:10px;margin-top:6px;">
                    <span>Tổng thanh toán:</span>
                    <span id="totalDisplay" style="color:#e50050;">100.000đ</span>
                </div>
            </div>

            <button type="submit" id="submitBtn"
                    style="display:block;width:100%;background:#e50050;color:#fff;padding:16px;border:none;border-radius:12px;font-size:16px;font-weight:800;cursor:pointer;margin-top:16px;transition:background .2s;">
                🎉 Xác nhận & Thanh toán cọc
            </button>
        </div>
    </form>
</div>

<style>
.table-seat:hover:not(.booked) { transform: scale(1.06); box-shadow: 0 4px 16px rgba(229,0,80,.2) !important; }
.table-seat.selected { background: #e50050 !important; border-color: #c0003c !important; }
.table-seat.selected span { color: #fff !important; }
.floor-tab.active { color: #e50050 !important; border-bottom-color: #e50050 !important; }
.menu-card:hover { border-color: #e50050; }
#submitBtn:hover { background: #c0003c; }
</style>

<script>
let selectedTableId = null;
let preOrderTotal = 0;
const DEPOSIT = 100000;

// ---- FLOOR TABS ----
document.querySelectorAll('.floor-tab').forEach((btn, idx) => {
    if (idx === 0) btn.classList.add('active');
    btn.addEventListener('click', () => {
        document.querySelectorAll('.floor-tab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const floor = btn.dataset.floor;
        document.querySelectorAll('.floor-map').forEach(m => {
            m.style.display = m.dataset.floor == floor ? 'block' : 'none';
        });
    });
});

// ---- SELECT TABLE ----
function selectTable(el) {
    if (el.dataset.booked === '1') return;

    // Kiểm tra số người
    const guests = parseInt(document.getElementById('guests_count').value) || 0;
    const cap = parseInt(el.dataset.capacity);
    if (guests > cap) {
        alert(`Bàn này chỉ phục vụ ${cap} người. Bạn chọn ${guests} người — vui lòng chọn bàn lớn hơn!`);
        return;
    }

    document.querySelectorAll('.table-seat').forEach(t => {
        if (t.dataset.booked !== '1') {
            t.classList.remove('selected');
            t.style.background = '#e8f5e9';
            t.style.borderColor = '#4caf50';
            t.querySelectorAll('span').forEach(s => {
                s.style.color = s.style.fontSize === '11px' ? '#2e7d32' : '#888';
            });
        }
    });

    el.classList.add('selected');
    selectedTableId = el.dataset.id;
    document.getElementById('selected_table_id').value = selectedTableId;
    document.getElementById('tableError').style.display = 'none';

    const info = document.getElementById('selectedTableInfo');
    info.style.display = 'block';
    document.getElementById('selectedTableLabel').textContent = el.dataset.label;
    document.getElementById('selectedTableMeta').textContent = ` — Sức chứa ${el.dataset.capacity} người`;
}

// ---- CHECK AVAILABILITY (AJAX) ----
document.getElementById('checkAvailBtn').addEventListener('click', () => {
    const date = document.getElementById('booking_date').value;
    const time = document.getElementById('booking_time').value;
    if (!date || !time) { alert('Vui lòng chọn ngày và giờ!'); return; }

    const btn = document.getElementById('checkAvailBtn');
    btn.textContent = '⏳ Đang kiểm tra...';
    btn.disabled = true;

    fetch(`/restaurants/{{ $restaurant->id }}/availability?date=${date}&time=${time}`)
        .then(r => r.json())
        .then(tables => {
            // Reset selection
            selectedTableId = null;
            document.getElementById('selected_table_id').value = '';
            document.getElementById('selectedTableInfo').style.display = 'none';

            tables.forEach(t => {
                const el = document.querySelector(`.table-seat[data-id="${t.id}"]`);
                if (!el) return;
                el.dataset.booked = t.is_booked ? '1' : '0';
                el.classList.remove('selected', 'booked', 'available');
                if (t.is_booked) {
                    el.classList.add('booked');
                    el.style.background = '#f5f5f5';
                    el.style.borderColor = '#bbb';
                    el.style.cursor = 'not-allowed';
                    el.querySelectorAll('span').forEach(s => s.style.color = '#bbb');
                } else {
                    el.classList.add('available');
                    el.style.background = '#e8f5e9';
                    el.style.borderColor = '#4caf50';
                    el.style.cursor = 'pointer';
                    el.querySelectorAll('span').forEach((s, i) => {
                        s.style.color = i === 0 ? '#2e7d32' : '#888';
                    });
                }
            });

            btn.textContent = '✅ Đã cập nhật!';
            setTimeout(() => { btn.textContent = '🔍 Xem bàn trống'; btn.disabled = false; }, 1500);
        })
        .catch(() => { btn.textContent = '🔍 Xem bàn trống'; btn.disabled = false; });
});

// ---- PRE-ORDER ----
function changeQty(id, delta) {
    const input = document.getElementById('qty_' + id);
    const newVal = Math.max(0, Math.min(20, parseInt(input.value || 0) + delta));
    input.value = newVal;
    updateTotal();
}

function updateTotal() {
    let total = 0;
    let lines = [];

    document.querySelectorAll('[id^="qty_"]').forEach(input => {
        const qty = parseInt(input.value) || 0;
        if (qty > 0) {
            const price = parseFloat(input.dataset.price);
            const name  = input.dataset.name;
            const sub   = price * qty;
            total += sub;
            lines.push(`<div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                <span>${name} × ${qty}</span>
                <span style="color:#ffd700;">${sub.toLocaleString('vi-VN')}đ</span>
            </div>`);
        }
    });

    preOrderTotal = total;

    const summary = document.getElementById('preOrderSummary');
    const costRow = document.getElementById('preOrderCostRow');
    if (summary) {
        summary.style.display = total > 0 ? 'block' : 'none';
        document.getElementById('preOrderList').innerHTML = lines.join('');
        document.getElementById('preOrderTotal').textContent = total.toLocaleString('vi-VN') + 'đ';
    }
    if (costRow) {
        costRow.style.display = total > 0 ? 'flex' : 'none';
        document.getElementById('preOrderCostDisplay').textContent = total.toLocaleString('vi-VN') + 'đ';
    }

    const grandTotal = DEPOSIT + total;
    document.getElementById('totalDisplay').textContent = grandTotal.toLocaleString('vi-VN') + 'đ';
}

// ---- FORM SUBMIT VALIDATION ----
document.getElementById('bookingForm').addEventListener('submit', function(e) {
    if (!selectedTableId) {
        e.preventDefault();
        document.getElementById('tableError').style.display = 'block';
        document.getElementById('tableError').scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    const date = document.getElementById('booking_date').value;
    const time = document.getElementById('booking_time').value;
    if (date && time) {
        const selected = new Date(`${date}T${time}`);
        const min = new Date(Date.now() + 3600000);
        if (selected < min) {
            e.preventDefault();
            alert('Bạn phải đặt bàn trước ít nhất 1 tiếng!');
        }
    }
});
</script>
@endsection