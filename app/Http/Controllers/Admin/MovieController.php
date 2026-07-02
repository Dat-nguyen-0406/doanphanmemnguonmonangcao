<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::withCount('showtimes')
            ->orderBy('release_date', 'desc')
            ->paginate(15);
        
        return view('admin.cinema.movies.index', compact('movies'));
    }

    public function create()
    {
        return view('admin.cinema.movies.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role == 1) {
        return back()->with('error', 'Admin tổng không dùng chức năng này .');
    }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'genre' => 'nullable|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'title.required' => 'Vui lòng nhập tên phim.',
            'duration.required' => 'Vui lòng nhập thời lượng phim.',
            'release_date.required' => 'Vui lòng chọn ngày phát hành.',
        ]);

        if ($request->hasFile('poster')) {
            $file = $request->file('poster');
            $path = $file->store('movies', 'public');
            $validated['poster'] = $path;
        }

        Movie::create($validated);

        return redirect()->route('admin.movies.index')
            ->with('success', 'Thêm phim mới thành công!');
    }

    public function edit(Movie $movie)
    {
        return view('admin.cinema.movies.edit', compact('movie'));
    }

    public function update(Request $request, Movie $movie)

    {
        if (Auth::user()->role == 1) {
        return back()->with('error', 'Admin tổng không dùng chức năng này .');
    }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'release_date' => 'required|date',
            'genre' => 'nullable|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('poster')) {
            // Delete old poster if exists
            if ($movie->poster && \Storage::disk('public')->exists($movie->poster)) {
                \Storage::disk('public')->delete($movie->poster);
            }
            
            $file = $request->file('poster');
            $path = $file->store('movies', 'public');
            $validated['poster'] = $path;
        }

        $movie->update($validated);

        return redirect()->route('admin.movies.index')
            ->with('success', 'Cập nhật phim thành công!');
    }

    public function destroy(Movie $movie)
    {
        if (Auth::user()->role == 1) {
        return back()->with('error', 'Admin tổng không dùng chức năng này .');
    }
       
        $movie->delete();

        return redirect()->route('admin.movies.index')
            ->with('success', 'Xóa phim thành công!');
    }
}
