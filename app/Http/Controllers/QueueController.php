<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class QueueController extends Controller
{
    /**
     * Dedicated Studio Public Display TV Screen (FR-04).
     */
    public function display(): View
    {
        $today = Carbon::today()->toDateString();

        $inStudio = Booking::with(['user', 'package', 'schedule'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->where('status', 'in_studio')
            ->first();

        $nextQueue = Booking::with(['user', 'package', 'schedule'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->orderBy('id', 'asc')
            ->first();

        $waitingList = Booking::with(['user', 'package', 'schedule'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in'])
            ->orderBy('id', 'asc')
            ->get();

        $completedList = Booking::with(['user', 'package'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->take(5)
            ->get();

        return view('queue.display', compact('inStudio', 'nextQueue', 'waitingList', 'completedList', 'today'));
    }

    /**
     * Real-time JSON API for Live Polling on Display & Customer Widget.
     */
    public function statusJson(): JsonResponse
    {
        $today = Carbon::today()->toDateString();

        $inStudio = Booking::with(['user', 'package', 'schedule'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->where('status', 'in_studio')
            ->first();

        $nextQueue = Booking::with(['user', 'package', 'schedule'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->orderBy('id', 'asc')
            ->first();

        $activeQueues = Booking::with(['user', 'package', 'schedule'])
            ->whereHas('schedule', fn ($q) => $q->whereDate('date', $today))
            ->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in', 'in_studio', 'completed'])
            ->orderBy('id', 'asc')
            ->get();

        // Check if called within last 20 seconds for audio chime
        $soundAlert = false;
        $calledNumber = null;
        if ($inStudio && $inStudio->called_at && Carbon::parse($inStudio->called_at)->diffInSeconds(Carbon::now()) <= 20) {
            $soundAlert = true;
            $calledNumber = $inStudio->queue_number;
        }

        return response()->json([
            'today' => $today,
            'in_studio' => $inStudio ? [
                'queue_number' => $inStudio->queue_number,
                'customer_name' => $inStudio->user->name,
                'package_name' => $inStudio->package->name,
                'backdrop' => $inStudio->studio_backdrop,
                'called_at' => $inStudio->called_at?->toIso8601String(),
            ] : null,
            'next_queue' => $nextQueue ? [
                'queue_number' => $nextQueue->queue_number,
                'customer_name' => $nextQueue->user->name,
                'package_name' => $nextQueue->package->name,
            ] : null,
            'sound_alert' => $soundAlert,
            'called_number' => $calledNumber,
            'waiting_count' => $activeQueues->whereIn('status', ['waiting_verification', 'confirmed', 'checked_in'])->count(),
            'active_queues' => $activeQueues->map(fn ($b) => [
                'id' => $b->id,
                'queue_number' => $b->queue_number,
                'customer_name' => $b->user->name,
                'status' => $b->status,
                'status_label' => $b->status_label,
                'status_badge' => $b->status_badge_class,
                'time' => $b->schedule?->formatted_time,
            ]),
        ]);
    }
}
