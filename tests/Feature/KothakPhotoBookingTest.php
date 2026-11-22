<?php

namespace Tests\Feature;

use App\Models\AddOn;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\User;
use App\Services\SmartQueueService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class KothakPhotoBookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $cashier;

    protected User $customer;

    protected Package $package;

    protected Schedule $schedule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Studio',
            'email' => 'admin@kothakphoto.id',
            'phone_whatsapp' => '08132674377',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->cashier = User::create([
            'name' => 'Kasir Raka',
            'email' => 'cashier@kothakphoto.id',
            'phone_whatsapp' => '08132674378',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'is_active' => true,
        ]);

        $this->customer = User::create([
            'name' => 'Hana Naila',
            'email' => 'hananaeylaa@gmail.com',
            'phone_whatsapp' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'is_active' => true,
        ]);

        $this->package = Package::create([
            'name' => 'Flower Garden Booth',
            'description' => 'Sesi photobox 15 menit',
            'duration' => 15,
            'max_participants' => 4,
            'price' => 35000,
            'prints_included' => '2 Strip + Soft File',
            'operational_hours' => 'Buka 09.00 — 23.00 WIB',
            'badge' => 'FAVORIT',
            'category' => 'BOOTH',
            'is_active' => true,
        ]);

        Package::create([
            'name' => 'Lift Booth',
            'description' => 'Konsep modern elevator',
            'duration' => 15,
            'max_participants' => 4,
            'price' => 35000,
            'prints_included' => '2 Strip + Soft File',
            'operational_hours' => 'Buka 09.00 — 23.00 WIB',
            'badge' => 'POPULER',
            'category' => 'BOOTH',
            'is_active' => true,
        ]);

        $this->schedule = Schedule::create([
            'date' => Carbon::today()->toDateString(),
            'start_time' => '13:00:00',
            'end_time' => '13:30:00',
            'capacity' => 2, // set low capacity to test double booking
            'is_active' => true,
        ]);
    }

    public function test_catalog_and_customer_history_render_successfully(): void
    {
        $response = $this->get(route('packages.index'));
        $response->assertStatus(200);
        $response->assertSee('KOTHaK');
        $response->assertSee('Flower Garden Booth');
        $response->assertSee('Lift Booth');

        $history = $this->get(route('booking.history'));
        $history->assertStatus(200);
        $history->assertSee('Tiket Saya');
    }

    public function test_customer_can_book_and_generates_queue_number_and_checkout_redirect(): void
    {
        $addOn = AddOn::create([
            'name' => 'Extra Soft File Drive',
            'description' => 'HD link',
            'price' => 15000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->customer)->post(route('booking.store'), [
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'studio_backdrop' => 'Electric Blue Backdrop',
            'participants_count' => 3,
            'add_ons' => [$addOn->id],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'total_price' => 50000, // 35000 + 15000
            'status' => 'confirmed',
        ]);

        $booking = Booking::where('user_id', $this->customer->id)->first();
        $this->assertNotNull($booking);
        $this->assertStringStartsWith('KTH-', $booking->queue_number);
    }

    public function test_anti_double_booking_prevents_overbooking_when_slot_capacity_is_reached(): void
    {
        // Schedule capacity is 2
        // Booking 1
        Booking::create([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => Carbon::now(),
            'booking_type' => 'online',
            'participants_count' => 2,
            'status' => 'confirmed',
            'queue_number' => 'KTH-001',
            'studio_backdrop' => 'Electric Blue Backdrop',
            'package_price' => 35000,
            'total_price' => 35000,
        ]);

        // Booking 2 (reaches capacity 2 of 2)
        Booking::create([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => Carbon::now(),
            'booking_type' => 'online',
            'participants_count' => 2,
            'status' => 'waiting_verification',
            'queue_number' => 'KTH-002',
            'studio_backdrop' => 'Electric Blue Backdrop',
            'package_price' => 35000,
            'total_price' => 35000,
        ]);

        // Attempting Booking 3 in the same full slot must fail!
        $response = $this->actingAs($this->customer)->post(route('booking.store'), [
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'studio_backdrop' => 'Warm Cream Minimalist',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(2, Booking::where('schedule_id', $this->schedule->id)->count());
    }

    public function test_cashier_can_verify_qris_payment_and_checkin_customer(): void
    {
        $booking = Booking::create([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => Carbon::now(),
            'status' => 'waiting_verification',
            'queue_number' => 'KTH-001',
            'studio_backdrop' => 'Electric Blue Backdrop',
            'package_price' => 35000,
            'total_price' => 35000,
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'amount' => 35000,
            'payment_method' => 'qris_static',
            'proof_image_path' => 'uploads/payments/demo_qris_sample.png',
            'payment_status' => 'pending',
        ]);

        // Cashier accepts payment
        $response = $this->actingAs($this->cashier)->post(route('cashier.verify', $booking->id), [
            'decision' => 'accept',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('confirmed', $booking->fresh()->status);
        $this->assertEquals('verified', $booking->payment->fresh()->payment_status);

        // Cashier checks in customer
        $checkinResponse = $this->actingAs($this->cashier)->post(route('cashier.checkin', $booking->id));
        $checkinResponse->assertSessionHas('success');
        $this->assertEquals('checked_in', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->checked_in_at);
    }

    public function test_cashier_can_call_next_queue_into_studio(): void
    {
        $booking = Booking::create([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => Carbon::now(),
            'status' => 'checked_in',
            'queue_number' => 'KTH-001',
            'studio_backdrop' => 'Electric Blue Backdrop',
            'package_price' => 35000,
            'total_price' => 35000,
            'checked_in_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($this->cashier)->post(route('cashier.call-next'));
        $response->assertSessionHas('success');

        $this->assertEquals('in_studio', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->called_at);
    }

    public function test_admin_dashboard_and_csv_report_generation(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Analitik', false);
        $response->assertSee('Pelaporan', false);

        $csvResponse = $this->actingAs($this->admin)->get(route('admin.reports.csv'));
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', $csvResponse->headers->get('Content-Type'));
    }

    public function test_admin_and_cashier_can_access_users_management(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(200);

        $responseCashier = $this->actingAs($this->cashier)->get(route('admin.users.index'));
        $responseCashier->assertStatus(200);
    }

    public function test_rbac_prevents_customer_from_accessing_admin_routes(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_booth_queue_number_prefix_per_booth(): void
    {
        $this->schedule->update(['capacity' => 10]);

        // 1. Flower Garden Booth -> F-001
        $this->actingAs($this->customer)->post(route('booking.store'), [
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'studio_backdrop' => 'Flower Garden Booth',
        ]);
        $bookingF = Booking::where('studio_backdrop', 'Flower Garden Booth')->latest()->first();
        $this->assertNotNull($bookingF);
        $this->assertStringStartsWith('F-', $bookingF->queue_number);
        $bookingF->update(['status' => 'completed']);

        // 2. Lift Booth -> L-001
        $this->actingAs($this->customer)->post(route('booking.store'), [
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'studio_backdrop' => 'Lift Booth',
        ]);
        $bookingL = Booking::where('studio_backdrop', 'Lift Booth')->latest()->first();
        $this->assertNotNull($bookingL);
        $this->assertStringStartsWith('L-', $bookingL->queue_number);
        $bookingL->update(['status' => 'completed']);

        // 3. Vintage Booth -> V-001
        $this->actingAs($this->customer)->post(route('booking.store'), [
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'studio_backdrop' => 'Vintage Booth',
        ]);
        $bookingV = Booking::where('studio_backdrop', 'Vintage Booth')->latest()->first();
        $this->assertNotNull($bookingV);
        $this->assertStringStartsWith('V-', $bookingV->queue_number);
    }

    public function test_customer_can_view_stulokal_style_ticket(): void
    {
        $booking = Booking::create([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => Carbon::now(),
            'status' => 'confirmed',
            'queue_number' => 'F-001',
            'studio_backdrop' => 'Flower Garden Booth',
            'package_price' => 35000,
            'total_price' => 35000,
        ]);

        $response = $this->actingAs($this->customer)->get(route('booking.ticket', $booking->id));
        $response->assertStatus(200);
        $response->assertSee('AHEAD OF YOU', false);
        $response->assertSee('YOUR TICKET NUMBER', false);
        $response->assertSee($booking->queue_number, false);
        $response->assertSee('Waktu Booking', false);
    }

    public function test_continuous_queue_allows_unlimited_bookings_in_sequence(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $response = $this->flushSession()->post(route('booking.store'), [
                'name' => "Pelanggan {$i}",
                'phone_whatsapp' => "08123456789{$i}",
                'studio_backdrop' => 'Flower Garden Booth',
            ]);
            $response->assertRedirect();
            $response->assertSessionMissing('error');
        }

        $this->assertEquals(5, Booking::where('studio_backdrop', 'Flower Garden Booth')->count());
    }

    public function test_customer_history_shows_tickets_from_this_browser_device_only(): void
    {
        // 1. Browser baru yang belum pernah booking: riwayat kosong
        $emptyResponse = $this->get(route('booking.history'));
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertSee('No active tickets');
        $emptyResponse->assertSee('Active (0)');
        $emptyResponse->assertSee('History (0)');

        // 2. Buat tiket aktif, tiket selesai, dan tiket tidak hadir
        $bActive = Booking::create([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => Carbon::today(),
            'booking_type' => 'online',
            'participants_count' => 2,
            'status' => 'confirmed',
            'queue_number' => 'V-007',
            'studio_backdrop' => 'Vintage Booth',
            'package_price' => 35000,
            'total_price' => 35000,
        ]);

        $bCompleted = Booking::create([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => Carbon::today(),
            'booking_type' => 'online',
            'participants_count' => 2,
            'status' => 'completed',
            'queue_number' => 'L-002',
            'studio_backdrop' => 'Lift Booth',
            'package_price' => 35000,
            'total_price' => 35000,
        ]);

        $bNoShow = Booking::create([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => Carbon::today(),
            'booking_type' => 'online',
            'participants_count' => 2,
            'status' => 'no_show',
            'queue_number' => 'F-003',
            'studio_backdrop' => 'Flower Garden Booth',
            'package_price' => 35000,
            'total_price' => 35000,
        ]);

        $recentIds = [$bActive->id, $bCompleted->id, $bNoShow->id];

        // 3. Tab 'active': hanya tiket yang sedang aktif
        $activeResponse = $this->withSession(['recent_bookings' => $recentIds])
            ->get(route('booking.history', ['tab' => 'active']));
        $activeResponse->assertStatus(200);
        $activeResponse->assertSee('V-007');
        $activeResponse->assertDontSee('L-002');
        $activeResponse->assertDontSee('F-003');

        // 4. Tab 'history': semua tiket yang sudah selesai dan tidak hadir
        $historyResponse = $this->withSession(['recent_bookings' => $recentIds])
            ->get(route('booking.history', ['tab' => 'history']));
        $historyResponse->assertStatus(200);
        $historyResponse->assertSee('L-002');
        $historyResponse->assertSee('SELESAI');
        $historyResponse->assertSee('F-003');
        $historyResponse->assertSee('TIDAK HADIR');
        $historyResponse->assertDontSee('V-007');
    }

    public function test_user_with_active_ticket_is_automatically_redirected_to_ticket(): void
    {
        $booking = Booking::create([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => Carbon::today(),
            'booking_type' => 'online',
            'participants_count' => 2,
            'status' => 'confirmed',
            'queue_number' => 'V-007',
            'studio_backdrop' => 'Vintage Booth',
            'package_price' => 35000,
            'total_price' => 35000,
        ]);

        // Pengunjung dengan tiket aktif langsung diarahkan ke tiketnya saat membuka link web
        $response = $this->withSession(['recent_bookings' => [$booking->id]])->get(route('packages.index'));
        $response->assertRedirect(route('booking.ticket', $booking->id));
    }

    public function test_user_cannot_create_another_ticket_before_current_session_is_completed(): void
    {
        $user = User::factory()->create([
            'email' => 'guest_08991234567@kothak.local',
            'phone_whatsapp' => '08991234567',
        ]);
        $booking = Booking::create([
            'user_id' => $user->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'booking_date' => Carbon::today(),
            'booking_type' => 'online',
            'participants_count' => 2,
            'status' => 'confirmed',
            'queue_number' => 'V-001',
            'studio_backdrop' => 'Vintage Booth',
            'package_price' => 35000,
            'total_price' => 35000,
        ]);

        // Mencoba booking lagi dengan nomor WA yang sama saat antrean masih berjalan
        $attempt = $this->withSession(['recent_bookings' => [$booking->id]])->post(route('booking.store'), [
            'name' => 'Budi',
            'phone_whatsapp' => '08991234567',
            'studio_backdrop' => 'Lift Booth',
        ]);

        // Harus ditolak dan dikembalikan ke tiket aktifnya
        $attempt->assertRedirect(route('booking.ticket', $booking->id));
        $attempt->assertSessionHas('info');
        $this->assertEquals(1, Booking::where('user_id', $user->id)->count());

        // Setelah sesi foto diselesaikan (status = completed)
        $booking->update(['status' => 'completed']);

        // Sekarang baru diperbolehkan booking sesi berikutnya
        $successAttempt = $this->withSession(['recent_bookings' => []])->post(route('booking.store'), [
            'name' => 'Budi',
            'phone_whatsapp' => '08991234567',
            'studio_backdrop' => 'Lift Booth',
        ]);

        $successAttempt->assertRedirect();
        $this->assertEquals(2, Booking::where('user_id', $user->id)->count());
    }

    public function test_admin_can_access_all_management_pages_without_errors(): void
    {
        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.packages.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.reports.print'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.reports.csv'))->assertOk();

        // Cashier can also access booth management, users & dashboard
        $this->actingAs($this->cashier)->get(route('admin.packages.index'))->assertOk();
        $this->actingAs($this->cashier)->get(route('admin.users.index'))->assertOk();
        $this->actingAs($this->cashier)->get(route('admin.dashboard'))->assertOk();

        // Customer cannot login, only cashier and admin
        $customerLogin = $this->post(route('login'), [
            'email' => $this->customer->email,
            'password' => 'password',
        ]);
        $customerLogin->assertSessionHasErrors('email');

        $cashierLogin = $this->post(route('login'), [
            'email' => $this->cashier->email,
            'password' => 'password',
        ]);
        $cashierLogin->assertRedirect(route('cashier.dashboard'));
    }

    public function test_smart_queue_auto_advances_after_15_minutes(): void
    {
        $bookingInStudio = Booking::create([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'studio_backdrop' => 'Vintage Booth',
            'queue_number' => 'V-098',
            'status' => 'in_studio',
            'called_at' => Carbon::now()->subMinutes(16),
            'total_price' => 35000,
        ]);

        $bookingNext = Booking::create([
            'user_id' => $this->customer->id,
            'package_id' => $this->package->id,
            'schedule_id' => $this->schedule->id,
            'studio_backdrop' => 'Vintage Booth',
            'queue_number' => 'V-099',
            'status' => 'confirmed',
            'total_price' => 35000,
        ]);

        $response = $this->actingAs($this->cashier)->get(route('cashier.dashboard'));
        $response->assertOk();

        // 1. Sesi lama otomatis completed
        $this->assertEquals('completed', $bookingInStudio->fresh()->status);
        // 2. Antrean berikutnya otomatis dipanggil ke tahap konfirmasi 30 detik ('calling')
        $this->assertEquals('calling', $bookingNext->fresh()->status);
        $this->assertNotNull($bookingNext->fresh()->called_at);

        // 3. Setelah 30 detik berlalu tanpa ditandai tidak hadir, otomatis masuk 'in_studio'
        $bookingNext->update(['called_at' => Carbon::now()->subSeconds(35)]);
        app(SmartQueueService::class)->autoAdvanceExpiredSessions();
        $this->assertEquals('in_studio', $bookingNext->fresh()->status);
    }
}
