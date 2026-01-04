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
        // --- SMS Tables ---

        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // e.g., 'fees_reminder', 'results_published'
            $table->string('name');
            $table->text('message_body');
            $table->json('variables')->nullable(); // e.g., ['name', 'balance']
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sms_batches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('filters')->nullable(); // Snapshot of filters used
            $table->integer('total_messages')->default(0);
            $table->integer('success_count')->default(0);
            $table->integer('failure_count')->default(0);
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('sms_messages', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_type')->default('custom'); // student, staff, custom
            $table->unsignedBigInteger('recipient_id')->nullable();
            $table->string('phone_number');
            $table->text('message_body');
            $table->string('template_key')->nullable();
            $table->string('status')->default('queued'); // queued, sent, failed
            $table->timestamp('sent_at')->nullable();
            $table->string('failure_reason')->nullable();
            $table->foreignId('sms_batch_id')->nullable()->constrained('sms_batches')->cascadeOnDelete();
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('meta')->nullable(); // For idempotency hashes, provider IDs
            $table->timestamps();

            $table->index(['recipient_type', 'recipient_id']);
            $table->index('status');
        });

        // --- Evaluation System Tables ---

        Schema::create('evaluation_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('evaluation_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_template_id')->constrained('evaluation_templates')->cascadeOnDelete();
            $table->text('question_text');
            $table->string('type')->default('likert'); // likert, text
            $table->integer('order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();
        });

        Schema::create('evaluation_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // e.g., "Semester 1 2025/2026 Evaluation"
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_open')->default(false);
            $table->timestamps();
        });

        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_period_id')->constrained('evaluation_periods')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('module_offering_id')->constrained('module_offerings')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete(); // The teacher being evaluated
            $table->string('status')->default('pending'); // pending, submitted
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Ensure one evaluation per student per module per period
            $table->unique(['evaluation_period_id', 'student_id', 'module_offering_id'], 'unique_student_module_evaluation');
        });

        Schema::create('evaluation_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->cascadeOnDelete();
            $table->foreignId('evaluation_question_id')->constrained('evaluation_questions')->cascadeOnDelete();
            $table->integer('rating')->nullable(); // 1-5 for likert
            $table->text('comment')->nullable(); // for text
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_answers');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('evaluation_periods');
        Schema::dropIfExists('evaluation_questions');
        Schema::dropIfExists('evaluation_templates');
        Schema::dropIfExists('sms_messages');
        Schema::dropIfExists('sms_batches');
        Schema::dropIfExists('sms_templates');
    }
};
