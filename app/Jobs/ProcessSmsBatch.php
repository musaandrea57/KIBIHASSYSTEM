<?php

namespace App\Jobs;

use App\Models\SmsBatch;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSmsBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $batch;

    /**
     * Create a new job instance.
     */
    public function __construct(SmsBatch $batch)
    {
        $this->batch = $batch;
    }

    /**
     * Execute the job.
     */
    public function handle(SmsService $smsService): void
    {
        Log::info("Starting processing of SMS Batch ID: {$this->batch->id}");
        
        try {
            $smsService->processBatch($this->batch);
            Log::info("Completed processing of SMS Batch ID: {$this->batch->id}");
        } catch (\Exception $e) {
            Log::error("Failed to process SMS Batch ID: {$this->batch->id}. Error: " . $e->getMessage());
            $this->batch->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage()
            ]);
            // Depending on requirements, we might not want to fail the job if partial success, 
            // but processBatch handles individual failures.
            // If the whole process crashes, we log it.
        }
    }
}
