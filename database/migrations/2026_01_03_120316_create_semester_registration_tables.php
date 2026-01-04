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
        // A) Registration rules
        Schema::create('programme_level_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->integer('nta_level'); // 4, 5, 6
            $table->integer('min_credits');
            $table->integer('max_credits');
            $table->timestamps();

            $table->unique(['program_id', 'nta_level']);
        });

        // B) Registration deadlines
        Schema::create('semester_registration_deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            // Only one active deadline per semester/year (soft rule, but let's assume one record per pair)
            // Or maybe just index them. The user said "Only one active deadline per semester/year"
            // I'll add a unique constraint to ensure data integrity
            $table->unique(['academic_year_id', 'semester_id'], 'sem_reg_deadlines_unique');
        });

        // C) Semester registrations
        Schema::create('semester_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->integer('nta_level'); // snapshot
            $table->foreignId('program_id')->constrained('programs'); // snapshot
            $table->string('status')->default('draft'); // draft, submitted, approved, rejected
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id', 'semester_id'], 'unique_sem_reg');
        });

        // D) Registered modules
        Schema::create('semester_registration_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('semester_registration_id')->constrained('semester_registrations')->cascadeOnDelete();
            $table->foreignId('module_offering_id')->constrained('module_offerings');
            $table->integer('credits_snapshot');
            $table->timestamps();

            $table->unique(['semester_registration_id', 'module_offering_id'], 'unique_reg_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('semester_registration_items');
        Schema::dropIfExists('semester_registrations');
        Schema::dropIfExists('semester_registration_deadlines');
        Schema::dropIfExists('programme_level_rules');
    }
};
