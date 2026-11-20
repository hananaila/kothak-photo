<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportExportService
{
    /**
     * Compile comprehensive analytics data for Admin Dashboard.
     *
     * @return array<string, mixed>
     */
    public function getAnalyticsData(?string $startDate = null, ?string $endDate = null): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->subDays(29)->startOfDay();
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();

        $query = Booking::with(['package', 'user', 'schedule', 'payment'])
            ->whereBetween('booking_date', [$start, $end]);

        $allBookings = $query->get();

        // 1. Metric Cards
        $totalBookings = $allBookings->count();
        $completedBookings = $allBookings->where('status', 'completed');
        $totalRevenue = $allBookings->whereIn('status', ['confirmed', 'checked_in', 'in_studio', 'completed'])->sum('total_price');
        $todayRevenue = $allBookings->filter(fn ($b) => Carbon::parse($b->booking_date)->isToday() && in_array($b->status, ['confirmed', 'checked_in', 'in_studio', 'completed']))->sum('total_price');

        $verifiedPaymentsCount = $allBookings->filter(fn ($b) => $b->payment && $b->payment->payment_status === 'verified')->count();
        $totalPaymentsCount = $allBookings->filter(fn ($b) => $b->payment !== null)->count();
        $successRate = $totalPaymentsCount > 0 ? round(($verifiedPaymentsCount / $totalPaymentsCount) * 100, 1) : 100.0;

        $avgDuration = $allBookings->avg(fn ($b) => $b->package?->duration ?? 15) ?? 15;

        // 2. Daily Revenue Bar Chart (Last 7-14 days in range)
        $dailyData = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dStr = $cursor->toDateString();
            $label = $cursor->format('d M');
            $rev = $allBookings->filter(function ($b) use ($dStr) {
                return Carbon::parse($b->booking_date)->toDateString() === $dStr
                    && in_array($b->status, ['confirmed', 'checked_in', 'in_studio', 'completed']);
            })->sum('total_price');

            $visits = $allBookings->filter(function ($b) use ($dStr) {
                return Carbon::parse($b->booking_date)->toDateString() === $dStr;
            })->count();

            $dailyData[] = [
                'date' => $dStr,
                'label' => $label,
                'revenue' => (float) $rev,
                'visits' => $visits,
            ];
            $cursor->addDay();
        }

        // Limit daily data to last 14 entries for clear presentation if range is large
        if (count($dailyData) > 14) {
            $dailyChartData = array_slice($dailyData, -14);
        } else {
            $dailyChartData = $dailyData;
        }

        // 3. Package Popularity Donut Chart
        $packageStats = [];
        $packages = Package::all();
        foreach ($packages as $pkg) {
            $count = $allBookings->where('package_id', $pkg->id)->count();
            $rev = $allBookings->where('package_id', $pkg->id)
                ->whereIn('status', ['confirmed', 'checked_in', 'in_studio', 'completed'])
                ->sum('total_price');

            $packageStats[] = [
                'name' => $pkg->name,
                'count' => $count,
                'revenue' => (float) $rev,
            ];
        }

        // 4. Busy Hours Analysis (Jam Sibuk 10:00 - 21:00)
        $hoursDistribution = [];
        for ($h = 10; $h <= 21; $h++) {
            $hFormatted = sprintf('%02d:00', $h);
            $hoursDistribution[$hFormatted] = 0;
        }

        foreach ($allBookings as $b) {
            if ($b->schedule && $b->schedule->start_time) {
                $slotH = substr($b->schedule->start_time, 0, 2).':00';
                if (isset($hoursDistribution[$slotH])) {
                    $hoursDistribution[$slotH]++;
                } else {
                    $hoursDistribution[$slotH] = 1;
                }
            }
        }

        return [
            'startDate' => $start->toDateString(),
            'endDate' => $end->toDateString(),
            'totalBookings' => $totalBookings,
            'completedCount' => $completedBookings->count(),
            'totalRevenue' => $totalRevenue,
            'todayRevenue' => $todayRevenue,
            'successRate' => $successRate,
            'avgDuration' => round($avgDuration, 0),
            'dailyChartData' => $dailyChartData,
            'packageStats' => $packageStats,
            'hoursDistribution' => $hoursDistribution,
            'recentBookings' => $allBookings->sortByDesc('id')->take(20),
        ];
    }

    /**
     * Export bookings to CSV.
     */
    public function exportCsv(Collection $bookings): string
    {
        $output = fopen('php://temp', 'r+');

        // UTF-8 BOM for correct Excel opening
        fwrite($output, "\xEF\xBB\xBF");

        // CSV Header
        fputcsv($output, [
            'No',
            'No. Antrean',
            'Tipe Reservasi',
            'Tanggal Booking',
            'Nama Pelanggan',
            'No. WhatsApp',
            'Paket Foto',
            'Slot Waktu',
            'Background',
            'Harga Paket (IDR)',
            'Total Biaya (IDR)',
            'Status Booking',
            'Status Pembayaran',
            'Metode Pembayaran',
            'Waktu Check-In',
        ]);

        $no = 1;
        foreach ($bookings as $b) {
            fputcsv($output, [
                $no++,
                $b->queue_number,
                strtoupper($b->booking_type ?? 'online'),
                Carbon::parse($b->booking_date)->format('Y-m-d H:i:s'),
                $b->user?->name ?? 'Guest',
                $b->user?->phone_whatsapp ?? '-',
                $b->package?->name ?? '-',
                $b->schedule ? ($b->schedule->formatted_time) : '-',
                $b->studio_backdrop ?? '-',
                number_format((float) $b->package_price, 0, '', ''),
                number_format((float) $b->total_price, 0, '', ''),
                $b->status_label,
                $b->payment ? $b->payment->status_label : 'Belum Bayar',
                $b->payment ? strtoupper($b->payment->payment_method) : '-',
                $b->checked_in_at ? Carbon::parse($b->checked_in_at)->format('Y-m-d H:i') : '-',
            ]);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent ?: '';
    }
}
