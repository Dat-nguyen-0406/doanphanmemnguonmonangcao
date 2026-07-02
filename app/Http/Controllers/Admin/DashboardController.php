<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Showtime;
use App\Models\Movie;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'day');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Default date range
        if (!$dateFrom) {
            $dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        }
        if (!$dateTo) {
            $dateTo = Carbon::now()->format('Y-m-d');
        }

        $dateFromObj = Carbon::createFromFormat('Y-m-d', $dateFrom);
        $dateToObj = Carbon::createFromFormat('Y-m-d', $dateTo);

        // Base query for revenue
        $baseQuery = Payment::whereHas('booking', function ($q) use ($dateFromObj, $dateToObj) {
            $q->whereBetween('booking_date', [$dateFromObj->startOfDay(), $dateToObj->endOfDay()]);
        })
        ->where('status', 'success');

        // Nếu là Cinema Partner (role 2), lọc theo branch
        if (Auth::user()->role == 2) {
            if (!Auth::user()->branch_id) {
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Bạn chưa được gán chi nhánh.');
            }
            $baseQuery->whereHas('booking.showtime', function ($q) {
                $q->where('branch_id', Auth::user()->branch_id);
            });
        }

        // Total revenue
        $totalRevenue = $baseQuery->sum('amount');

        // Revenue by period
        $revenueByPeriod = $this->getRevenueByPeriod($period, $dateFromObj, $dateToObj);

        // Revenue by movie
        $revenueByMovie = $this->getRevenueByMovie($dateFromObj, $dateToObj);

        // Revenue by branch
        $revenueByBranch = $this->getRevenueByBranch($dateFromObj, $dateToObj);

        // Statistics
        $statisticsQuery = Booking::whereBetween('booking_date', [$dateFromObj->startOfDay(), $dateToObj->endOfDay()]);
        
        // Filter by branch nếu là Cinema Partner
        if (Auth::user()->role == 2) {
            $statisticsQuery->whereHas('showtime', function ($q) {
                $q->where('branch_id', Auth::user()->branch_id);
            });
        }

        $totalBookings = $statisticsQuery->count();
        
        $completedBookings = $statisticsQuery->where('status', '!=', 'cancelled')
            ->count();

        $averageTicketPrice = $totalBookings > 0 ? $totalRevenue / $totalBookings : 0;

        return view('admin.cinema.dashboard.index', compact(
            'totalRevenue',
            'revenueByPeriod',
            'revenueByMovie',
            'revenueByBranch',
            'totalBookings',
            'completedBookings',
            'averageTicketPrice',
            'period',
            'dateFrom',
            'dateTo'
        ));
    }

    private function getRevenueByPeriod($period, $dateFrom, $dateTo)
    {
        $data = [];
        
        if ($period === 'day') {
            $current = $dateFrom->copy();
            while ($current->lte($dateTo)) {
                $query = Payment::whereHas('booking', function ($q) use ($current) {
                    $q->whereDate('booking_date', $current->format('Y-m-d'));
                })
                ->where('status', 'success');

                // Filter by branch nếu là Cinema Partner
                if (Auth::user()->role == 2) {
                    $query->whereHas('booking.showtime', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }

                $dayRevenue = $query->sum('amount');
                $data[$current->format('d/m')] = $dayRevenue;
                $current->addDay();
            }
        } elseif ($period === 'week') {
            $current = $dateFrom->copy()->startOfWeek();
            while ($current->lte($dateTo)) {
                $weekEnd = $current->copy()->endOfWeek();
                $query = Payment::whereHas('booking', function ($q) use ($current, $weekEnd) {
                    $q->whereBetween('booking_date', [$current->startOfDay(), $weekEnd->endOfDay()]);
                })
                ->where('status', 'success');

                // Filter by branch nếu là Cinema Partner
                if (Auth::user()->role == 2) {
                    $query->whereHas('booking.showtime', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }

                $weekRevenue = $query->sum('amount');
                $label = 'Tuần ' . $current->weekOfYear;
                $data[$label] = $weekRevenue;
                $current->addWeek();
            }
        } elseif ($period === 'month') {
            $current = $dateFrom->copy()->startOfMonth();
            while ($current->lte($dateTo)) {
                $monthEnd = $current->copy()->endOfMonth();
                $query = Payment::whereHas('booking', function ($q) use ($current, $monthEnd) {
                    $q->whereBetween('booking_date', [$current->startOfDay(), $monthEnd->endOfDay()]);
                })
                ->where('status', 'success');

                // Filter by branch nếu là Cinema Partner
                if (Auth::user()->role == 2) {
                    $query->whereHas('booking.showtime', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }

                $monthRevenue = $query->sum('amount');
                $label = 'Tháng ' . $current->format('m');
                $data[$label] = $monthRevenue;
                $current->addMonth();
            }
        }

        return $data;
    }

    private function getRevenueByMovie($dateFrom, $dateTo)
    {
        $query = Payment::selectRaw('movies.title, SUM(payments.amount) as revenue, COUNT(DISTINCT bookings.id) as booking_count')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
            ->join('movies', 'showtimes.movie_id', '=', 'movies.id')
            ->whereBetween('bookings.booking_date', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->where('payments.status', 'success');

        // Filter by branch nếu là Cinema Partner
        if (Auth::user()->role == 2) {
            $query->where('showtimes.branch_id', Auth::user()->branch_id);
        }

        return $query->groupBy('movies.id', 'movies.title')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();
    }

    private function getRevenueByBranch($dateFrom, $dateTo)
    {
        $query = Payment::selectRaw('branches.name, SUM(payments.amount) as revenue, COUNT(DISTINCT bookings.id) as booking_count')
            ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
            ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
            ->join('branches', 'showtimes.branch_id', '=', 'branches.id')
            ->whereBetween('bookings.booking_date', [$dateFrom->startOfDay(), $dateTo->endOfDay()])
            ->where('payments.status', 'success');

        // Nếu là Cinema Partner, chỉ hiển thị branch của họ
        if (Auth::user()->role == 2) {
            $query->where('showtimes.branch_id', Auth::user()->branch_id);
        }

        return $query->groupBy('branches.id', 'branches.name')
            ->orderByDesc('revenue')
            ->get();
    }

    public function exportRevenue(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', Carbon::now()->format('Y-m-d'));
        $type = $request->get('type', 'movie');

        $dateFromObj = Carbon::createFromFormat('Y-m-d', $dateFrom);
        $dateToObj = Carbon::createFromFormat('Y-m-d', $dateTo);

        $filename = "revenue_" . $type . "_" . date('Y-m-d-His') . ".csv";

        $callback = function() use ($type, $dateFromObj, $dateToObj) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 in Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($type === 'movie') {
                fputcsv($handle, ['Tên Phim', 'Số Lượng Vé', 'Doanh Thu (VND)']);
                
                $query = Payment::selectRaw('movies.title, COUNT(DISTINCT bookings.id) as booking_count, SUM(payments.amount) as revenue')
                    ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
                    ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
                    ->join('movies', 'showtimes.movie_id', '=', 'movies.id')
                    ->whereBetween('bookings.booking_date', [$dateFromObj->startOfDay(), $dateToObj->endOfDay()])
                    ->where('payments.status', 'success');

                // Filter by branch nếu là Cinema Partner
                if (Auth::user()->role == 2) {
                    $query->where('showtimes.branch_id', Auth::user()->branch_id);
                }

                $data = $query->groupBy('movies.id', 'movies.title')
                    ->orderByDesc('revenue')
                    ->get();

                foreach ($data as $row) {
                    fputcsv($handle, [
                        $row->title,
                        $row->booking_count,
                        number_format($row->revenue, 0, ',', '.'),
                    ]);
                }
            } elseif ($type === 'branch') {
                fputcsv($handle, ['Chi Nhánh', 'Số Lượng Vé', 'Doanh Thu (VND)']);
                
                $query = Payment::selectRaw('branches.name, COUNT(DISTINCT bookings.id) as booking_count, SUM(payments.amount) as revenue')
                    ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
                    ->join('showtimes', 'bookings.showtime_id', '=', 'showtimes.id')
                    ->join('branches', 'showtimes.branch_id', '=', 'branches.id')
                    ->whereBetween('bookings.booking_date', [$dateFromObj->startOfDay(), $dateToObj->endOfDay()])
                    ->where('payments.status', 'success');

                // Filter by branch nếu là Cinema Partner
                if (Auth::user()->role == 2) {
                    $query->where('showtimes.branch_id', Auth::user()->branch_id);
                }

                $data = $query->groupBy('branches.id', 'branches.name')
                    ->orderByDesc('revenue')
                    ->get();

                foreach ($data as $row) {
                    fputcsv($handle, [
                        $row->name,
                        $row->booking_count,
                        number_format($row->revenue, 0, ',', '.'),
                    ]);
                }
            } else { // daily
                fputcsv($handle, ['Ngày', 'Số Lượng Vé', 'Doanh Thu (VND)']);
                
                $query = Payment::selectRaw('DATE(bookings.booking_date) as booking_date, COUNT(DISTINCT bookings.id) as booking_count, SUM(payments.amount) as revenue')
                    ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
                    ->whereBetween('bookings.booking_date', [$dateFromObj->startOfDay(), $dateToObj->endOfDay()])
                    ->where('payments.status', 'success');

                // Filter by branch nếu là Cinema Partner
                if (Auth::user()->role == 2) {
                    $query->whereHas('booking.showtime', function ($q) {
                        $q->where('branch_id', Auth::user()->branch_id);
                    });
                }

                $data = $query->groupBy('booking_date')
                    ->orderBy('booking_date', 'desc')
                    ->get();

                foreach ($data as $row) {
                    fputcsv($handle, [
                        Carbon::parse($row->booking_date)->format('d/m/Y'),
                        $row->booking_count,
                        number_format($row->revenue, 0, ',', '.'),
                    ]);
                }
            }

            fclose($handle);
        };
        
        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
