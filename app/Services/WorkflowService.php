<?php

namespace App\Services;

use App\Models\Saisine;
use App\Models\User;
use App\Models\Validation;
use App\Models\WorkflowStage;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
    public function __construct(
        protected NotificationService $notificationService,
        protected VersioningService $versioningService
    ) {}

    /**
     * Validate and advance the saisine to the next stage.
     * 
     * Requirements: 3.1-3.6, 4.1-4.6
     */
    public function validateAndAdvance(Saisine $saisine, User $validator, string $action, string $comment): void
    {
        // Check authorization
        if (!$this->canUserValidate($saisine, $validator)) {
            throw new \Exception('User is not authorized to validate this saisine at the current stage');
        }

        // Validate action
        $validActions = [
            Validation::ACTION_VALIDATE,
            Validation::ACTION_PARTIAL_VALIDATION,
            Validation::ACTION_TOTAL_VALIDATION,
        ];

        if (!in_array($action, $validActions)) {
            throw new \Exception('Invalid validation action');
        }

        DB::transaction(function () use ($saisine, $validator, $action, $comment) {
            $currentStage = $saisine->currentStage;

            // Create validation record
            Validation::create([
                'saisine_id' => $saisine->id,
                'validator_id' => $validator->id,
                'stage_id' => $currentStage->id,
                'action' => $action,
                'comment' => $comment,
            ]);

            // Advance to next stage
            $nextStage = $this->getNextStage($currentStage);
            
            if (!$nextStage) {
                throw new \Exception('No next stage available');
            }

            $saisine->current_stage_id = $nextStage->id;
            $saisine->stage_entered_at = now();
            $saisine->save();

            // Create version
            $this->versioningService->createVersion($saisine, $validator, [
                'action' => 'stage_advanced',
                'from_stage' => $currentStage->display_name,
                'to_stage' => $nextStage->display_name,
                'validation_action' => $action,
                'comment' => $comment,
            ]);

            // Notify validators at the next stage
            $this->notificationService->notifyStageValidators($saisine, $nextStage);

            // If critical urgency, send immediate notification
            if ($saisine->urgency_level === 'critical') {
                $this->notificationService->notifyCriticalUrgency($saisine);
            }
        });
    }

    /**
     * Request completion and return to previous stage.
     * 
     * Requirements: 3.1-3.6, 4.1-4.6
     */
    public function requestCompletion(Saisine $saisine, User $validator, string $reason): void
    {
        // Check authorization
        if (!$this->canUserValidate($saisine, $validator)) {
            throw new \Exception('User is not authorized to request completion for this saisine');
        }

        // Validate reason is provided
        if (empty(trim($reason))) {
            throw new \Exception('A comment is required when requesting completion');
        }

        DB::transaction(function () use ($saisine, $validator, $reason) {
            $currentStage = $saisine->currentStage;

            // Create validation record
            Validation::create([
                'saisine_id' => $saisine->id,
                'validator_id' => $validator->id,
                'stage_id' => $currentStage->id,
                'action' => Validation::ACTION_REQUEST_COMPLETION,
                'comment' => $reason,
            ]);

            // Return to previous stage
            $previousStage = $this->getPreviousStage($currentStage);
            
            if (!$previousStage) {
                throw new \Exception('No previous stage available');
            }

            $saisine->current_stage_id = $previousStage->id;
            $saisine->stage_entered_at = now();
            $saisine->save();

            // Create version
            $this->versioningService->createVersion($saisine, $validator, [
                'action' => 'completion_requested',
                'from_stage' => $currentStage->display_name,
                'to_stage' => $previousStage->display_name,
                'reason' => $reason,
            ]);

            // Notify responsible user at previous stage
            $this->notificationService->notifyCompletionRequest($saisine, $validator, $reason);
        });
    }

    /**
     * Check if a user can validate the saisine at its current stage.
     * 
     * Requirements: 3.1-3.6, 4.1-4.6
     */
    public function canUserValidate(Saisine $saisine, User $user): bool
    {
        if (!$saisine->currentStage) {
            return false;
        }

        $requiredRoles = $saisine->currentStage->getRequiredRoles();
        
        return $user->hasAnyRole($requiredRoles);
    }

    /**
     * Get the next workflow stage in sequence.
     * 
     * Requirements: 3.1-3.6, 4.1-4.6
     */
    public function getNextStage(WorkflowStage $currentStage): ?WorkflowStage
    {
        return $currentStage->getNextStage();
    }

    /**
     * Get the previous workflow stage in sequence.
     * 
     * Requirements: 3.1-3.6, 4.1-4.6
     */
    public function getPreviousStage(WorkflowStage $currentStage): ?WorkflowStage
    {
        return $currentStage->getPreviousStage();
    }

    /**
     * Reject the saisine and return it to RJG Qualification stage.
     * Can be done from any stage in the workflow.
     */
    public function rejectToRJG(Saisine $saisine, User $validator, string $reason): void
    {
        // Check authorization
        if (!$this->canUserValidate($saisine, $validator)) {
            throw new \Exception('User is not authorized to reject this saisine');
        }

        // Validate reason is provided
        if (empty(trim($reason))) {
            throw new \Exception('A comment is required when rejecting a request');
        }

        DB::transaction(function () use ($saisine, $validator, $reason) {
            $currentStage = $saisine->currentStage;

            // Create validation record with reject action
            Validation::create([
                'saisine_id' => $saisine->id,
                'validator_id' => $validator->id,
                'stage_id' => $currentStage->id,
                'action' => Validation::ACTION_REJECT,
                'comment' => $reason,
            ]);

            // Get RJG Qualification stage
            $rjgStage = WorkflowStage::where('name', WorkflowStage::RJG_QUALIFICATION)->first();
            
            if (!$rjgStage) {
                throw new \Exception('RJG Qualification stage not found');
            }

            $saisine->current_stage_id = $rjgStage->id;
            $saisine->stage_entered_at = now();
            $saisine->save();

            // Create version
            $this->versioningService->createVersion($saisine, $validator, [
                'action' => 'rejected',
                'from_stage' => $currentStage->display_name,
                'to_stage' => $rjgStage->display_name,
                'reason' => $reason,
            ]);

            // Notify RJG validators
            $this->notificationService->notifyStageValidators($saisine, $rjgStage);
        });
    }
}
