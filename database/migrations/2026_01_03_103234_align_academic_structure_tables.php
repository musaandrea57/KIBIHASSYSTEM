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
        // Programs
        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('programs', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }
        });

        // Academic Years
        Schema::table('academic_years', function (Blueprint $table) {
            if (!Schema::hasColumn('academic_years', 'is_current')) {
                $table->boolean('is_current')->default(false);
            }
        });

        // Semesters
        Schema::table('semesters', function (Blueprint $table) {
            if (Schema::hasColumn('semesters', 'academic_year_id')) {
                // We need to drop the foreign key first if it exists
                // Note: The exact index name might vary, so we try standardized names or array syntax
                $table->dropForeign(['academic_year_id']);
                $table->dropColumn('academic_year_id');
            }
            if (!Schema::hasColumn('semesters', 'number')) {
                $table->integer('number')->after('name'); // 1 or 2
            }
        });

        // Modules
        Schema::table('modules', function (Blueprint $table) {
            if (Schema::hasColumn('modules', 'nta_level')) {
                $table->dropColumn('nta_level');
            }
            if (Schema::hasColumn('modules', 'semester_number')) {
                $table->dropColumn('semester_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['description', 'is_active']);
        });

        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn('is_current');
        });

        Schema::table('semesters', function (Blueprint $table) {
            $table->foreignId('academic_year_id')->nullable()->constrained()->cascadeOnDelete();
            $table->dropColumn('number');
        });

        Schema::table('modules', function (Blueprint $table) {
            $table->integer('nta_level')->nullable();
            $table->integer('semester_number')->nullable();
        });
    }
};
