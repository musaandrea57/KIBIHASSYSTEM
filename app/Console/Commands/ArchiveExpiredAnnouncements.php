<?php

namespace App\Console\Commands;

use App\Services\AnnouncementService;
use Illuminate\Console\Command;

class ArchiveExpiredAnnouncements extends Command
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
    protected $description = 'Archive expired announcements automatically';

    /**
     * Execute the console command.
     */
    public function handle(AnnouncementService $service)
    {
        $this->info('Checking for expired announcements...');
        
        $count = $service->archiveExpired();
        
        if ($count > 0) {
            $this->info("Successfully archived {$count} expired announcements.");
        } else {
            $this->info('No expired announcements found.');
        }
    }
}
