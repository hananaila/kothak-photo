<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScheduleManagementController extends Controller
{
    public function index(Request $request): View
    {
        $selectedDate = $request->input('date', Carbon::today()->toDateString());

        $schedules = Schedule::whereDate('date', $selectedDate)
            ->withCount(['bookings as total_booked' => function ($query) {
                $query->whereNotIn('status', ['cancelled']);
            }])
            ->orderBy('start_time', 'asc')
            ->get();

        return view('admin.schedules.index', compact('schedules', 'selectedDate'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        // Ensure time format has seconds
        $start = strlen($validated['start_time']) === 5 ? $validated['start_time'].':00' : $validated['start_time'];
        $end = strlen($validated['end_time']) === 5 ? $validated['end_time'].':00' : $validated['end_time'];

        $existing = Schedule::whereDate('date', $validated['date'])
            ->where('start_time', $start)
            ->where('end_time', $end)
            ->first();

        if ($existing) {
            return back()->with('error', "Slot waktu {$start} - {$end} pada tanggal tersebut sudah ada.");
        }

        Schedule::create([
            'date' => $validated['date'],
            'start_time' => $start,
            'end_time' => $end,
            'capacity' => $validated['capacity'],
            'is_active' => true,
        ]);

        return back()->with('success', "Slot jadwal {$start} - {$end} berhasil dibuat.");
    }

    public function generateBatch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'start_hour' => ['required', 'integer', 'min:0', 'max:23'],
            'end_hour' => ['required', 'integer', 'min:1', 'max:24'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:120'],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $date = $validated['date'];
        $start = Carbon::parse("{$date} ".sprintf('%02d:00:00', $validated['start_hour']));
        $end = Carbon::parse("{$date} ".sprintf('%02d:00:00', $validated['end_hour']));
        $duration = $validated['duration_minutes'];
        $capacity = $validated['capacity'];

        $count = 0;
        $current = $start->copy();

        while ($current->copy()->addMinutes($duration)->lte($end)) {
            $slotStart = $current->format('H:i:s');
            $slotEnd = $current->copy()->addMinutes($duration)->format('H:i:s');

            Schedule::firstOrCreate([
                'date' => $date,
                'start_time' => $slotStart,
                'end_time' => $slotEnd,
            ], [
                'capacity' => $capacity,
                'is_active' => true,
            ]);

            $count++;
            $current->addMinutes($duration);
        }

        return back()->with('success', "Berhasil menjadwalkan {$count} slot waktu otomatis untuk tanggal {$date}.");
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        if ($schedule->activeBookings()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus slot jadwal karena telah memiliki reservasi aktif.');
        }

        $schedule->delete();

        return back()->with('success', 'Slot waktu jadwal berhasil dihapus.');
    }
}
