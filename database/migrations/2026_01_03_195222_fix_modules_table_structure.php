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
        Schema::table('modules', function (Blueprint $table) {
            if (!Schema::hasColumn('modules', 'nta_level')) {
                $table->integer('nta_level')->after('program_id'); // 4, 5, 6
            }
            if (!Schema::hasColumn('modules', 'semester_number')) {
                $table->integer('semester_number')->default(1)->after('nta_level'); // 1, 2
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['nta_level', 'semester_number']);
        });
    }
};
