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
        // Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Staff Profiles
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->unique()->cascadeOnDelete();
            $table->string('staff_id')->unique();
            $table->foreignId('department_id')->constrained();
            $table->string('phone')->nullable();
            $table->string('gender')->nullable(); // 'M', 'F'
            $table->string('status')->default('active'); // active, inactive
            $table->string('photo_path')->nullable();
            $table->date('employed_at')->nullable();
            $table->timestamps();
        });

        // Module Assignments
        // First drop the old table if it exists (from previous migration that might be incompatible)
        Schema::dropIfExists('module_assignments');

        Schema::create('module_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_offering_id')->constrained('module_offerings')->cascadeOnDelete();
            $table->foreignId('teacher_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by_user_id')->constrained('users');
            $table->timestamp('assigned_at')->useCurrent();
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_assignments');
        Schema::dropIfExists('staff_profiles');
        Schema::dropIfExists('departments');
    }
};
