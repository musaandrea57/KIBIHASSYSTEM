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
        // Drop existing results table if exists to replace with module_results
        Schema::dropIfExists('results');

        // A) Grade scales
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->integer('min_mark');
            $table->integer('max_mark');
            $table->string('grade'); // A, B, C...
            $table->decimal('grade_point', 3, 1); // 4.0
            $table->string('definition'); // Excellent, Good...
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // B) Assessment types
        Schema::create('assessment_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // CW, SE, TEST1...
            $table->string('name'); // Test, Assignment, Quiz
            $table->string('description')->nullable();
            $table->decimal('max_mark', 5, 2)->default(100);
            $table->decimal('weight', 5, 2)->default(0); // Optional default weight
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // C) Module results
        Schema::create('module_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            
            // Link to module offering if it exists, otherwise link to module + context
            // Assuming module_offerings table exists from previous steps or we use direct module link + academic context
            // The prompt mentions module_offering_id. Let's check if module_offerings exists.
            // If not, we fall back to module_id + academic_year_id + semester_id.
            // Based on previous LS, `2026_01_03_103237_create_module_offerings_table.php` exists.
            $table->foreignId('module_offering_id')->nullable()->constrained('module_offerings');
            
            // We also store context directly for easier querying/indexing
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->foreignId('semester_id')->constrained('semesters');
            
            $table->decimal('credits_snapshot', 4, 1)->default(0);
            
            $table->decimal('cw_mark', 5, 2)->nullable();
            $table->decimal('se_mark', 5, 2)->nullable();
            $table->decimal('total_mark', 5, 2)->nullable();
            
            $table->string('grade')->nullable();
            $table->decimal('grade_point', 3, 2)->nullable();
            $table->decimal('points', 6, 2)->nullable(); // credits * grade_point
            $table->decimal('gpa_contribution', 6, 3)->nullable();
            
            $table->string('remark')->nullable();
            $table->string('flags')->nullable(); // *, **
            
            $table->enum('status', ['draft', 'pending_admin_approval', 'published'])->default('draft');
            
            $table->foreignId('uploaded_by')->nullable()->constrained('users'); // Teacher
            $table->foreignId('approved_by')->nullable()->constrained('users'); // Admin
            $table->foreignId('published_by')->nullable()->constrained('users'); // Admin
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();

            $table->unique(['student_id', 'module_offering_id'], 'mod_res_stud_offering_unique');
        });

        // D) CA detail entries
        Schema::create('continuous_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_result_id')->constrained('module_results')->cascadeOnDelete();
            $table->foreignId('assessment_type_id')->constrained('assessment_types');
            $table->decimal('mark', 5, 2);
            $table->decimal('max_mark', 5, 2);
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        // E) Academic PDF results uploads
        Schema::create('official_result_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs');
            $table->integer('nta_level');
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->string('file_path');
            $table->string('original_filename');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->enum('status', ['pending_admin_approval', 'approved', 'rejected', 'published'])->default('pending_admin_approval');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // F) Transcripts
        Schema::create('transcripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years'); // If specific year
            $table->string('file_path');
            $table->foreignId('generated_by')->constrained('users');
            $table->timestamp('generated_at');
            $table->string('version')->default('1.0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transcripts');
        Schema::dropIfExists('official_result_uploads');
        Schema::dropIfExists('continuous_assessments');
        Schema::dropIfExists('module_results');
        Schema::dropIfExists('assessment_types');
        Schema::dropIfExists('grade_scales');
    }
};
