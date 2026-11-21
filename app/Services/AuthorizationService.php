<?php

namespace App\Services;

use App\Models\Saisine;
use App\Models\User;

class AuthorizationService
{
    /**
     * Check if user can view the saisine.
     * 
     * Requirements: 1.4, 5.1-5.6
     */
    public function canViewSaisine(User $user, Saisine $saisine): bool
    {
        // Creator can always view
        if ($saisine->creator_id === $user->id) {
            return true;
        }

        // Contributors can view
        if ($saisine->contributors()->where('users.id', $user->id)->exists()) {
            return true;
        }

        // Observers can view
        if ($saisine->observers()->where('users.id', $user->id)->exists()) {
            return true;
        }

        // Users who have validated or requested completion can view
        if ($saisine->validations()->where('validator_id', $user->id)->exists()) {
            return true;
        }

        // Users with roles required for current stage can view
        if ($saisine->currentStage) {
            $requiredRoles = $saisine->currentStage->getRequiredRoles();
            if ($user->hasAnyRole($requiredRoles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can edit the saisine.
     * 
     * Requirements: 1.4, 5.1-5.6
     */
    public function canEditSaisine(User $user, Saisine $saisine): bool
    {
        // Creator can edit
        if ($saisine->creator_id === $user->id) {
            return true;
        }

        // Contributors can edit
        if ($saisine->contributors()->where('users.id', $user->id)->exists()) {
            return true;
        }

        // Users with roles required for current stage can edit
        if ($saisine->currentStage) {
            $requiredRoles = $saisine->currentStage->getRequiredRoles();
            if ($user->hasAnyRole($requiredRoles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can validate the saisine at current stage.
     * 
     * Requirements: 1.4, 5.1-5.6
     */
    public function canValidateSaisine(User $user, Saisine $saisine): bool
    {
        if (!$saisine->currentStage) {
            return false;
        }

        $requiredRoles = $saisine->currentStage->getRequiredRoles();
        
        return $user->hasAnyRole($requiredRoles);
    }

    /**
     * Check if user can add contributors to the saisine.
     * 
     * Requirements: 1.4, 5.1-5.6
     */
    public function canAddContributors(User $user, Saisine $saisine): bool
    {
        // Creator can add contributors
        if ($saisine->creator_id === $user->id) {
            return true;
        }

        // Users with validator roles for current stage can add contributors
        if ($saisine->currentStage) {
            $requiredRoles = $saisine->currentStage->getRequiredRoles();
            if ($user->hasAnyRole($requiredRoles)) {
                return true;
            }
        }

        return false;
    }
}
