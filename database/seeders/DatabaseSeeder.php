<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (Pelanggan, Kasir, Admin)
        $customer = User::updateOrCreate(
            ['email' => 'hananaeylaa@gmail.com'],
            [
                'name' => 'Hana Naila Rahmadina',
                'phone_whatsapp' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]
        );

        $cashier = User::updateOrCreate(
            ['email' => 'cashier@kothakphoto.id'],
            [
                'name' => 'Raka Pratama (Kasir)',
                'phone_whatsapp' => '08132674377',
                'password' => Hash::make('password'),
                'role' => 'cashier',
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@kothakphoto.id'],
            [
                'name' => 'Admin Kothak Studio',
                'phone_whatsapp' => '08132674377',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Seed Packages
        $pkg1 = Package::create([
            'name' => 'Kothak Solo / Duo Express',
            'description' => 'Sesi photobox 10 menit, 2 strip cetak 2R, soft file HD via QR.',
            'duration' => 10,
            'price' => 25000,
            'is_active' => true,
        ]);

        $pkg2 = Package::create([
            'name' => 'Kothak Signature Box (2-4 Orang)',
            'description' => 'Sesi photobox 15 menit, 2 cetak 4R, soft file HD + GIF.',
            'duration' => 15,
            'price' => 35000,
            'is_active' => true,
        ]);

        $pkg3 = Package::create([
            'name' => 'Warm Minimalist Portrait Session',
            'description' => 'Sesi 15 menit di Background Warm Cream Minimalist + cetak 4R.',
            'duration' => 15,
            'price' => 35000,
            'is_active' => true,
        ]);

        $pkg4 = Package::create([
            'name' => 'Bestie Squad Frame (4-6 Orang)',
            'description' => 'Sesi 20 menit untuk 4-6 orang, 4 cetak strip 4R + semua soft file.',
            'duration' => 20,
            'price' => 50000,
            'is_active' => true,
        ]);

        $pkg5 = Package::create([
            'name' => 'Kopi & Frame Couple Bundle',
            'description' => 'Sesi Signature Photobox 15 menit + 2 Iced Signature Coffee Kothak.',
            'duration' => 15,
            'price' => 65000,
            'is_active' => true,
        ]);

        // 3. Seed Schedules (Hari ini & Besok)
        $today = Carbon::today()->toDateString();
        $tomorrow = Carbon::tomorrow()->toDateString();

        $slots = [
            ['start' => '13:00:00', 'end' => '13:30:00', 'capacity' => 3],
            ['start' => '14:00:00', 'end' => '14:30:00', 'capacity' => 3],
            ['start' => '15:00:00', 'end' => '15:30:00', 'capacity' => 3],
            ['start' => '16:00:00', 'end' => '16:30:00', 'capacity' => 3],
            ['start' => '17:00:00', 'end' => '17:30:00', 'capacity' => 3],
            ['start' => '19:00:00', 'end' => '19:30:00', 'capacity' => 3],
        ];

        $todaySchedules = [];
        foreach ($slots as $slot) {
            $todaySchedules[] = Schedule::create([
                'date' => $today,
                'start_time' => $slot['start'],
                'end_time' => $slot['end'],
                'capacity' => $slot['capacity'],
            ]);

            Schedule::create([
                'date' => $tomorrow,
                'start_time' => $slot['start'],
                'end_time' => $slot['end'],
                'capacity' => $slot['capacity'],
            ]);
        }

        // 4. Seed Sample Bookings & Payments for demo
        $b1 = Booking::create([
            'user_id' => $customer->id,
            'package_id' => $pkg2->id,
            'schedule_id' => $todaySchedules[0]->id,
            'booking_date' => Carbon::now()->subHours(2),
            'status' => 'completed',
            'queue_number' => 'KTH-001',
            'studio_backdrop' => 'Electric Blue Backdrop',
            'whatsapp_sent_at' => Carbon::now()->subHours(2),
            'checked_in_at' => Carbon::now()->subHours(1),
        ]);
        Payment::create([
            'booking_id' => $b1->id,
            'proof_image_path' => 'uploads/payments/demo_qris_sample.png',
            'payment_status' => 'verified',
            'verified_by' => $cashier->id,
            'verified_at' => Carbon::now()->subHours(2),
        ]);

        $b2 = Booking::create([
            'user_id' => $customer->id,
            'package_id' => $pkg3->id,
            'schedule_id' => $todaySchedules[1]->id,
            'booking_date' => Carbon::now()->subHour(),
            'status' => 'in_studio',
            'queue_number' => 'KTH-002',
            'studio_backdrop' => 'Warm Cream Minimalist',
            'whatsapp_sent_at' => Carbon::now()->subHour(),
            'checked_in_at' => Carbon::now()->subMinutes(30),
        ]);
        Payment::create([
            'booking_id' => $b2->id,
            'proof_image_path' => 'uploads/payments/demo_qris_sample.png',
            'payment_status' => 'verified',
            'verified_by' => $cashier->id,
            'verified_at' => Carbon::now()->subHour(),
        ]);

        $b3 = Booking::create([
            'user_id' => $customer->id,
            'package_id' => $pkg5->id,
            'schedule_id' => $todaySchedules[2]->id,
            'booking_date' => Carbon::now()->subMinutes(30),
            'status' => 'checked_in',
            'queue_number' => 'KTH-003',
            'studio_backdrop' => 'Electric Blue Backdrop',
            'whatsapp_sent_at' => Carbon::now()->subMinutes(30),
            'checked_in_at' => Carbon::now()->subMinutes(10),
        ]);
        Payment::create([
            'booking_id' => $b3->id,
            'proof_image_path' => 'uploads/payments/demo_qris_sample.png',
            'payment_status' => 'verified',
            'verified_by' => $cashier->id,
            'verified_at' => Carbon::now()->subMinutes(25),
        ]);

        $b4 = Booking::create([
            'user_id' => $customer->id,
            'package_id' => $pkg4->id,
            'schedule_id' => $todaySchedules[3]->id,
            'booking_date' => Carbon::now()->subMinutes(15),
            'status' => 'waiting_verification',
            'queue_number' => 'KTH-004',
            'studio_backdrop' => 'Electric Blue Backdrop',
        ]);
        Payment::create([
            'booking_id' => $b4->id,
            'proof_image_path' => 'uploads/payments/demo_qris_sample.png',
            'payment_status' => 'pending',
        ]);
    }
}
