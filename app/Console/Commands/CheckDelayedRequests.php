<?php

namespace App\Console\Commands;

use App\Models\Saisine;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class CheckDelayedRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saisine:check-delays';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for delayed requests and send notifications';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->info('Checking for delayed requests...');

        // Get all saisines that are delayed
        $delayedSaisines = Saisine::with(['currentStage'])
            ->whereNotNull('stage_entered_at')
            ->get()
            ->filter(function ($saisine) {
                return $saisine->isDelayed();
            });

        if ($delayedSaisines->isEmpty()) {
            $this->info('No delayed requests found.');
            return 0;
        }

        $count = 0;
        foreach ($delayedSaisines as $saisine) {
            $notificationService->notifyDelayAlert($saisine);
            $count++;
            
            $this->line("Sent delay alert for: {$saisine->reference_number} - {$saisine->title}");
        }

        $this->info("Sent {$count} delay alert notification(s).");
        
        return 0;
    }
}
