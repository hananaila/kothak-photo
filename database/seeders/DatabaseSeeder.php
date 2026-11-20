<?php

namespace Database\Seeders;

use App\Models\AddOn;
use App\Models\Booking;
use App\Models\BookingAddOn;
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
        // 1. Seed Users (Pelanggan, Kasir, Admin, plus extra customers)
        $admin = User::updateOrCreate(
            ['email' => 'admin@kothakphoto.id'],
            [
                'name' => 'Admin Kothak Studio',
                'phone_whatsapp' => '08132674377',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $cashier = User::updateOrCreate(
            ['email' => 'cashier@kothakphoto.id'],
            [
                'name' => 'Raka Pratama (Kasir)',
                'phone_whatsapp' => '08132674378',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'is_active' => true,
            ]
        );

        $customer1 = User::updateOrCreate(
            ['email' => 'hananaeylaa@gmail.com'],
            [
                'name' => 'Hana Naila Rahmadina',
                'phone_whatsapp' => '081234567890',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
            ]
        );

        $customer2 = User::updateOrCreate(
            ['email' => 'dimas.pratama@gmail.com'],
            [
                'name' => 'Dimas Pratama',
                'phone_whatsapp' => '085712345678',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
            ]
        );

        $customer3 = User::updateOrCreate(
            ['email' => 'anisa.safitri@gmail.com'],
            [
                'name' => 'Anisa Safitri',
                'phone_whatsapp' => '087812345678',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
            ]
        );

        // 2. Seed Packages (FR-07)
        $pkg1 = Package::updateOrCreate(
            ['name' => 'Kothak Solo / Duo Express'],
            [
                'description' => 'Sesi photobox 10 menit, 2 strip cetak 2R, soft file HD via QR.',
                'duration' => 10,
                'max_participants' => 2,
                'price' => 25000,
                'badge' => 'HEMAT',
                'category' => 'SOLO_DUO',
                'is_active' => true,
            ]
        );

        $pkg2 = Package::updateOrCreate(
            ['name' => 'Kothak Signature Box (2-4 Orang)'],
            [
                'description' => 'Sesi photobox 15 menit, 2 cetak 4R, soft file HD + GIF animasi.',
                'duration' => 15,
                'max_participants' => 4,
                'price' => 35000,
                'badge' => 'BEST SELLER',
                'category' => 'GROUP',
                'is_active' => true,
            ]
        );

        $pkg3 = Package::updateOrCreate(
            ['name' => 'Warm Minimalist Portrait Session'],
            [
                'description' => 'Sesi 15 menit background Warm Cream Minimalist + cetak 4R eksklusif.',
                'duration' => 15,
                'max_participants' => 2,
                'price' => 35000,
                'badge' => 'FAVORIT',
                'category' => 'SOLO_DUO',
                'is_active' => true,
            ]
        );

        $pkg4 = Package::updateOrCreate(
            ['name' => 'Bestie Squad Frame (4-6 Orang)'],
            [
                'description' => 'Sesi 20 menit untuk 4-6 orang, 4 cetak strip 4R + semua soft file Google Drive.',
                'duration' => 20,
                'max_participants' => 6,
                'price' => 50000,
                'badge' => 'RAMAI',
                'category' => 'GROUP',
                'is_active' => true,
            ]
        );

        $pkg5 = Package::updateOrCreate(
            ['name' => 'Kopi & Frame Couple Bundle'],
            [
                'description' => 'Sesi Signature Photobox 15 menit + 2 Iced Signature Coffee Kothak Cafe.',
                'duration' => 15,
                'max_participants' => 2,
                'price' => 65000,
                'badge' => 'BUNDLE HEMAT',
                'category' => 'BUNDLING',
                'is_active' => true,
            ]
        );

        $pkg6 = Package::updateOrCreate(
            ['name' => 'Graduation & Special Moment (4-6 Orang)'],
            [
                'description' => 'Sesi 25 menit lengkap aksesoris toga & frame wisuda + cetak 6R + soft file HD.',
                'duration' => 25,
                'max_participants' => 6,
                'price' => 75000,
                'badge' => 'SPECIAL',
                'category' => 'GROUP',
                'is_active' => true,
            ]
        );

        // 3. Seed Add-Ons (FR-03)
        $addon1 = AddOn::updateOrCreate(
            ['name' => 'Extra Cetak Strip 4R (2 Lembar)'],
            [
                'description' => 'Tambahan 2 lembar cetak photo strip 4R kualitas lab.',
                'price' => 10000,
                'is_active' => true,
            ]
        );

        $addon2 = AddOn::updateOrCreate(
            ['name' => 'All Soft File HD via Google Drive'],
            [
                'description' => 'Seluruh foto mentah & hasil edit HD dikirimkan via link Google Drive.',
                'price' => 15000,
                'is_active' => true,
            ]
        );

        $addon3 = AddOn::updateOrCreate(
            ['name' => 'Extra Person (+1 Orang)'],
            [
                'description' => 'Biaya tambahan per orang melebihi kapasitas standar paket.',
                'price' => 10000,
                'is_active' => true,
            ]
        );

        $addon4 = AddOn::updateOrCreate(
            ['name' => 'Sewa Properti & Aksesoris Khusus'],
            [
                'description' => 'Akses kacamata unik, bando korea, topi karakter, dan buket bunga.',
                'price' => 10000,
                'is_active' => true,
            ]
        );

        // 4. Seed Schedules (Hari ini, besok, dan 7 hari ke belakang untuk analitik)
        $today = Carbon::today();
        $slotTimes = [
            ['10:00:00', '10:30:00'],
            ['11:00:00', '11:30:00'],
            ['13:00:00', '13:30:00'],
            ['14:00:00', '14:30:00'],
            ['15:00:00', '15:30:00'],
            ['16:00:00', '16:30:00'],
            ['17:00:00', '17:30:00'],
            ['19:00:00', '19:30:00'],
            ['20:00:00', '20:30:00'],
        ];

        // Seed slots for past 7 days (for historical charts)
        for ($i = 7; $i >= 1; $i--) {
            $pastDate = $today->copy()->subDays($i);
            foreach ($slotTimes as $st) {
                Schedule::firstOrCreate(
                    ['date' => $pastDate->toDateString(), 'start_time' => $st[0], 'end_time' => $st[1]],
                    ['capacity' => 3, 'is_active' => true]
                );
            }
        }

        // Seed slots for Today and Tomorrow
        $todaySchedules = [];
        foreach ($slotTimes as $st) {
            $todaySchedules[] = Schedule::firstOrCreate(
                ['date' => $today->toDateString(), 'start_time' => $st[0], 'end_time' => $st[1]],
                ['capacity' => 3, 'is_active' => true]
            );

            Schedule::firstOrCreate(
                ['date' => $today->copy()->addDay()->toDateString(), 'start_time' => $st[0], 'end_time' => $st[1]],
                ['capacity' => 3, 'is_active' => true]
            );
        }

        // 5. Seed Historical Bookings (Past 7 Days) for Chart.js Revenue & Popularity Data
        $packagesList = [$pkg1, $pkg2, $pkg3, $pkg4, $pkg5, $pkg6];
        $usersList = [$customer1, $customer2, $customer3];

        for ($i = 6; $i >= 1; $i--) {
            $date = $today->copy()->subDays($i);
            $daySchedules = Schedule::whereDate('date', $date->toDateString())->get();

            // 3-5 bookings per day in the past
            $numBookings = rand(3, 5);
            for ($k = 1; $k <= $numBookings; $k++) {
                $sched = $daySchedules->random();
                $pkg = $packagesList[array_rand($packagesList)];
                $usr = $usersList[array_rand($usersList)];

                $qNum = sprintf('KTH-%s-%03d', $date->format('md'), $k);

                $booking = Booking::updateOrCreate(
                    ['queue_number' => $qNum],
                    [
                        'user_id' => $usr->id,
                        'package_id' => $pkg->id,
                        'schedule_id' => $sched->id,
                        'booking_date' => $date->copy()->setTime(12, $k * 10),
                        'booking_type' => ($k % 3 === 0) ? 'walk_in' : 'online',
                        'participants_count' => rand(1, $pkg->max_participants),
                        'studio_backdrop' => ($k % 2 === 0) ? 'Warm Cream Minimalist' : 'Electric Blue Backdrop',
                        'package_price' => $pkg->price,
                        'add_ons_total' => 0,
                        'total_price' => $pkg->price,
                        'status' => 'completed',
                        'checked_in_at' => $date->copy()->setTime(12, 15),
                        'called_at' => $date->copy()->setTime(12, 20),
                        'completed_at' => $date->copy()->setTime(12, 35),
                    ]
                );

                Payment::updateOrCreate(
                    ['booking_id' => $booking->id],
                    [
                        'amount' => $booking->total_price,
                        'payment_method' => 'qris_static',
                        'proof_image_path' => 'uploads/payments/demo_qris_sample.png',
                        'payment_status' => 'verified',
                        'verified_by' => $cashier->id,
                        'verified_at' => $date->copy()->setTime(12, 10),
                    ]
                );
            }
        }

        // 6. Seed Today's Active Queues (Live Demo)
        // KTH-001: Completed earlier today
        $b1 = Booking::updateOrCreate(
            ['queue_number' => 'KTH-001'],
            [
                'user_id' => $customer2->id,
                'package_id' => $pkg1->id,
                'schedule_id' => $todaySchedules[0]->id,
                'booking_date' => Carbon::now()->subHours(3),
                'booking_type' => 'online',
                'participants_count' => 2,
                'studio_backdrop' => 'Electric Blue Backdrop',
                'package_price' => $pkg1->price,
                'add_ons_total' => 0,
                'total_price' => $pkg1->price,
                'status' => 'completed',
                'whatsapp_sent_at' => Carbon::now()->subHours(3),
                'checked_in_at' => Carbon::now()->subHours(2)->subMinutes(50),
                'called_at' => Carbon::now()->subHours(2)->subMinutes(45),
                'completed_at' => Carbon::now()->subHours(2)->subMinutes(30),
            ]
        );
        Payment::updateOrCreate(
            ['booking_id' => $b1->id],
            [
                'amount' => $b1->total_price,
                'payment_method' => 'qris_static',
                'proof_image_path' => 'uploads/payments/demo_qris_sample.png',
                'payment_status' => 'verified',
                'verified_by' => $cashier->id,
                'verified_at' => Carbon::now()->subHours(3),
            ]
        );

        // KTH-002: In Studio right now!
        $b2 = Booking::updateOrCreate(
            ['queue_number' => 'KTH-002'],
            [
                'user_id' => $customer3->id,
                'package_id' => $pkg3->id,
                'schedule_id' => $todaySchedules[2]->id,
                'booking_date' => Carbon::now()->subHours(2),
                'booking_type' => 'online',
                'participants_count' => 2,
                'studio_backdrop' => 'Warm Cream Minimalist',
                'package_price' => $pkg3->price,
                'add_ons_total' => $addon1->price,
                'total_price' => $pkg3->price + $addon1->price,
                'status' => 'in_studio',
                'whatsapp_sent_at' => Carbon::now()->subHours(2),
                'checked_in_at' => Carbon::now()->subMinutes(35),
                'called_at' => Carbon::now()->subMinutes(10),
            ]
        );
        Payment::updateOrCreate(
            ['booking_id' => $b2->id],
            [
                'amount' => $b2->total_price,
                'payment_method' => 'qris_static',
                'proof_image_path' => 'uploads/payments/demo_qris_sample.png',
                'payment_status' => 'verified',
                'verified_by' => $cashier->id,
                'verified_at' => Carbon::now()->subHours(1)->subMinutes(45),
            ]
        );
        BookingAddOn::updateOrCreate(
            ['booking_id' => $b2->id, 'add_on_id' => $addon1->id],
            ['quantity' => 1, 'unit_price' => $addon1->price, 'subtotal' => $addon1->price]
        );

        // KTH-003: Checked-in, NEXT in line!
        $b3 = Booking::updateOrCreate(
            ['queue_number' => 'KTH-003'],
            [
                'user_id' => $customer1->id,
                'package_id' => $pkg2->id,
                'schedule_id' => $todaySchedules[3]->id,
                'booking_date' => Carbon::now()->subHour(),
                'booking_type' => 'online',
                'participants_count' => 4,
                'studio_backdrop' => 'Electric Blue Backdrop',
                'package_price' => $pkg2->price,
                'add_ons_total' => 0,
                'total_price' => $pkg2->price,
                'status' => 'checked_in',
                'whatsapp_sent_at' => Carbon::now()->subHour(),
                'checked_in_at' => Carbon::now()->subMinutes(15),
            ]
        );
        Payment::updateOrCreate(
            ['booking_id' => $b3->id],
            [
                'amount' => $b3->total_price,
                'payment_method' => 'qris_static',
                'proof_image_path' => 'uploads/payments/demo_qris_sample.png',
                'payment_status' => 'verified',
                'verified_by' => $cashier->id,
                'verified_at' => Carbon::now()->subMinutes(45),
            ]
        );

        // KTH-004: Confirmed (Payment verified, hasn't arrived/checked-in yet)
        $b4 = Booking::updateOrCreate(
            ['queue_number' => 'KTH-004'],
            [
                'user_id' => $customer1->id,
                'package_id' => $pkg5->id,
                'schedule_id' => $todaySchedules[4]->id,
                'booking_date' => Carbon::now()->subMinutes(45),
                'booking_type' => 'online',
                'participants_count' => 2,
                'studio_backdrop' => 'Electric Blue Backdrop',
                'package_price' => $pkg5->price,
                'add_ons_total' => 0,
                'total_price' => $pkg5->price,
                'status' => 'confirmed',
                'whatsapp_sent_at' => Carbon::now()->subMinutes(45),
            ]
        );
        Payment::updateOrCreate(
            ['booking_id' => $b4->id],
            [
                'amount' => $b4->total_price,
                'payment_method' => 'qris_static',
                'proof_image_path' => 'uploads/payments/demo_qris_sample.png',
                'payment_status' => 'verified',
                'verified_by' => $cashier->id,
                'verified_at' => Carbon::now()->subMinutes(30),
            ]
        );

        // KTH-005: Waiting verification (Uploaded proof, waiting for cashier to accept)
        $b5 = Booking::updateOrCreate(
            ['queue_number' => 'KTH-005'],
            [
                'user_id' => $customer2->id,
                'package_id' => $pkg4->id,
                'schedule_id' => $todaySchedules[5]->id,
                'booking_date' => Carbon::now()->subMinutes(20),
                'booking_type' => 'online',
                'participants_count' => 5,
                'studio_backdrop' => 'Warm Cream Minimalist',
                'package_price' => $pkg4->price,
                'add_ons_total' => $addon2->price,
                'total_price' => $pkg4->price + $addon2->price,
                'status' => 'waiting_verification',
                'whatsapp_sent_at' => Carbon::now()->subMinutes(20),
            ]
        );
        Payment::updateOrCreate(
            ['booking_id' => $b5->id],
            [
                'amount' => $b5->total_price,
                'payment_method' => 'qris_static',
                'proof_image_path' => 'uploads/payments/demo_qris_sample.png',
                'payment_status' => 'pending',
            ]
        );
        BookingAddOn::updateOrCreate(
            ['booking_id' => $b5->id, 'add_on_id' => $addon2->id],
            ['quantity' => 1, 'unit_price' => $addon2->price, 'subtotal' => $addon2->price]
        );
    }
}
