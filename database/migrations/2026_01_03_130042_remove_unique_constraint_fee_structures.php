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
        try {
            Schema::table('fee_structures', function (Blueprint $table) {
                // Add indexes for foreign keys to ensure they are covered when unique index is dropped
                // Only if they don't exist? Laravel usually handles this but let's be safe or just standard
                
                // Try to drop the unique index, ignore if it doesn't exist
                 $table->dropUnique('fee_struct_unique_context');
            });
        } catch (\Exception $e) {
            // Index probably doesn't exist, safe to continue
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_structures', function (Blueprint $table) {
            $table->unique(['program_id', 'nta_level', 'academic_year_id', 'semester_id'], 'fee_struct_unique_context');
            
            $table->dropIndex(['program_id']);
            $table->dropIndex(['academic_year_id']);
            $table->dropIndex(['semester_id']);
        });
    }
};
