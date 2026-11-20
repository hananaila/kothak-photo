<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\ReportExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __construct(
        protected ReportExportService $reportService
    ) {}

    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(14)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $analytics = $this->reportService->getAnalyticsData($startDate, $endDate);

        return view('admin.dashboard', array_merge($analytics, [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]));
    }

    public function exportCsv(Request $request): Response
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $bookings = Booking::with(['package', 'user', 'schedule', 'payment'])
            ->whereBetween('booking_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->orderBy('id', 'desc')
            ->get();

        $csvData = $this->reportService->exportCsv($bookings);
        $fileName = sprintf('laporan_kothak_photo_%s_sd_%s.csv', $startDate, $endDate);

        return response($csvData, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    public function printReport(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $analytics = $this->reportService->getAnalyticsData($startDate, $endDate);
        $bookings = Booking::with(['package', 'user', 'schedule', 'payment'])
            ->whereBetween('booking_date', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ])
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.report_print', array_merge($analytics, [
            'bookings' => $bookings,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]));
    }
}
