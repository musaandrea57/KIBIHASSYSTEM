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
        // Drop existing finance tables if they exist to start fresh with correct schema
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('fee_structure_items');
        Schema::dropIfExists('fee_items'); // This was previously the structure items, now will be master
        Schema::dropIfExists('fee_structures');
        Schema::enableForeignKeyConstraints();

        // 1. Fee Items Master
        Schema::create('fee_items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Tuition Fee, etc.
            $table->string('code')->unique()->nullable(); // TF, REG
            $table->string('category')->default('other'); // tuition, mandatory, other
            $table->string('default_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Fee Structures Header
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs');
            $table->integer('nta_level'); // 4, 5, 6
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->string('name'); // e.g., "Nursing NTA4 Sem1 2025/2026"
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->unique(['program_id', 'nta_level', 'academic_year_id', 'semester_id'], 'fee_struct_unique_context');
        });

        // 3. Fee Structure Lines
        Schema::create('fee_structure_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_structure_id')->constrained('fee_structures')->cascadeOnDelete();
            $table->foreignId('fee_item_id')->constrained('fee_items');
            $table->decimal('amount', 12, 2);
            $table->boolean('is_mandatory')->default(true);
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 4. Invoices Header
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // KIB-INV-YYYY-000001
            $table->foreignId('student_id')->constrained('students');
            // Snapshots
            $table->foreignId('program_id')->constrained('programs'); 
            $table->integer('nta_level');
            
            $table->foreignId('academic_year_id')->constrained('academic_years');
            $table->foreignId('semester_id')->constrained('semesters');
            $table->foreignId('fee_structure_id')->nullable()->constrained('fee_structures');
            
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            
            $table->enum('status', ['unpaid', 'partial', 'paid', 'voided'])->default('unpaid');
            $table->string('currency')->default('TZS');
            
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('total_paid', 12, 2)->default(0);
            $table->decimal('balance', 12, 2)->default(0);
            
            $table->foreignId('created_by')->nullable()->constrained('users');
            
            // Void info
            $table->foreignId('voided_by')->nullable()->constrained('users');
            $table->text('void_reason')->nullable();
            $table->timestamp('voided_at')->nullable();
            
            $table->timestamps();
        });

        // 5. Invoice Lines
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('fee_item_id')->nullable()->constrained('fee_items'); // Nullable if custom item
            $table->string('description')->nullable(); // Snapshot of name
            $table->decimal('amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            // balance_amount is computed as amount - paid_amount
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 6. Payments
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_reference')->unique(); // KIB-PAY-YYYY-000001
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('invoice_id')->constrained('invoices'); // Mandatory per rules
            
            $table->date('payment_date');
            $table->enum('method', ['cash', 'bank', 'mobile_money', 'card', 'cheque']);
            $table->string('transaction_ref')->nullable(); // External Ref
            
            $table->decimal('amount', 12, 2);
            
            $table->foreignId('received_by')->constrained('users'); // Accountant
            
            $table->enum('status', ['posted', 'reversed'])->default('posted');
            
            // Reversal info
            $table->foreignId('reversed_by')->nullable()->constrained('users');
            $table->text('reversed_reason')->nullable();
            $table->timestamp('reversed_at')->nullable();
            
            $table->timestamps();
        });

        // 7. Payment Allocations
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignId('invoice_item_id')->constrained('invoice_items');
            $table->decimal('amount', 12, 2);
            $table->timestamps();
        });

        // 8. Receipts
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique(); // KIB-RCPT-YYYY-000001
            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('invoice_id')->constrained('invoices');
            
            $table->timestamp('issued_at');
            $table->foreignId('issued_by')->constrained('users');
            $table->string('pdf_path')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('fee_structure_items');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_items');
        Schema::enableForeignKeyConstraints();
    }
};
