<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel packages (Manajemen Paket Foto Studio)
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->text('description');
            $table->unsignedInteger('duration')->comment('Durasi sesi foto dalam menit');
            $table->decimal('price', 10, 2)->comment('Harga paket dalam IDR');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Tabel schedules (Jadwal & Kuota Slot Harian Studio)
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('capacity')->default(3)->comment('Kuota maksimal reservasi per slot');
            $table->timestamps();

            $table->unique(['date', 'start_time', 'end_time'], 'uq_schedule_slot');
        });

        // 3. Tabel bookings (Transaksi Reservasi & Nomor Antrean Pintar)
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('restrict')->onUpdate('cascade');
            $table->dateTime('booking_date')->useCurrent();
            $table->string('status', 40)->default('waiting_verification'); // waiting_verification, confirmed, checked_in, in_studio, completed, cancelled
            $table->string('queue_number', 20);
            $table->string('studio_backdrop', 80)->default('Electric Blue Backdrop');
            $table->dateTime('whatsapp_sent_at')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        // 4. Tabel payments (Bukti Pembayaran QRIS Statis & Verifikasi Kasir)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->onDelete('cascade')->onUpdate('cascade');
            $table->string('proof_image_path', 255)->comment('Path bukti transfer di public/uploads/payments/');
            $table->string('payment_status', 40)->default('pending'); // pending, verified, rejected
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null')->onUpdate('cascade');
            $table->dateTime('verified_at')->nullable();
            $table->string('rejection_note', 255)->nullable();
            $table->timestamps();

            $table->index('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('packages');
    }
};
