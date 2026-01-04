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
        Schema::table('students', function (Blueprint $table) {
            $table->string('nationality')->nullable();
            $table->string('nin')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('marital_status')->nullable();
            
            $table->string('permanent_address')->nullable();
            $table->string('current_address')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            
            $table->string('nhif_card_number')->nullable();
            $table->boolean('has_disability')->default(false);
            $table->text('disability_details')->nullable();
            $table->text('medical_conditions')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'nationality', 'nin', 'passport_number', 'marital_status',
                'permanent_address', 'current_address', 'region', 'country',
                'nhif_card_number', 'has_disability', 'disability_details', 'medical_conditions'
            ]);
        });
    }
};
