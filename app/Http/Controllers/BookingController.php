<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        $packages = Package::where('is_active', true)->get();
        
        // Active queue for live widget
        $today = Carbon::today()->toDateString();
        $inStudio = Booking::with(['user', 'package'])
            ->whereHas('schedule', fn($q) => $q->whereDate('date', $today))
            ->where('status', 'in_studio')
            ->first();

        $callingNext = Booking::with(['user', 'package'])
            ->whereHas('schedule', fn($q) => $q->whereDate('date', $today))
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->orderBy('id', 'asc')
            ->first();

        $activeQueues = Booking::with(['user', 'package', 'schedule'])
            ->whereHas('schedule', fn($q) => $q->whereDate('date', $today))
            ->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in', 'in_studio', 'completed'])
            ->orderBy('id', 'asc')
            ->get();

        return view('customer.packages', compact('packages', 'inStudio', 'callingNext', 'activeQueues'));
    }

    public function selectSchedule(Request $request, Package $package): View
    {
        $selectedDate = $request->query('date', Carbon::today()->toDateString());

        // Get or create slots for this date if none exists
        $schedules = Schedule::whereDate('date', $selectedDate)
            ->withCount(['bookings as total_booked' => function ($query) {
                $query->whereNotIn('status', ['cancelled', 'Ditolak']);
            }])
            ->orderBy('start_time', 'asc')
            ->get();

        // If no schedules exist for this selected date, seed standard slots
        if ($schedules->isEmpty()) {
            $defaultSlots = [
                ['13:00:00', '13:30:00'],
                ['14:00:00', '14:30:00'],
                ['15:00:00', '15:30:00'],
                ['16:00:00', '16:30:00'],
                ['17:00:00', '17:30:00'],
                ['19:00:00', '19:30:00'],
            ];
            foreach ($defaultSlots as $slot) {
                Schedule::firstOrCreate([
                    'date' => $selectedDate,
                    'start_time' => $slot[0],
                    'end_time' => $slot[1],
                ], ['capacity' => 3]);
            }
            $schedules = Schedule::whereDate('date', $selectedDate)
                ->withCount(['bookings as total_booked' => function ($query) {
                    $query->whereNotIn('status', ['cancelled', 'Ditolak']);
                }])
                ->orderBy('start_time', 'asc')
                ->get();
        }

        return view('customer.select_schedule', compact('package', 'selectedDate', 'schedules'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'package_id' => ['required', 'exists:packages,id'],
            'schedule_id' => ['required', 'exists:schedules,id'],
            'studio_backdrop' => ['nullable', 'string', 'max:80'],
            'name' => ['nullable', 'string', 'max:120'],
            'phone_whatsapp' => ['nullable', 'string', 'max:25'],
        ]);

        $userId = Auth::id();
        if (!$userId) {
            // If user is guest, automatically log in as Hana or create account
            $user = \App\Models\User::firstOrCreate(
                ['email' => 'hananaeylaa@gmail.com'],
                [
                    'name' => $validated['name'] ?? 'Hana Naila Rahmadina',
                    'phone_whatsapp' => $validated['phone_whatsapp'] ?? '081234567890',
                    'password' => bcrypt('password'),
                    'role' => 'customer',
                ]
            );
            Auth::login($user);
            $userId = $user->id;
        }

        return DB::transaction(function () use ($validated, $userId) {
            $schedule = Schedule::lockForUpdate()->findOrFail($validated['schedule_id']);

            $activeCount = Booking::where('schedule_id', $schedule->id)
                ->whereNotIn('status', ['cancelled', 'Ditolak'])
                ->count();

            if ($activeCount >= $schedule->capacity) {
                return back()->with('error', 'Maaf, kuota slot waktu ini sudah penuh. Silakan pilih slot lain.');
            }

            // Generate Queue Number (e.g. KTH-005)
            $dailyBookingsCount = Booking::whereHas('schedule', function ($q) use ($schedule) {
                $q->whereDate('date', $schedule->date);
            })->count();

            $queueNumber = sprintf('KTH-%03d', $dailyBookingsCount + 1);

            $booking = Booking::create([
                'user_id' => $userId,
                'package_id' => $validated['package_id'],
                'schedule_id' => $schedule->id,
                'booking_date' => Carbon::now(),
                'status' => 'waiting_verification',
                'queue_number' => $queueNumber,
                'studio_backdrop' => $validated['studio_backdrop'] ?? 'Electric Blue Backdrop',
            ]);

            return redirect()->route('payment.checkout', $booking->id)
                ->with('success', 'Reservasi berhasil dibuat! Silakan konfirmasi pembayaran QRIS.');
        });
    }

    public function history(): View
    {
        $userId = Auth::id() ?? 1;
        $bookings = Booking::with(['package', 'schedule', 'payment'])
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        return view('customer.history', compact('bookings'));
    }
}
