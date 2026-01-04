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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained();
            $table->foreignId('semester_id')->constrained();
            $table->decimal('score', 5, 2); // e.g., 85.50
            $table->string('grade'); // A, B, C, D, F
            $table->decimal('grade_points', 3, 1)->nullable(); // e.g. 4.0
            $table->string('remarks')->nullable(); // Pass, Fail, Supp
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->unique(['student_id', 'module_id', 'academic_year_id', 'semester_id'], 'unique_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
