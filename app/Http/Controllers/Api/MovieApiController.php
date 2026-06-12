<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;

/**
 * API Controller cho Movie
 * Phục vụ các request từ client-side (fetch, list, etc)
 */
class MovieApiController extends Controller
{
    /**
     * Lấy danh sách tất cả phim
     * GET /api/movies
     */
    public function index()
    {
        try {
            $movies = Movie::with('showtimes')
                ->orderBy('release_date', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $movies,
                'message' => 'Lấy danh sách phim thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách phim: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy chi tiết phim
     * GET /api/movies/{id}
     */
    public function show($id)
    {
        try {
            $movie = Movie::with('showtimes.branch', 'showtimes.bookings')
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $movie,
                'message' => 'Lấy chi tiết phim thành công.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phim.'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tìm kiếm phim
     * GET /api/movies/search?q=title
     */
    public function search(Request $request)
    {
        try {
            $query = $request->query('q', '');
            
            $movies = Movie::where('title', 'LIKE', "%{$query}%")
                ->orWhere('genre', 'LIKE', "%{$query}%")
                ->orWhere('description', 'LIKE', "%{$query}%")
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $movies,
                'message' => 'Tìm kiếm phim thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tìm kiếm: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy phim mới nhất
     * GET /api/movies/latest?limit=10
     */
    public function latest(Request $request)
    {
        try {
            $limit = $request->query('limit', 10);
            
            $movies = Movie::orderBy('release_date', 'desc')
                ->limit($limit)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $movies,
                'message' => 'Lấy phim mới nhất thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}
