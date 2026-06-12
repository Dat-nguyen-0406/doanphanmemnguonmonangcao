<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use App\Models\Movie;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShowtimeController extends Controller
{
    public function index()
    {
        $query = Showtime::with('movie', 'branch')
            ->orderBy('start_time', 'desc');

        // Nếu là Cinema Partner (role 2), chỉ hiển thị lịch chiếu của chi nhánh của họ
        if (Auth::user()->role == 2) {
            if (!Auth::user()->branch_id) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Bạn chưa được gán chi nhánh. Liên hệ Admin để được cấu hình.');
            }
            $query->where('branch_id', Auth::user()->branch_id);
        }

        $showtimes = $query->paginate(15);
        
        return view('admin.cinema.showtimes.index', compact('showtimes'));
    }

    public function create()
    {
        $movies = Movie::all();
        
        // Nếu là Cinema Partner (role 2), chỉ hiển thị chi nhánh của họ
        if (Auth::user()->role == 2) {
            if (!Auth::user()->branch_id) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Bạn chưa được gán chi nhánh.');
            }
            $branches = Branch::where('id', Auth::user()->branch_id)->get();
        } else {
            $branches = Branch::all();
        }
        
        return view('admin.cinema.showtimes.create', compact('movies', 'branches'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role == 1) {
        return back()->with('error', 'Admin tổng chỉ có quyền xem, không được tạo lịch chiếu.');
    }
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'branch_id' => 'required|exists:branches,id',
            'start_time' => 'required|date',
            'price_normal' => 'required|numeric|min:0',
            'price_vip' => 'required|numeric|min:0',
        ], [
            'movie_id.required' => 'Vui lòng chọn phim.',
            'branch_id.required' => 'Vui lòng chọn chi nhánh.',
            'start_time.required' => 'Vui lòng chọn thời gian chiếu.',
            'price_normal.required' => 'Vui lòng nhập giá ghế thường.',
            'price_vip.required' => 'Vui lòng nhập giá ghế VIP.',
        ]);

        // Nếu là Cinema Partner, bắt buộc branch_id phải khớp
        if (Auth::user()->role == 2) {
            if ($validated['branch_id'] != Auth::user()->branch_id) {
                return back()->with('error', 'Bạn không có quyền tạo lịch chiếu cho chi nhánh này.');
            }
        }

        // Set price to price_normal for backward compatibility
        $validated['price'] = $validated['price_normal'];

        Showtime::create($validated);

        return redirect()->route('admin.showtimes.index')
            ->with('success', 'Thêm lịch chiếu mới thành công!');
    }

    public function edit(Showtime $showtime)
{
    $user = Auth::user();

    // Kiểm tra quyền: Nếu là Role 2 mà sai chi nhánh thì chặn
    if ($user->role == 2 && $showtime->branch_id != $user->branch_id) {
        return redirect()->route('admin.showtimes.index')->with('error', 'Bạn không có quyền.');
    }

    // Gợi ý: Bạn có thể truyền một biến check vào View để ẩn nút "Lưu" nếu là Role 1
    $isReadOnly = ($user->role == 1);

    $movies = Movie::all();
    $branches = ($user->role == 2) 
                ? Branch::where('id', $user->branch_id)->get() 
                : Branch::all();
    
    return view('admin.cinema.showtimes.edit', compact('showtime', 'movies', 'branches', 'isReadOnly'));
}

    public function update(Request $request, Showtime $showtime)
    {
        // Kiểm tra quyền: Cinema Partner chỉ được sửa lịch chiếu của chi nhánh mình
        if (Auth::user()->role == 1) {
        return redirect()->route('admin.showtimes.index')->with('error', 'Admin tổng không được phép sửa dữ liệu này.');
    }

        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'branch_id' => 'required|exists:branches,id',
            'start_time' => 'required|date_format:Y-m-d H:i',
            'price_normal' => 'required|numeric|min:0',
            'price_vip' => 'required|numeric|min:0',
        ]);

        // Nếu là Cinema Partner, bắt buộc branch_id không được đổi
        if (Auth::user()->role == 2 && $validated['branch_id'] != Auth::user()->branch_id) {
            return back()->with('error', 'Bạn không có quyền sửa chi nhánh của lịch chiếu.');
        }

        $validated['price'] = $validated['price_normal'];

        $showtime->update($validated);

        return redirect()->route('admin.showtimes.index')
            ->with('success', 'Cập nhật lịch chiếu thành công!');
    }

    public function destroy(Showtime $showtime)
    {
        // Kiểm tra quyền: Cinema Partner chỉ được xóa lịch chiếu của chi nhánh mình
        if (Auth::user()->role == 2 && $showtime->branch_id != Auth::user()->branch_id) {
            return redirect()->route('admin.showtimes.index')
                ->with('error', 'Bạn không có quyền xóa lịch chiếu này.');
        }

        $showtime->delete();

        return redirect()->route('admin.showtimes.index')
            ->with('success', 'Xóa lịch chiếu thành công!');
    }
}
