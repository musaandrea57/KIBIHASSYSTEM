<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('label')->nullable();
            $table->string('type')->default('text'); // text, boolean, select
            $table->string('options')->nullable(); // JSON for select options
            $table->timestamps();
        });

        // Seed default settings
        DB::table('sms_settings')->insert([
            [
                'key' => 'provider', 
                'value' => 'simulated', 
                'label' => 'SMS Provider', 
                'type' => 'select', 
                'options' => json_encode(['simulated' => 'Simulated', 'nextsms' => 'NextSMS', 'beem' => 'Beem'])
            ],
            [
                'key' => 'sender_id', 
                'value' => 'KIBIHAS', 
                'label' => 'Sender ID', 
                'type' => 'text', 
                'options' => null
            ],
            [
                'key' => 'is_enabled', 
                'value' => '1', 
                'label' => 'Enable SMS Sending', 
                'type' => 'boolean', 
                'options' => null
            ],
            [
                'key' => 'daily_limit',
                'value' => '1000',
                'label' => 'Daily Limit',
                'type' => 'number',
                'options' => null
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_settings');
    }
};
