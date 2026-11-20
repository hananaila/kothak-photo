<?php

namespace Tests\Feature;

use App\Models\AddOn;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Schedule;
use App\Models\User;
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
            'name' => 'Kothak Signature Box (2-4 Orang)',
            'description' => 'Sesi photobox 15 menit',
            'duration' => 15,
            'max_participants' => 4,
            'price' => 35000,
            'badge' => 'BEST SELLER',
            'category' => 'GROUP',
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

    public function test_catalog_and_smart_queue_display_render_successfully(): void
    {
        $response = $this->get(route('packages.index'));
        $response->assertStatus(200);
        $response->assertSee('KOTHaK');
        $response->assertSee('Kothak Signature Box');

        $display = $this->get(route('queue.display'));
        $display->assertStatus(200);
        $display->assertSee('Layar Antrean Studio');
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
            'status' => 'waiting_verification',
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

    public function test_rbac_prevents_customer_from_accessing_admin_routes(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }
}
