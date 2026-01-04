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
        Schema::create('module_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained();
            $table->foreignId('academic_year_id')->constrained();
            $table->integer('nta_level'); // 4, 5, 6
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['module_id', 'academic_year_id', 'semester_id', 'nta_level'], 'unique_module_offering');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_offerings');
    }
};
