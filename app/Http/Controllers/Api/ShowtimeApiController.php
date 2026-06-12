<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Showtime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * API Controller cho Showtime
 * Phục vụ các request từ client-side
 */
class ShowtimeApiController extends Controller
{
    /**
     * Lấy danh sách suất chiếu với filters
     * GET /api/showtimes?branch_id=1&movie_id=2&date=2026-05-15
     */
    public function index(Request $request)
    {
        try {
            $query = Showtime::with('movie', 'branch', 'bookings.seats');

            // Filter by branch
            if ($request->has('branch_id')) {
                $query->where('branch_id', $request->query('branch_id'));
            }

            // Filter by movie
            if ($request->has('movie_id')) {
                $query->where('movie_id', $request->query('movie_id'));
            }

            // Filter by date
            if ($request->has('date')) {
                $date = $request->query('date');
                $query->whereDate('start_time', $date);
            }

            $showtimes = $query->orderBy('start_time', 'asc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $showtimes,
                'message' => 'Lấy danh sách suất chiếu thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy chi tiết suất chiếu
     * GET /api/showtimes/{id}
     */
    public function show($id)
    {
        try {
            $showtime = Showtime::with('movie', 'branch', 'bookings.seats')
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $showtime,
                'message' => 'Lấy chi tiết suất chiếu thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy suất chiếu.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy suất chiếu theo chi nhánh
     * GET /api/branches/{branch_id}/showtimes
     */
    public function byBranch($branchId, Request $request)
    {
        try {
            $query = Showtime::where('branch_id', $branchId)
                ->with('movie', 'branch');

            // Filter by date if provided
            if ($request->has('date')) {
                $date = $request->query('date');
                $query->whereDate('start_time', $date);
            }

            $showtimes = $query->orderBy('start_time', 'asc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $showtimes,
                'message' => 'Lấy suất chiếu theo chi nhánh thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy suất chiếu theo phim
     * GET /api/movies/{movie_id}/showtimes
     */
    public function byMovie($movieId, Request $request)
    {
        try {
            $query = Showtime::where('movie_id', $movieId)
                ->with('movie', 'branch');

            // Filter by branch if provided
            if ($request->has('branch_id')) {
                $query->where('branch_id', $request->query('branch_id'));
            }

            $showtimes = $query->orderBy('start_time', 'asc')->get();
            
            return response()->json([
                'success' => true,
                'data' => $showtimes,
                'message' => 'Lấy suất chiếu theo phim thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
