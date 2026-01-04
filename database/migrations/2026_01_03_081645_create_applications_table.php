<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // For applicant login if they create account
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->foreignId('program_id')->constrained();
            $table->string('status')->default('submitted'); // submitted, under_review, approved, rejected
            $table->json('biodata')->nullable();
            $table->json('education_background')->nullable();
            $table->json('documents')->nullable(); // Paths to uploaded files
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
