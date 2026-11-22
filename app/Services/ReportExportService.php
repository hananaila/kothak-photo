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

        // 1. Metric Cards: Perhitungan Akurat Berdasarkan Status Riil Transaksi Pengguna
        $totalBookings = $allBookings->count();
        $completedBookings = $allBookings->where('status', 'completed');
        $inStudioBookings = $allBookings->where('status', 'in_studio');
        $waitingBookings = $allBookings->whereIn('status', ['confirmed', 'checked_in', 'waiting_verification']);
        $noShowBookings = $allBookings->whereIn('status', ['cancelled', 'no_show']);

        // Pendapatan Sah/Terealisasi: HANYA sesi yang telah diselesaikan di kasir (completed)
        $totalRevenue = $allBookings->where('status', 'completed')->sum('total_price');

        $todayRevenue = $allBookings->filter(function ($b) {
            return Carbon::parse($b->booking_date)->isToday()
                && $b->status === 'completed';
        })->sum('total_price');

        $verifiedPaymentsCount = $allBookings->filter(fn ($b) => $b->payment && in_array($b->payment->payment_status, ['verified', 'completed']))->count();
        $totalPaymentsCount = $allBookings->filter(fn ($b) => $b->payment !== null)->count();
        $successRate = $totalPaymentsCount > 0 ? round(($verifiedPaymentsCount / $totalPaymentsCount) * 100, 1) : 100.0;

        // Attendance & No-Show metrics
        $noShowCount = $noShowBookings->count();
        $attendedTotal = $completedBookings->count() + $noShowCount;
        $attendanceRate = $attendedTotal > 0 ? round(($completedBookings->count() / $attendedTotal) * 100, 1) : 100.0;

        // Booth Popularity (Dinamis dari seluruh paket yang ada di studio)
        $packages = Package::all();
        $boothStats = [];
        foreach ($packages as $pkg) {
            $boothStats[$pkg->name] = $allBookings->filter(function ($b) use ($pkg) {
                return $b->package_id === $pkg->id || $b->studio_backdrop === $pkg->name;
            })->count();
        }

        $topBoothName = 'Belum ada antrean';
        $topBoothCount = 0;
        foreach ($boothStats as $bName => $bCount) {
            if ($bCount >= $topBoothCount && $bCount > 0) {
                $topBoothName = $bName;
                $topBoothCount = $bCount;
            }
        }
        if ($topBoothCount === 0 && $packages->isNotEmpty()) {
            $topBoothName = $packages->first()->name;
        }

        $avgDuration = $allBookings->avg(fn ($b) => $b->package?->duration ?? 15) ?? 15;

        // 2. Daily Revenue Bar Chart (Last 7-14 days in range)
        $dailyData = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dStr = $cursor->toDateString();
            $label = $cursor->format('d M');
            $rev = $allBookings->filter(function ($b) use ($dStr) {
                return Carbon::parse($b->booking_date)->toDateString() === $dStr
                    && $b->status === 'completed';
            })->sum('total_price');

            $visits = $allBookings->filter(function ($b) use ($dStr) {
                return Carbon::parse($b->booking_date)->toDateString() === $dStr;
            })->count();

            $dailyData[] = [
                'date' => $dStr,
                'label' => $label,
                'revenue' => (float) $rev,
                'visits' => $visits,
                'is_today' => $cursor->isToday(),
            ];
            $cursor->addDay();
        }

        // Limit daily data to last 14 entries for clear presentation if range is large
        if (count($dailyData) > 14) {
            $dailyChartData = array_slice($dailyData, -14);
        } else {
            $dailyChartData = $dailyData;
        }

        // 3. Package Popularity & Revenue Breakdown (Palet Warna Unik dan Serasi per Booth)
        $packageColorPalette = [
            '#E06A55', // Warm Terracotta Coral (Flower Garden / Floral)
            '#1E3A8A', // Deep Royal Navy Blue (Lift Booth / Modern Box)
            '#D97706', // Warm Vintage Amber (Vintage Booth / Classic)
            '#0D9488', // Emerald Teal (Hana Booth / Aesthetic)
            '#7C3AED', // Soft Royal Violet
            '#2563EB', // Vivid Cobalt Blue
            '#EA580C', // Sunset Orange
        ];

        $packageStats = [];
        $totalRevForPerc = $totalRevenue > 0 ? $totalRevenue : 1;
        $colorIdx = 0;

        foreach ($packages as $pkg) {
            // Cocokkan pemesanan berdasarkan package_id atau studio_backdrop
            $matchingBookings = $allBookings->filter(function ($b) use ($pkg) {
                return $b->package_id === $pkg->id || $b->studio_backdrop === $pkg->name;
            });

            $count = $matchingBookings->count();
            $rev = (float) $matchingBookings
                ->where('status', 'completed')
                ->sum('total_price');

            $percentage = round(($rev / $totalRevForPerc) * 100, 1);
            $color = $packageColorPalette[$colorIdx % count($packageColorPalette)];
            $colorIdx++;

            $packageStats[] = [
                'id' => $pkg->id,
                'name' => $pkg->name,
                'category' => $pkg->category,
                'badge' => $pkg->badge,
                'price' => (float) $pkg->price,
                'count' => $count,
                'revenue' => $rev,
                'percentage' => $percentage,
                'color' => $color,
            ];
        }

        usort($packageStats, fn ($a, $b) => $b['revenue'] <=> $a['revenue']);

        // 4. Busy Hours Analysis (Berdasarkan Jam Booking Aktual Pengguna)
        $hoursDistribution = [];
        for ($h = 10; $h <= 21; $h++) {
            $hFormatted = sprintf('%02d:00', $h);
            $hoursDistribution[$hFormatted] = 0;
        }

        foreach ($allBookings as $b) {
            $timeSource = $b->created_at ?? $b->booking_date;
            if ($timeSource) {
                $hour = Carbon::parse($timeSource)->format('H').':00';
                if (isset($hoursDistribution[$hour])) {
                    $hoursDistribution[$hour]++;
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
            'attendanceRate' => $attendanceRate,
            'noShowCount' => $noShowCount,
            'boothStats' => $boothStats,
            'topBoothName' => $topBoothName,
            'topBoothCount' => $topBoothCount,
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
