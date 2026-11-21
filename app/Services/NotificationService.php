<?php

namespace App\Services;

use App\Models\Saisine;
use App\Models\User;
use App\Models\WorkflowStage;
use App\Notifications\CompletionRequestNotification;
use App\Notifications\ContributorAddedNotification;
use App\Notifications\CriticalUrgencyNotification;
use App\Notifications\DelayAlertNotification;
use App\Notifications\StageValidationNotification;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Notify users with required roles for the new stage.
     * 
     * Requirements: 8.1-8.6
     */
    public function notifyStageValidators(Saisine $saisine, WorkflowStage $stage): void
    {
        $requiredRoles = $stage->getRequiredRoles();
        
        if (empty($requiredRoles)) {
            return;
        }

        // Get all users with any of the required roles
        $validators = User::whereHas('roles', function ($query) use ($requiredRoles) {
            $query->whereIn('name', $requiredRoles);
        })->get();

        // Send notification to each validator
        foreach ($validators as $validator) {
            $validator->notify(new StageValidationNotification($saisine, $stage));
        }
    }

    /**
     * Notify responsible user of completion request.
     * 
     * Requirements: 8.1-8.6
     */
    public function notifyCompletionRequest(Saisine $saisine, User $requestor, string $reason): void
    {
        // Notify the creator of the saisine
        $saisine->creator->notify(new CompletionRequestNotification($saisine, $requestor, $reason));

        // Also notify validators at the current (previous) stage
        $this->notifyStageValidators($saisine, $saisine->currentStage);
    }

    /**
     * Notify newly added contributor.
     * 
     * Requirements: 8.1-8.6
     */
    public function notifyContributorAdded(Saisine $saisine, User $contributor): void
    {
        $contributor->notify(new ContributorAddedNotification($saisine));
    }

    /**
     * Alert validators of delayed requests.
     * 
     * Requirements: 8.1-8.6
     */
    public function notifyDelayAlert(Saisine $saisine): void
    {
        $currentStage = $saisine->currentStage;
        
        if (!$currentStage) {
            return;
        }

        $requiredRoles = $currentStage->getRequiredRoles();
        
        if (empty($requiredRoles)) {
            return;
        }

        // Get all users with any of the required roles
        $validators = User::whereHas('roles', function ($query) use ($requiredRoles) {
            $query->whereIn('name', $requiredRoles);
        })->get();

        // Send delay alert to each validator
        foreach ($validators as $validator) {
            $validator->notify(new DelayAlertNotification($saisine));
        }
    }

    /**
     * Immediate notification for critical requests.
     * 
     * Requirements: 8.1-8.6
     */
    public function notifyCriticalUrgency(Saisine $saisine): void
    {
        $currentStage = $saisine->currentStage;
        
        if (!$currentStage) {
            return;
        }

        $requiredRoles = $currentStage->getRequiredRoles();
        
        if (empty($requiredRoles)) {
            return;
        }

        // Get all users with any of the required roles
        $validators = User::whereHas('roles', function ($query) use ($requiredRoles) {
            $query->whereIn('name', $requiredRoles);
        })->get();

        // Send critical urgency notification to each validator
        foreach ($validators as $validator) {
            $validator->notify(new CriticalUrgencyNotification($saisine));
        }
    }
}
