<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Announcement;

class ArchiveAnnouncements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'announcements:archive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Archive announcements that have passed their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = Announcement::where('is_published', true)
            ->where('is_archived', false)
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->update(['is_archived' => true]);

        if ($count > 0) {
            $this->info("Archived {$count} expired announcements.");
        } else {
            $this->info("No expired announcements found to archive.");
        }
    }
}
