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
        // 1. Update users table: is_active
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });

        // 2. Update packages table: max_participants, badge, category
        Schema::table('packages', function (Blueprint $table) {
            if (! Schema::hasColumn('packages', 'max_participants')) {
                $table->unsignedInteger('max_participants')->default(2)->after('duration');
            }
            if (! Schema::hasColumn('packages', 'badge')) {
                $table->string('badge', 50)->nullable()->after('price');
            }
            if (! Schema::hasColumn('packages', 'category')) {
                $table->string('category', 50)->default('REGULAR')->after('badge');
            }
        });

        // 3. Update schedules table: is_active
        Schema::table('schedules', function (Blueprint $table) {
            if (! Schema::hasColumn('schedules', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('capacity');
            }
        });

        // 4. Create add_ons table (FR-03: Layanan Tambahan)
        if (! Schema::hasTable('add_ons')) {
            Schema::create('add_ons', function (Blueprint $table) {
                $table->id();
                $table->string('name', 120);
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 5. Update bookings table
        Schema::table('bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('bookings', 'booking_type')) {
                $table->string('booking_type', 20)->default('online')->after('booking_date'); // online, walk_in
            }
            if (! Schema::hasColumn('bookings', 'participants_count')) {
                $table->unsignedInteger('participants_count')->default(2)->after('booking_type');
            }
            if (! Schema::hasColumn('bookings', 'package_price')) {
                $table->decimal('package_price', 10, 2)->default(0)->after('studio_backdrop');
            }
            if (! Schema::hasColumn('bookings', 'add_ons_total')) {
                $table->decimal('add_ons_total', 10, 2)->default(0)->after('package_price');
            }
            if (! Schema::hasColumn('bookings', 'total_price')) {
                $table->decimal('total_price', 10, 2)->default(0)->after('add_ons_total');
            }
            if (! Schema::hasColumn('bookings', 'notes')) {
                $table->text('notes')->nullable()->after('total_price');
            }
            if (! Schema::hasColumn('bookings', 'called_at')) {
                $table->dateTime('called_at')->nullable()->after('checked_in_at');
            }
            if (! Schema::hasColumn('bookings', 'completed_at')) {
                $table->dateTime('completed_at')->nullable()->after('called_at');
            }
        });

        // 6. Create booking_add_ons pivot table
        if (! Schema::hasTable('booking_add_ons')) {
            Schema::create('booking_add_ons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade')->onUpdate('cascade');
                $table->foreignId('add_on_id')->constrained('add_ons')->onDelete('restrict')->onUpdate('cascade');
                $table->unsignedInteger('quantity')->default(1);
                $table->decimal('unit_price', 10, 2);
                $table->decimal('subtotal', 10, 2);
                $table->timestamps();

                $table->unique(['booking_id', 'add_on_id'], 'uq_booking_addon');
            });
        }

        // 7. Update payments table: amount, payment_method
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'amount')) {
                $table->decimal('amount', 10, 2)->default(0)->after('booking_id');
            }
            if (! Schema::hasColumn('payments', 'payment_method')) {
                $table->string('payment_method', 40)->default('qris_static')->after('amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_add_ons');
        Schema::dropIfExists('add_ons');

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['amount', 'payment_method']);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn([
                'booking_type',
                'participants_count',
                'package_price',
                'add_ons_total',
                'total_price',
                'notes',
                'called_at',
                'completed_at',
            ]);
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['max_participants', 'badge', 'category']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
