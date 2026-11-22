<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('prints_included')->nullable()->default('2 Strip + Soft File')->after('price');
            $table->string('operational_hours')->nullable()->default('Buka 09.00 — 23.00 WIB')->after('prints_included');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['prints_included', 'operational_hours']);
        });
    }
};
