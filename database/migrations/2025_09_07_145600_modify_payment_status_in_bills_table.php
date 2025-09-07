<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            // Change enum to allow unpaid and remove partial
            \DB::statement("ALTER TABLE bills MODIFY COLUMN payment_status ENUM('pending','paid','unpaid') NOT NULL DEFAULT 'pending'");
        });
    }

    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            // Revert to previous enum
            \DB::statement("ALTER TABLE bills MODIFY COLUMN payment_status ENUM('pending','partial','paid') NOT NULL DEFAULT 'pending'");
        });
    }
};


