<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seat;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeatController extends Controller
{
    public function index()
    {
        $query = Seat::with('branch')
            ->orderBy('branch_id', 'asc')
            ->orderBy('row', 'asc')
            ->orderBy('seat_number', 'asc');

        // Nếu là Cinema Partner (role 2), chỉ hiển thị ghế của chi nhánh mình
        if (Auth::user()->role == 2) {
            if (!Auth::user()->branch_id) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Bạn chưa được gán chi nhánh.');
            }
            $query->where('branch_id', Auth::user()->branch_id);
        }

        $seats = $query->paginate(30);
        
        return view('admin.cinema.seats.index', compact('seats'));
    }

    public function create()
    {
        // Nếu là Cinema Partner, chỉ hiển thị chi nhánh của họ
        if (Auth::user()->role == 2) {
            if (!Auth::user()->branch_id) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Bạn chưa được gán chi nhánh.');
            }
            $branches = Branch::where('id', Auth::user()->branch_id)->get();
        } else {
            $branches = Branch::all();
        }
        
        return view('admin.cinema.seats.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'row' => 'required|string|max:10',
            'seat_number' => 'required|integer|min:1',
            'type' => 'required|in:normal,vip',
        ], [
            'branch_id.required' => 'Vui lòng chọn chi nhánh.',
            'row.required' => 'Vui lòng nhập hàng ghế.',
            'seat_number.required' => 'Vui lòng nhập số ghế.',
            'type.required' => 'Vui lòng chọn loại ghế.',
        ]);

        // Nếu là Cinema Partner, bắt buộc branch_id phải khớp
        if (Auth::user()->role == 2 && $validated['branch_id'] != Auth::user()->branch_id) {
            return back()->with('error', 'Bạn không có quyền tạo ghế cho chi nhánh này.');
        }

        // Check for duplicate
        $existing = Seat::where('branch_id', $validated['branch_id'])
            ->where('row', $validated['row'])
            ->where('seat_number', $validated['seat_number'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'Ghế này đã tồn tại!');
        }

        Seat::create($validated);

        return redirect()->route('admin.seats.index')
            ->with('success', 'Thêm ghế mới thành công!');
    }

    public function edit(Seat $seat)
    {
        // Kiểm tra quyền: Cinema Partner chỉ được sửa ghế của chi nhánh mình
        if (Auth::user()->role == 2 && $seat->branch_id != Auth::user()->branch_id) {
            return redirect()->route('admin.seats.index')
                ->with('error', 'Bạn không có quyền sửa ghế này.');
        }

        // Nếu là Cinema Partner, chỉ hiển thị chi nhánh của họ
        if (Auth::user()->role == 2) {
            $branches = Branch::where('id', Auth::user()->branch_id)->get();
        } else {
            $branches = Branch::all();
        }
        
        return view('admin.cinema.seats.edit', compact('seat', 'branches'));
    }

    public function update(Request $request, Seat $seat)
    {
        // Kiểm tra quyền: Cinema Partner chỉ được sửa ghế của chi nhánh mình
        if (Auth::user()->role == 1) {
        return back()->with('error', 'Admin tổng không dùng chức năng này .');
    }

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'row' => 'required|string|max:10',
            'seat_number' => 'required|integer|min:1',
            'type' => 'required|in:normal,vip',
        ]);

        // Nếu là Cinema Partner, bắt buộc branch_id không được đổi
        if (Auth::user()->role == 2 && $validated['branch_id'] != Auth::user()->branch_id) {
            return back()->with('error', 'Bạn không có quyền sửa chi nhánh của ghế.');
        }

        // Check for duplicate with different ID
        $existing = Seat::where('branch_id', $validated['branch_id'])
            ->where('row', $validated['row'])
            ->where('seat_number', $validated['seat_number'])
            ->where('id', '!=', $seat->id)
            ->exists();

        if ($existing) {
            return back()->with('error', 'Ghế này đã tồn tại!');
        }

        $seat->update($validated);

        return redirect()->route('admin.seats.index')
            ->with('success', 'Cập nhật ghế thành công!');
    }

    public function destroy(Seat $seat)
    {
        // Kiểm tra quyền: Cinema Partner chỉ được xóa ghế của chi nhánh mình
        if (Auth::user()->role == 2 && $seat->branch_id != Auth::user()->branch_id) {
            return redirect()->route('admin.seats.index')
                ->with('error', 'Bạn không có quyền xóa ghế này.');
        }

        $seat->delete();

        return redirect()->route('admin.seats.index')
            ->with('success', 'Xóa ghế thành công!');
    }

    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'rows' => 'required|string',
            'seats_per_row' => 'required|integer|min:1',
            'type' => 'required|in:normal,vip',
        ], [
            'branch_id.required' => 'Vui lòng chọn chi nhánh.',
            'rows.required' => 'Vui lòng nhập các hàng ghế (vd: A,B,C).',
            'seats_per_row.required' => 'Vui lòng nhập số ghế trên một hàng.',
            'type.required' => 'Vui lòng chọn loại ghế.',
        ]);

        // Nếu là Cinema Partner, bắt buộc branch_id phải khớp
        if (Auth::user()->role == 2 && $validated['branch_id'] != Auth::user()->branch_id) {
            return back()->with('error', 'Bạn không có quyền tạo ghế cho chi nhánh này.');
        }

        $rows = array_map('trim', explode(',', $validated['rows']));
        $seatsPerRow = $validated['seats_per_row'];
        $branchId = $validated['branch_id'];
        $type = $validated['type'];

        $createdCount = 0;
        
        foreach ($rows as $row) {
            if (empty($row)) continue;
            
            for ($i = 1; $i <= $seatsPerRow; $i++) {
                // Check if seat already exists
                $existing = Seat::where('branch_id', $branchId)
                    ->where('row', $row)
                    ->where('seat_number', $i)
                    ->exists();

                if (!$existing) {
                    Seat::create([
                        'branch_id' => $branchId,
                        'row' => $row,
                        'seat_number' => $i,
                        'type' => $type,
                    ]);
                    $createdCount++;
                }
            }
        }

        return redirect()->route('admin.seats.index')
            ->with('success', "Tạo thành công $createdCount ghế mới!");
    }
}
