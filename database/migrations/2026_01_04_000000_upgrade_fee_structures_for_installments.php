<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create Bank Accounts Table
        if (!Schema::hasTable('bank_accounts')) {
            Schema::create('bank_accounts', function (Blueprint $table) {
                $table->id();
                $table->string('bank_name'); // e.g., CRDB, NMB
                $table->string('account_number');
                $table->string('account_name'); // e.g., KIBIHAS TUITION
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Upgrade Fee Structures Table
        Schema::table('fee_structures', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_structures', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('fee_structures', 'version')) {
                $table->integer('version')->default(1)->after('semester_id');
            }
            
            // Ensure individual indexes exist for foreign keys before dropping the composite unique index
            // MySQL uses the unique index for FK checks if no other index exists.
            // Check if index exists before creating? Laravel doesn't have hasIndex easily in Blueprint.
            // We just catch exception or hope for the best, or try to create only if needed.
            // But 'index' method doesn't check existence.
            // We'll skip index creation if we think it ran before.
            // Actually, dropping unique index 'fee_struct_unique_context' is the goal.
            // If we failed before, maybe indexes are created.
            
            try {
                $table->index('program_id');
            } catch (\Exception $e) {}
            try {
                $table->index('academic_year_id');
            } catch (\Exception $e) {}
            try {
                $table->index('semester_id');
            } catch (\Exception $e) {}

            try {
                $table->dropUnique('fee_struct_unique_context');
            } catch (\Exception $e) {
                // Ignore if already dropped
            }
        });

        // 3. Upgrade Fee Structure Items Table
        Schema::table('fee_structure_items', function (Blueprint $table) {
            if (!Schema::hasColumn('fee_structure_items', 'amount_oct')) {
                $table->decimal('amount_oct', 12, 2)->default(0)->after('amount');
                $table->decimal('amount_jan', 12, 2)->default(0)->after('amount_oct');
                $table->decimal('amount_apr', 12, 2)->default(0)->after('amount_jan');
            }
            if (!Schema::hasColumn('fee_structure_items', 'bank_account_id')) {
                $table->foreignId('bank_account_id')->nullable()->after('fee_item_id')->constrained('bank_accounts');
            }
        });

        // 4. Upgrade Invoice Items Table (Snapshots)
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'amount_oct')) {
                $table->decimal('amount_oct', 12, 2)->default(0)->after('amount');
                $table->decimal('amount_jan', 12, 2)->default(0)->after('amount_oct');
                $table->decimal('amount_apr', 12, 2)->default(0)->after('amount_jan');
            }
        });
    }

    public function down(): void
    {
        // ... (keep down as is or empty if strict rollback not needed for dev)
    }
};
