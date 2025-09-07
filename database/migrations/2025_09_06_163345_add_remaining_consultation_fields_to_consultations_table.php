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
        Schema::table('consultations', function (Blueprint $table) {
            // Add only the fields that don't exist yet
            if (!Schema::hasColumn('consultations', 'chief_complaint')) {
                $table->string('chief_complaint')->nullable()->after('symptoms');
            }
            if (!Schema::hasColumn('consultations', 'duration')) {
                $table->enum('duration', ['acute', 'subacute', 'chronic'])->nullable()->after('chief_complaint');
            }
            if (!Schema::hasColumn('consultations', 'vital_signs')) {
                $table->text('vital_signs')->nullable()->after('duration');
            }
            if (!Schema::hasColumn('consultations', 'physical_findings')) {
                $table->text('physical_findings')->nullable()->after('vital_signs');
            }
            if (!Schema::hasColumn('consultations', 'primary_diagnosis')) {
                $table->string('primary_diagnosis')->nullable()->after('physical_findings');
            }
            if (!Schema::hasColumn('consultations', 'secondary_diagnosis')) {
                $table->string('secondary_diagnosis')->nullable()->after('primary_diagnosis');
            }
            if (!Schema::hasColumn('consultations', 'medications')) {
                $table->text('medications')->nullable()->after('treatment_plan');
            }
            if (!Schema::hasColumn('consultations', 'follow_up_date')) {
                $table->date('follow_up_date')->nullable()->after('medications');
            }
            if (!Schema::hasColumn('consultations', 'consultation_started_at')) {
                $table->timestamp('consultation_started_at')->nullable()->after('consultation_date');
            }
            if (!Schema::hasColumn('consultations', 'consultation_completed_at')) {
                $table->timestamp('consultation_completed_at')->nullable()->after('consultation_started_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn([
                'chief_complaint',
                'duration',
                'vital_signs',
                'physical_findings',
                'primary_diagnosis',
                'secondary_diagnosis',
                'medications',
                'follow_up_date',
                'consultation_started_at',
                'consultation_completed_at'
            ]);
        });
    }
};
