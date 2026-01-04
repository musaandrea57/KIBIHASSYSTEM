<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Upgrade Messages Table
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'classification')) {
                $table->string('classification')->nullable()->after('type'); // General Notice, Academic Directive, etc.
            }
            if (!Schema::hasColumn('messages', 'channels')) {
                $table->json('channels')->nullable()->after('classification'); // ['system', 'email', 'sms']
            }
            if (!Schema::hasColumn('messages', 'target_audience_filters')) {
                $table->json('target_audience_filters')->nullable()->after('channels'); // Stores filters used to select recipients
            }
        });

        // Upgrade Announcements Table
        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'category')) {
                $table->string('category')->default('General')->after('title'); // Academic, Finance, etc.
            }
            if (!Schema::hasColumn('announcements', 'priority')) {
                $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal')->after('category');
            }
            if (!Schema::hasColumn('announcements', 'audience')) {
                $table->json('audience')->nullable()->after('priority'); // Target roles/groups
            }
            if (!Schema::hasColumn('announcements', 'start_date')) {
                $table->timestamp('start_date')->nullable()->after('content');
            }
            if (!Schema::hasColumn('announcements', 'end_date')) {
                $table->timestamp('end_date')->nullable()->after('start_date');
            }
            if (!Schema::hasColumn('announcements', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('id');
            }
        });

        // Make Attachments Polymorphic
        Schema::table('attachments', function (Blueprint $table) {
            $table->unsignedBigInteger('message_id')->nullable()->change(); // Make nullable if not already
            if (!Schema::hasColumn('attachments', 'attachable_type')) {
                $table->string('attachable_type')->nullable()->after('message_id');
            }
            if (!Schema::hasColumn('attachments', 'attachable_id')) {
                $table->unsignedBigInteger('attachable_id')->nullable()->after('attachable_type');
            }
        });

        // Populate polymorphic columns for existing attachments
        DB::statement("UPDATE attachments SET attachable_type = 'App\\\\Models\\\\Message', attachable_id = message_id WHERE message_id IS NOT NULL AND attachable_id IS NULL");

        // Create Announcement Reads Table
        if (!Schema::hasTable('announcement_reads')) {
            Schema::create('announcement_reads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('announcement_id')->constrained('announcements')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->timestamp('read_at');
                $table->timestamps();
                $table->unique(['announcement_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_reads');

        Schema::table('attachments', function (Blueprint $table) {
            $table->dropColumn(['attachable_type', 'attachable_id']);
        });

        Schema::table('announcements', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['created_by', 'category', 'priority', 'audience', 'start_date', 'end_date']);
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['classification', 'channels', 'target_audience_filters']);
        });
    }
};
