<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('registration_number')->unique();
            $table->string('nactvet_registration_number')->nullable();
            $table->foreignId('program_id')->constrained();
            $table->integer('current_nta_level')->default(4);
            $table->foreignId('current_academic_year_id')->nullable()->constrained('academic_years');
            $table->foreignId('current_semester_id')->nullable()->constrained('semesters');
            $table->string('status')->default('active'); // active, postponed, suspended, discontinued, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
