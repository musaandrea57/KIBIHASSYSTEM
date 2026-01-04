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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('invoice_id')->nullable()->constrained(); // Nullable if payment is not yet allocated
            $table->string('transaction_id')->unique(); // Receipt Number or Bank Ref
            $table->decimal('amount', 12, 2);
            $table->string('method'); // cash, bank, mobile_money, card
            $table->timestamp('paid_at');
            $table->foreignId('recorded_by')->constrained('users'); // Staff who recorded/verified
            $table->string('proof_document')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
