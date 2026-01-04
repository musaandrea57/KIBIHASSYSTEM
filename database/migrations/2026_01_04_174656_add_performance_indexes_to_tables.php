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
        // Students Table Indexes
        try {
            Schema::table('students', function (Blueprint $table) {
                // Individual try-catch blocks or checks would be ideal but verbose.
                // We'll try to add them; if one exists, it might fail the whole batch in some DBs.
                // Best effort: Add one by one in separate schema calls to isolate failures.
            });
            
            $this->safeAddIndex('students', 'current_nta_level');
            $this->safeAddIndex('students', 'status');
            $this->safeAddIndex('students', 'created_at');
            $this->safeAddIndex('students', ['program_id', 'current_nta_level'], 'students_program_nta_index');

            // Module Results Table Indexes
            $this->safeAddIndex('module_results', 'published_at');
            $this->safeAddIndex('module_results', ['academic_year_id', 'semester_id'], 'module_results_year_semester_index');
            $this->safeAddIndex('module_results', ['student_id', 'academic_year_id', 'semester_id'], 'module_results_student_year_sem_index');
            $this->safeAddIndex('module_results', 'grade_point');
            
            // Programs Table Indexes
            $this->safeAddIndex('programs', 'department_id');

        } catch (\Exception $e) {
            // Log or ignore
        }
    }

    protected function safeAddIndex($table, $columns, $name = null)
    {
        try {
            Schema::table($table, function (Blueprint $table) use ($columns, $name) {
                $table->index($columns, $name);
            });
        } catch (\Exception $e) {
            // Index likely exists
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('students', function (Blueprint $table) {
                $table->dropIndex(['current_nta_level']);
                $table->dropIndex(['status']);
                $table->dropIndex(['created_at']);
                $table->dropIndex('students_program_nta_index');
            });

            Schema::table('module_results', function (Blueprint $table) {
                $table->dropIndex(['published_at']);
                $table->dropIndex('module_results_year_semester_index');
                $table->dropIndex('module_results_student_year_sem_index');
                $table->dropIndex(['grade_point']);
            });
            
            Schema::table('programs', function (Blueprint $table) {
                 $table->dropIndex(['department_id']);
            });
        } catch (\Exception $e) {
            // Ignore if index doesn't exist
        }
    }
};
