<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'status')) {
                $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('is_published');
            }
        });

        // Migrate existing data
        DB::statement("UPDATE announcements SET status = 'published' WHERE is_published = 1");
        DB::statement("UPDATE announcements SET status = 'archived' WHERE is_archived = 1");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
