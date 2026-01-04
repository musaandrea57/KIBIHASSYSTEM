<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('provider'); // NECTA, NACTE, TCU
            $table->string('action'); // verify_candidate, register_student
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->string('status'); // success, failed
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_logs');
    }
};
