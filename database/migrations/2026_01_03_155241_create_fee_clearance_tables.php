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
        Schema::create('fee_clearance_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->enum('status', ['cleared', 'not_cleared', 'overridden'])->default('not_cleared');
            $table->decimal('total_invoiced', 12, 2)->default(0);
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->decimal('outstanding_balance', 12, 2)->default(0);
            $table->timestamp('last_calculated_at')->useCurrent();
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id', 'semester_id'], 'fcs_unique_context');
        });

        Schema::create('fee_clearance_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->foreignId('semester_id')->constrained('semesters');
            
            $table->text('reason');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->foreignId('granted_by')->constrained('users');
            $table->foreignId('revoked_by')->nullable()->constrained('users');
            $table->timestamp('revoked_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_clearance_overrides');
        Schema::dropIfExists('fee_clearance_statuses');
    }
};
