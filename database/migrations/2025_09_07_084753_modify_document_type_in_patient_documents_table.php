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
        Schema::table('patient_documents', function (Blueprint $table) {
            // Change document_type from enum to string to allow free text
            $table->string('document_type', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_documents', function (Blueprint $table) {
            // Revert back to enum if needed
            $table->enum('document_type', ['lab_report', 'scan', 'prescription', 'referral', 'other'])->change();
        });
    }
};
