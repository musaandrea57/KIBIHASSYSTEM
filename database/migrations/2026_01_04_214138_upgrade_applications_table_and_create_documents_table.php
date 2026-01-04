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
        Schema::table('applications', function (Blueprint $table) {
            $table->string('application_number')->nullable()->change(); // Nullable for draft
            $table->string('status')->default('draft')->change(); // Default to draft
            $table->integer('current_step')->default(1);
            $table->foreignId('academic_year_id')->nullable()->constrained();
            $table->string('intake')->nullable(); // e.g. September
            $table->string('study_mode')->nullable(); // Full-time/Part-time
            $table->string('sponsorship')->nullable(); // Self/Government
            
            // Step 2 & 3 fields if not in biodata JSON (keeping structured is better for validation)
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->string('nationality')->nullable();
            $table->string('nin')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('marital_status')->nullable();
            
            // Address
            $table->string('permanent_address')->nullable();
            $table->string('current_address')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            
            // Emergency
            $table->json('emergency_contact')->nullable();
            
            // Health
            $table->string('nhif_card_number')->nullable();
            $table->boolean('has_disability')->default(false);
            $table->text('disability_details')->nullable();
            $table->text('medical_conditions')->nullable();

            $table->boolean('declaration_accepted')->default(false);
            $table->timestamp('submitted_at')->nullable();
        });

        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->morphs('documentable'); // e.g. Application, Student
            $table->string('type'); // birth_certificate, academic_transcript, etc.
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type');
            $table->integer('size_kb');
            $table->string('status')->default('pending'); // pending, verified, rejected
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
        
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn([
                'current_step', 'academic_year_id', 'intake', 'study_mode', 'sponsorship',
                'gender', 'dob', 'nationality', 'nin', 'passport_number', 'marital_status',
                'permanent_address', 'current_address', 'region', 'country',
                'emergency_contact', 'nhif_card_number', 'has_disability', 
                'disability_details', 'medical_conditions', 'declaration_accepted', 'submitted_at'
            ]);
            // Cannot easily revert nullable/default changes without raw SQL in some drivers, 
            // but standard Laravel allows changing back if needed.
        });
    }
};
