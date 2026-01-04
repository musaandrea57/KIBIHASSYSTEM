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
        Schema::table('academic_years', function (Blueprint $table) {
            $table->boolean('coursework_clearance_required')->default(true);
        });

        Schema::table('module_offerings', function (Blueprint $table) {
            $table->boolean('coursework_released')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn('coursework_clearance_required');
        });

        Schema::table('module_offerings', function (Blueprint $table) {
            $table->dropColumn('coursework_released');
        });
    }
};
